<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

// ユーザ認証のため、使用
use Illuminate\Support\Facades\Auth;

// 自作クラスの呼び出し
use App\Library\Account_project;
// モデルの宣言
use App\Payment;
use App\Genre;

// バリデーション実施用のリクエストクラスを定義
use App\Http\Requests\Variable_PaymentRequest;
use App\Http\Requests\Fixed_PaymentRequest;
use App\Http\Requests\Fixed_MonthRequest;


class PaymentController extends Controller
{
    public function variable_index(Request $request) {
        // ここは画面表示用
        // ユーザ特定のため、ユーザIDを取得
        // ログイン確認はルーティングに記載（Authミドルウェアを使用）
        $user_id = Auth::id();
        // 基本的に表示は当日日付で表示
        $now_year  = date('Y');
        $int_year  = (int) $now_year;
        $now_month = date('m');
        // htmlへ渡す用に、年月の格納用配列を用意
        $tmp_year  = array();
        $last_year = $int_year - 1;
        $tmp_month = array();
        // 年については昨年から、先の１０年間
        $tmp_year  = Account_project::get_years_for_selected_by_users($last_year, $last_year + 11);
        $tmp_month = Account_project::get_months_for_selected_by_users();

        // ジャンルを取得
        $genre_db = new Genre;
        $variable_payment = $genre_db
                            ->where([
                                ['status', '=' , Account_project::GENRE_STATUS_VARIABLE_COSTS]
                            ])
                            ->get();

        // 画面へ変数を渡し画面を表示
        return view('payment.variable_payment', compact('tmp_year', 'tmp_month', 'now_year', 'now_month', 'variable_payment'));
    }

    public function fixed_index(Request $request) {
        // ここは画面表示用
        // ユーザ特定のため、ユーザIDを取得
        // ログイン確認はルーティングに記載（Authミドルウェアを使用）
        $user_id = Auth::id();
        // 日付データが入力されてきた場合は、そちらを取得
        if (isset($request->selected_year) === FALSE) {
            $selected_year  = date('Y');
            $selected_month = date('m');
        } else {
            $selected_year  = $request->selected_year;
            $selected_month = $request->selected_month;
        }
        // 基本的に表示は当日日付で表示
        $now_year = date('Y');
        $now_year = (int) $now_year;
        // htmlへ渡す用に、年月の格納用配列を用意
        $tmp_year  = array();
        $last_year = $now_year - 1;
        $tmp_month = array();
        // 年については昨年から、先の１０年間
        $tmp_year  = Account_project::get_years_for_selected_by_users($last_year, $last_year + 11);
        $tmp_month = Account_project::get_months_for_selected_by_users();

        // 固定費のジャンルを取得。さらに固定費については数値があればそちらも併せて取得
        $genre_db = new Genre;
        $fixed_payment       = $genre_db
                              ->join('payments', 'genres.genre_id', '=', 'payments.genre_id')
                              ->where([
                                  ['payments.user_id', '=', $user_id],
                                  ['genres.status', '=', Account_project::GENRE_STATUS_FIXED_COSTS]
                              ])
                              ->whereRaw("DATE_FORMAT(payments.target_month, '%Y%m') = ?" , [$selected_year . $selected_month])
                              ->get();
        $fixed_payment_genre = $genre_db
                              ->where([
                                  ['genres.status', '=', Account_project::GENRE_STATUS_FIXED_COSTS]
                              ])
                              ->get();

        // データが存在するかでフラグを作成。存在する→1、存在しない→2
        $fixed_flag = Account_project::DATA_EXIST;
        if (isset($fixed_payment[0]->payment) === FALSE) {
            $fixed_flag = Account_project::DATA_NOT_EXIST;
        }

        // 画面へ変数を渡し画面を表示
        return view('payment.fixed_payment', compact('tmp_year', 'tmp_month', 'selected_year', 'selected_month', 'fixed_flag', 'fixed_payment', 'fixed_payment_genre'));
    }

