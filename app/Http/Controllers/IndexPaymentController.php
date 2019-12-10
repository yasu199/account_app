<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// ユーザ認証のため、Authクラスをuse
use Illuminate\Support\Facades\Auth;
// 使用するテーブルのモデルを定義
use App\Payment;
use App\Genre;

// バリデーションをする部分
use App\Http\Requests\IndexPaymentDateRequest;
use App\Http\Requests\IndexPaymentSortRequest;
use App\Http\Requests\IndexPaymentUpdateRequest;
use App\Http\Requests\IndexPaymentDeleteRequest;



class IndexPaymentController extends Controller
{
      public function index(Request $request) {
          // 表示のみを実行。sortflagに応じて、日付順orジャンル順or金額順にする
          // ユーザidを取得
          $user_id = Auth::id();
          // getで変数が渡されているか、確認
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
          if (isset($request->sort_flag) === FALSE) {
              $sort_flag = 1;
          } else {
              $sort_flag = (int) $request->sort_flag;
          }
          // 日付の選択用にデータをセット
          // formに必要な月、年をセット
          $tmp_year = array();
          $tmp_month = array();
          // 始まりの年は2018年から
          $now_year = date('Y');
          $now_year = (int) $now_year;
          for ($i = 2018; $i <= ($now_year + 10); $i++) {
              $tmp_year[] = (string) $i;
          }
          for ($i = 1; $i < 13; $i++) {
              $tmp_month[] = sprintf('%02d', $i);
          }

          // すべてのジャンル名とidを取得
          $gerne_db = new Genre();
          $all_genres = $gerne_db->select('genre_id', 'genre_name')->get();
          // ソートフラグによって取得するデータの順番を決める。0→日付順,1→ジャンル別
          $payment_db = new Payment();
          if ($sort_flag === 1) {
                $payment_data = $payment_db
                                ->select('payments.id', 'payments.target_month', 'genres.genre_id', 'genres.genre_name', 'payments.payment', 'payments.flag', 'payments.memo', 'genres.status')
                                ->join('genres', 'payments.genre_id', '=', 'genres.genre_id')
                                ->where([
                                    ['payments.user_id', '=', $user_id],
                                    [DB::raw("DATE_FORMAT(payments.target_month, '%Y%m')"), '=', $selected_year . $selected_month]
                                ])
                                ->orderBy('payments.target_month', 'asc')
                                ->orderBy('payments.genre_id', 'asc')
                                ->get();
          } else {
                $payment_data = $payment_db
                                ->select('payments.id', 'payments.target_month', 'genres.genre_id', 'genres.genre_name', 'payments.payment', 'payments.flag', 'payments.memo', 'genres.status')
                                ->join('genres', 'payments.genre_id', '=', 'genres.genre_id')
                                ->where([
                                    ['payments.user_id', '=', $user_id],
                                    [DB::raw("DATE_FORMAT(payments.target_month, '%Y%m')"), '=', $selected_year . $selected_month]
                                ])
                                ->orderBy('payments.genre_id', 'asc')
                                ->orderBy('payments.target_month', 'asc')
                                ->get();
          }

          return view('index_payment.index_payment', compact('payment_data', 'selected_year', 'selected_month', 'sort_flag', 'all_genres', 'tmp_year', 'tmp_month'));
      }

      public function select_date(IndexPaymentDateRequest $request) {
          // 日にちを選択してもらい、それを渡す
          $selected_year = $request->selected_year;
          $selected_month = $request->selected_month;
          $sort_flag = $request->sort_flag;
          $to_index_controller = array($selected_year, $selected_month, $sort_flag);
          return redirect()->action('IndexPaymentController@index', $to_index_controller);
      }
      public function sort(IndexPaymentSortRequest $request) {
          // ソートコードを受け取り、それを渡す
          $selected_year = $request->selected_year;
          $selected_month = $request->selected_month;
          $sort_flag = $request->sort_flag;
          $to_index_controller = array($selected_year, $selected_month, $sort_flag);
          return redirect()->action('IndexPaymentController@index', $to_index_controller);
      }
      public function update_payment(IndexPaymentUpdateRequest $request) {
          // 更新作業する
          $payment_id     = $request->payment_id;
          $payment_year   = $request->payment_year;
          $payment_month  = $request->payment_month;
          $payment_day    = $request->payment_day;
          $payment_date   = $payment_year . $payment_month . $payment_day;
          $genre_id       = $request->genre;
          $payment        = $request->payment;
          $memo           = $request->memo;
          $flag           = $request->flag;
          $sort_flag      = $request->sort_flag;

          if ($memo === NULL) {
              $memo = '';
          }
          $message = '';
          // paymentを更新するためのインスタンス取得
          $payment_db = new Payment();
          $update_return = $payment_db->where([
                                ['id', '=', (int) $payment_id]
                            ])
                            ->update([
                                'target_month' => $payment_date,
                                'genre_id' => (int) $genre_id,
                                'payment' => (int) $payment,
                                'memo' => $memo,
                                'flag' => (int) $flag
                            ]);
          if ($update_return === 1) {
              $message = 'データを更新しました。';
          } else {
              $message = '対象の支払データが削除されている可能性があります。画面を更新し、データを確認してください。';
          }
          // $messageをrequestへ保存
          $request->session()->flash('message', $message);

          $to_index_controller = array($payment_year, $payment_month, $sort_flag);
          return redirect()->action('IndexPaymentController@index', $to_index_controller);
      }
      public function delete_payment(IndexPaymentDeleteRequest $request) {
          // 削除する
          $payment_id = $request->payment_id;
          $selected_year = $request->selected_year;
          $selected_month = $request->selected_month;
          $sort_flag = $request->sort_flag;
          // paymentを更新するためのインスタンス取得
          $payment_db = new Payment();
          $delete_return = $payment_db->where([
                              ['id', '=', (int) $payment_id]
                          ])
                          ->delete();
          if ($delete_return === 1) {
              $message = 'データを削除しました。';
          } else {
              $message = '対象の支払データはすでに削除されている可能性があります。画面を更新し、データを確認してください。';
          }
          // $messageをrequestへ保存
          $request->session()->flash('message', $message);
          $to_index_controller = array($selected_year, $selected_month, $sort_flag);
          return redirect()->action('IndexPaymentController@index', $to_index_controller);
      }
}
