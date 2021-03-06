<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// ユーザ情報を取得するため
use Illuminate\Support\Facades\Auth;

// 自作クラスの呼び出し
use App\Library\Account_project;
// 使用するモデルの定義
use App\Payment;
use App\Genre;
use App\Budget;
// バリデーション用のリクエストクラスを定義
use App\Http\Requests\ComparePaymentRequest;


class ComparePaymentController extends Controller
{
      public function page_vies(Request $request) {
          // ユーザのIDを取得
          $user_id = Auth::id();
          // getで変数が渡されてる?
          if (isset($request->selected_year) === FALSE) {
              $selected_year = date('Y');
          } else {
              $selected_year = $request->selected_year;
          }
          if (isset($request->selected_month) === FALSE) {
              $selected_month = date('m');
          } else {
              $selected_month = $request->selected_month;
          }

          // formに必要な月、年をセット
          $tmp_year = [];
          $tmp_month = [];
          // 始まりの年は2018年から
          $now_year  = date('Y');
          $now_year  = (int) $now_year;
          $tmp_year  = Account_project::get_years_for_selected_by_users(2018, $now_year + 10);
          $tmp_month = Account_project::get_months_for_selected_by_users();

          // すべてのgenre_nameとIDを取得する
          $genre_db = new Genre();
          $all_genres = $genre_db->select('genre_id', 'genre_name', 'status')->get();
          // 各genre_idについて、予算、支払金額を取得
          $budget_db  = new Budget();
          $payment_db = new Payment();
          // データ格納用の配列
          $tmp_budget  = [];
          $tmp_payment = [];
          foreach ($all_genres as $value) {
              $selected_date = $selected_year . $selected_month;
              $genre_id      = (int) $value->genre_id;

              $tmp_budget[] = $budget_db->select('budget')
                          ->where([
                              ['user_id',      '=', $user_id],
                              ['genre_id',     '=', $genre_id],
                              ['target_month', '=', (int) $selected_date]
                          ])->get();

              $tmp_payment[] = $payment_db
                          ->selectRaw("sum(payment) as payment, DATE_FORMAT(target_month, '%Y%m') as selected_date")
                          ->where([
                              ['genre_id', '=', $genre_id],
                              ['user_id',  '=', $user_id],
                              ['flag',     '=', Account_project::TO_DESIDE_USE_PAYMENT_FLAG],
                          ])
                          ->whereRaw("DATE_FORMAT(target_month, '%Y%m') = ?", [$selected_date])
                          ->groupBy('selected_date')
                          ->get();
          }
          // budget,paymentについて、viewに渡すデータのゼロ埋めと、渡すデータの配列を取得
          $budget     = [];
          $payment    = [];
          $genre_name = [];
          foreach($all_genres as $value) {
              $budget[]  = 0;
              $payment[] = 0;
              $genre_name[] = $value->genre_name;
          }
          // データがあるかないかでフラグ制御→なかった場合、view側でグラフ表示しない
          $exit_budget  = FALSE;
          $exit_payment = FALSE;
          // 取得したデータがあるか確認して、あれば配列に格納
          $i = 0;
          foreach ($tmp_budget as $tmp_value) {
              foreach ($tmp_value as $value) {
                  if (isset($value->budget) === TRUE) {
                      $budget[$i] = (int) $value->budget;
                      $exit_budget = TRUE;
                  }
              }
              $i++;
          }
          $i = 0;
          foreach ($tmp_payment as $tmp_value) {
              foreach ($tmp_value as $value) {
                  if (isset($value->payment) === TRUE) {
                      $payment[$i] = (int) $value->payment;
                      $exit_payment = TRUE;
                  }
              }
              $i++;
          }
          // 画面に渡すメッセージ
          $message = '';
          if ($exit_budget === TRUE && $exit_payment === FALSE) {
              $message = '支払データがありません。';
          } elseif ($exit_budget === FALSE && $exit_payment === TRUE) {
              $message = '予算データがありません。';
          } elseif ($exit_budget === FALSE && $exit_payment === FALSE) {
              $message = '予算・支払データがありません。';
          } else {
              $message = true;
          }

          // デバイスが何か判定。PC→true、スマホ→FALSE
          $device = Account_project::decide_device ($request);
          // 画面へ変数を渡し、表示
          return view('compare_payment.compare_payment', compact('selected_date', 'selected_year', 'selected_month', 'budget', 'payment', 'genre_name','exit_budget', 'exit_payment', 'tmp_year', 'tmp_month', 'message', 'device'));
      }

      // ユーザからの入力値を取得し、渡すためだけ関数
      public function search_compare_payment(ComparePaymentRequest $request) {
          $selected_year  = $request->selected_year;
          $selected_month = $request->selected_month;

          $to_page_view_controller = [$selected_year, $selected_month];
          return redirect()->action('ComparePaymentController@page_vies', $to_page_view_controller);
      }
}