    public function variable_payment(Variable_PaymentRequest $request) {
        // 必要情報を取得
        $user_id        = Auth::id();
        $selected_year  = $request->year;
        $selected_month = $request->month;
        $selected_day   = $request->day;
        $selected_date  = $selected_year . $selected_month . $selected_day;
        $payment        = $request->payment;
        $memo           = $request->memo;
        // foreachでpaymentとメモをそれぞれinsert
        // 処理のために、モデルであるPaymentクラスのインスタンスを取得
        $payment_db   = new Payment();
        $message      = '';
        $message_flag = true;
        $false_num    = 0;
        $create_num   = 0;
        foreach($payment as $key => $value) {
            // paymentとメモのkeyは同じはずなので、$keyで両方を処理
            // 登録はpaymentに金額があったときのみ実施する
            if (!empty($value)) {
                $memo_value = $memo[$key];
                if ($memo_value === NULL) {
                    $memo_value = '';
                }
                // データベースに挿入
                // 支払データは同じデータがある可能性があり、、別ページでユーザのメンテが可能なので、データ存在チェックはしない
                // flagはデータを使用するかどうかのflagであり、別ページでユーザに選択させる。0→非表示、1→表示
                $create_return = $payment_db
                                ->create([
                                      'user_id'      => $user_id,
                                      'genre_id'     => $key,
                                      'payment'      => $value,
                                      'target_month' => $selected_date,
                                      'flag'         => Account_project::TO_DESIDE_USE_PAYMENT_FLAG,
                                      'memo'         => $memo_value
                                  ]);
                if (isset($create_return->payment) === false) {
                    $message_flag = false;
                    $false_num++;
                } else {
                    $create_num++;
                }
            }
        }

        // 表示させるメッセージ
        // データ登録に成功した場合
        $create_message      = $create_num . '件の支払データを登録しました。';
        $fail_create_message = $false_num . '件の支払登録に失敗しました。登録された支払データを確認してください。';

        $message = Account_project::decide_message_by_flag($message_flag, $create_message, $fail_create_message);
        // $messageをrequestへ保存
        $request->session()->flash('message', $message);
        return redirect()->action(
              'PaymentController@variable_index'
          );
    }

    public function fixed_payment(Fixed_PaymentRequest $request) {
      // 必要情報を取得
      $user_id        = Auth::id();
      $selected_year  = $request->year;
      $selected_month = $request->month;
      // 固定費は日にち部分については検討する必要がないため、１日として登録
      $selected_date = $selected_year . $selected_month . '01';
      $payment       = $request->payment;
      $memo          = $request->memo;
      $fixed_flag    = $request->fixed_flag;
      // foreachでpaymentとメモをそれぞれinsert
      // 処理のために、モデルであるPaymentクラスのインスタンスを取得
      $payment_db = new Payment();
      $message = '';
      $message_flag = true;
      $create_num = 0;
      $false_num = 0;

      foreach($payment as $key => $value) {
          // paymentとメモのkeyは同じはずなので、$keyで両方を処理
          // 登録はpaymentに金額があったときのみ実施する
          if (!empty($value)) {
              $memo_value = $memo[$key];
              if ($memo_value === NULL) {
                  $memo_value = '';
              }
              // データベースに挿入
              // 支払データは同じデータがある可能性、別ページでユーザのメンテが可能なので、データ存在チェックはしない
              // flagはデータを使用するかどうかのflagであり、別ページでユーザに選択させる。0→非表示、1→表示
              $create_return = $payment_db
                              ->create([
                                    'user_id'      => $user_id,
                                    'genre_id'     => $key,
                                    'payment'      => $value,
                                    'target_month' => $selected_date,
                                    'flag'         => Account_project::TO_DESIDE_USE_PAYMENT_FLAG,
                                    'memo'         => $memo_value
                                ]);
              if (isset($create_return->payment) === false) {
                  $message_flag = false;
                  $false_num++;
              } else {
                  $create_num++;
              }
          }
      }

      // 表示させるメッセージ
      // データ登録に成功した場合
      $create_message      = $create_num . '件の支払データを登録しました。';
      $fail_create_message = $false_num . '件の支払登録に失敗しました。登録された支払データを確認してください。';
      $message = Account_project::decide_message_by_flag($message_flag, $create_message, $fail_create_message);
      // $messageをrequestへ保存
      $request->session()->flash('message', $message);
      // 画面表示用コントローラへ渡すパラメータ
      $selected_date = array('selected_year' => $selected_year, 'selected_month' => $selected_month);
      return redirect()->action(
            'PaymentController@fixed_index', $selected_date
        );
    }

    public function fixed_payment_month(Fixed_MonthRequest $request) {
      // 必要情報を取得
      $user_id        = Auth::id();
      $selected_year  = $request->year;
      $selected_month = $request->month;
      // 日付を選択されたことをお知らせするために、
      // $messageをrequestへ保存
      $message = $selected_year . '年' . $selected_month . '月の支払データ編集ができます。';
      $request->session()->flash('message', $message);
      $selected_date = array('selected_year' => $selected_year, 'selected_month' => $selected_month);
      return redirect()->action(
            'PaymentController@fixed_index', $selected_date
        );
    }
}
