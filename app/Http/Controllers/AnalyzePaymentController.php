<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
// ユーザ認証のため、Authクラスをuse
use Illuminate\Support\Facades\Auth;
// 自作クラスの呼び出し
use App\Library\Account_project;

// 使用するモデルの宣言
use App\Payment;
use App\Genre;

// バリデーション用のリクエストクラスを定義
use App\Http\Requests\Search_PaymentRequest;

class AnalyzePaymentController extends Controller
{
    public function page_view(Request $request) {
        // ユーザのログイン確認はAuthミドルウェアに委託
        $user_id = Auth::id();
        // getで変数が渡されてるか？
        if (isset($request->first_date) === FALSE) {
            // なかった場合一年前を設定
            $first_date = Account_project::get_one_year_ago();
        } else {
            $first_date = $request->first_date;
        }
        if (isset($request->last_date) === FALSE) {
            $last_date = Account_project::get_todays_date();
        } else {
            $last_date = $request->last_date;
        }
        // genre_idが渡されているか?
        // 渡されていないとき（ユーザがジャンルを選択していないとき）view側ですべてのジャンルをチェック状態にしておきたいので、フラグを立てておく
        $selected_flag = true;
        if (isset($request->genre_id) === FALSE) {
            $checked_genre = [];
            $selected_flag = false;
        } else {
            $checked_genre = $request->genre_id;
            // genre_idは，区切りで渡されているので、配列化
            $checked_genre = Account_project::explode_data_by_commma($checked_genre);
        }
        // 渡されたデータがあるなしにかかわらず、すべてのジャンル名を取得する
        $genre_db = new Genre();
        $genre_name = $genre_db->select('genre_id', 'genre_name', 'status')->get();
        // formに必要な月、年をセット
        $tmp_year  = [];
        $tmp_month = [];
        // 始まりの年は2018年から
        $now_year  = date('Y');
        $now_year  = (int) $now_year;
        $tmp_year  = Account_project::get_years_for_selected_by_users(2018, $now_year + 10);
        $tmp_month = Account_project::get_months_for_selected_by_users();
        // viewのform用に日付データを分解
        $first_year  = Account_project::get_year($first_date);
        $first_month = Account_project::get_month($first_date);
        $last_year   = Account_project::get_year($last_date);
        $last_month  = Account_project::get_month($last_date);

        // チェックをされたgenre_idの支払いデータを取得する配列
        $tmp_payment_data = [];
        // チェック済みのジャンル名を取得する配列
        $tmp_genre_name     = [];
        $checked_genre_name = [];
        // チェックされたジャンルがあるとき、それぞれのジャンルについて、名前と支払いデータを取得
        if (count($checked_genre) !== 0) {
            $payment_db = new Payment();
            foreach($checked_genre as $value) {
                $value = (int) $value;
                $tmp_genre_name[] = $genre_db->select('genre_name')
                                  ->where([
                                      ['genre_id', '=', $value]
                                    ])
                                  ->get();
                // 月ごとの支払いデータを取得する
                $tmp_payment_data[] = $payment_db
                                  ->selectRaw("genre_id, user_id, sum(payment) as payment, DATE_FORMAT(target_month, '%Y%m') as selected_date")
                                  ->where([
                                      ['genre_id', '=', $value],
                                      ['user_id', '=', $user_id],
                                      ['flag', '=', Account_project::TO_DESIDE_USE_PAYMENT_FLAG],
                                  ])
                                  ->whereRaw("DATE_FORMAT(target_month, '%Y%m') BETWEEN ? AND ?", [$first_date, $last_date])
                                  ->groupBy('selected_date')
                                  ->get();
            }

            foreach($tmp_genre_name as $tmp_value) {
                foreach($tmp_value as $value) {
                    $checked_genre_name[] = $value->genre_name;
                }
            }

            // 支払データについては、すべてのジャンルについて、月ごとにpayment = 0円で整地。
            $payment_data = [];
            foreach($checked_genre as $key => $value) {
                for ($i = (int) $first_date; $i <= (int) $last_date; $i++) {
                    $tmp_date     = (string) $i;
                    $payment_date = Account_project::get_date_explode_slash($tmp_date);
                    $payment_data[$key][] = ['genre_id' => $value, 'payment' => 0, 'selected_date' => $payment_date, 'genre_name' => $checked_genre_name[$key]];
                    // 月が12月のとき、次の年に移行させる
                    $i = Account_project::change_next_year($i);
                }
            }
            // データベースより値を取得できている部分については、先ほど０円で整地した配列の、対応部分をデータベースからの取得値と変換。
            $i = 0;
            foreach ($tmp_payment_data as $tmp_value) {
                foreach($tmp_value as $value) {
                    if (isset($value->selected_date) === TRUE) {
                        $selected_year  = Account_project::get_year($value->selected_date);
                        $selected_month = Account_project::get_month($value->selected_date);
                        $selected_year  = (int) $selected_year;
                        $selected_month = (int) $selected_month;
                        // データが格納されている場所はどこか特定
                        $inp_year  = (int) $first_year;
                        $inp_month = (int) $first_month;
                        if ($inp_year === $selected_year) {
                            $h = $selected_month - $inp_month;
                        } else {
                            $n = $selected_year - $inp_year;
                            $h = $selected_month + ((12 * $n) - $inp_month);
                        }
                        $payment_data[$i][$h] = ['genre_id' => $value->genre_id, 'payment' => (int) $value->payment, 'selected_date' => Account_project::get_date_explode_slash($value->selected_date), 'genre_name' => $checked_genre_name[$i]];
                    }
                }
                $i++;
            }
        } else {
            // ユーザからジャンルの指定がなかった場合は、空の配列にする
            $payment_data = [];
        }
        // どのジャンルがチェック済みかを分かるように、view渡すため、チェック済み→１、チェックされていない→０で配列化
        $checked_status = [];
        foreach ($genre_name as $value) {
            $db_genre_id = $value->genre_id;
            $exit_flag = true;
            if (count($checked_genre) !== 0) {
                foreach($checked_genre as $key => $tmp_value) {
                    if ((int) $tmp_value === $db_genre_id) {
                        $checked_status[] = Account_project::CHEAKED_STATUS;
                        $exit_flag = false;
                        unset($checked_genre[$key]);
                        break;
                    }
                }
            }
            if ($exit_flag) {
                $checked_status[] = Account_project::NOT_CHEAKED_STATUS;
            } else {
                if (count($checked_genre) !== 0) {
                    $checked_genre = array_values($checked_genre);
                }
            }
        }
        // 画面へ変数を渡し、表示
        return view('analyze_payment.analyze_payment', compact('first_year', 'first_month', 'last_year', 'last_month', 'tmp_year', 'tmp_month', 'payment_data', 'genre_name', 'checked_status', 'selected_flag'));
    }

    // 選択された期間とジャンルを取得し、渡すだけの関数
    public function search_payment(Search_PaymentRequest $request) {
        $first_year  = $request->first_year;
        $first_month = $request->first_month;
        $last_year   = $request->last_year;
        $last_month  = $request->last_month;
        $search_id   = $request->search_id;
        // page_viewコントローラにわたす値を作成
        $checked_genre = '';
        $first_date = $first_year . $first_month;
        $last_date  = $last_year . $last_month;
        // search_idをカンマ区切りの文字列に変換
        $checked_genre = Account_project::change_comma_string($search_id);
        $to_page_view_controller = [$first_date, $last_date, $checked_genre];
        return redirect()->action(
              'AnalyzePaymentController@page_view', $to_page_view_controller
          );
    }

}
