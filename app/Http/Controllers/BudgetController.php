<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
// ユーザ認証のため、使用
use Illuminate\Support\Facades\Auth;

// 自作クラスの呼び出し
use App\Library\Account_project;


// モデルの宣言
use App\Budget;
use App\Genre;

// バリデーションを実施する部分を実装
use App\Http\Requests\Budget_monthRequest;
use App\Http\Requests\BudgetRequest;

class BudgetController extends Controller
{
    public function index(Request $request) {
        // ユーザ特定のため、ユーザIDを取得
        // ログイン確認はルーティングに記載（Authミドルウェアを使用）
        $user_id = Auth::id();
        // データ表示用のページとしても使うため、変数値を確認
        if (isset($request->selected_date) === FALSE) {
            // アクセス初回のみ選択月に本日日時を使用
            $selected_date = Account_project::get_todays_date();
        } else {
            $selected_date = $request->selected_date;
        }
        $selected_date = (int) $selected_date;
        // 月の値を取得
        $genre_db = new Genre();
        // 変動費
        $variable_budget = $genre_db
                            ->leftjoin('budgets', 'genres.genre_id', '=', 'budgets.genre_id')
                            ->where([
                                      ['budgets.user_id',      '=', $user_id],
                                      ['budgets.target_month', '=', $selected_date],
                                      ['genres.status',        '=', Account_project::GENRE_STATUS_VARIABLE_COSTS]
                                    ])
                            ->get();
        // 固定費
        $fixed_budget = $genre_db
                          ->leftjoin('budgets', 'genres.genre_id', '=', 'budgets.genre_id')
                          ->where([
                                    ['budgets.user_id',      '=', $user_id],
                                    ['budgets.target_month', '=', $selected_date],
                                    ['genres.status',        '=', Account_project::GENRE_STATUS_FIXED_COSTS]
                                  ])
                          ->get();
        // 入力画面側で新規登録or更新の情報をformで送信する情報に付与するためのフラグを設定
        // すでにデータが存在する場合
        $flag = Account_project::DATA_EXIST;
        if (isset($variable_budget[0]->budget) === FALSE) {
            $flag = Account_project::DATA_NOT_EXIST;
            $variable_budget = $genre_db->where([['status', '=', Account_project::GENRE_STATUS_VARIABLE_COSTS]])->get();
            $fixed_budget    = $genre_db->where([['status', '=', Account_project::GENRE_STATUS_FIXED_COSTS   ]])->get();
        }

        return view('budget.budget', compact('selected_date', 'variable_budget', 'fixed_budget', 'flag'));
    }




    public function month(Budget_monthRequest $request) {
      // ログイン認証はミドルウェアに投げてある。
      // バリデーションエラー時、同一のviewにリダイレクトされてしまう。
      // postで受けるこのコントローラで画面表示をすると、戻されたviewから再度送信をし、バリデーションエラーがあった場合、エラー表示が出てしまうので、getで受ける画面表示用コントローラに入力値を渡すようにしている。
      $selected_date = $request->selected_date;
      $year = Account_project::get_year($selected_date);
      $month = Account_project::get_month($selected_date);

      // 日付を選択されたことをお知らせするために、
      // $messageをrequestへ保存
      $message = $year . '年' . $month . '月の予算編集ができます。';
      $request->session()->flash('message', $message);

      return redirect()->action(
          'BudgetController@index', ['selected_date' => $selected_date]
      );
    }



    public function budget(BudgetRequest $request) {
        // budgetの更新、もしくは新規登録を実施する
        // user_idの取得
        $user_id = Auth::id();
        // 予算データを連想配列として取得
        $budget = $request->budget;
        // 予算入力月を取得
        $selected_date = $request->selected_date;
        $selected_date = (int) $selected_date;
        // 表示をするmessage
        $message = '';
        // データを受けっとったときの処理
        // message用に件数を取得
        $update_num = 0;
        $create_num = 0;
        // updateの処理を実行
        // foreach文で予算の配列データを順番にupdate
        foreach($budget as $key=>$value) {
            // updateされたときにtrueになるフラグ
            $update_flag = false;
            // createされたときにtrueになるフラグ
            $create_flag = false;
            // update処理
            $budget_db = new Budget();
            // データがあるときは更新、ないときは作成する。
            $update_return = $budget_db
                            ->updateOrCreate(
                                [
                                    'user_id'      => $user_id,
                                    'genre_id'     => $key,
                                    'target_month' => $selected_date
                                ],
                                [
                                    'user_id'      => $user_id,
                                    'genre_id'     => $key,
                                    'budget'       => $value,
                                    'target_month' => $selected_date
                                ]
                              );
            // update,createのフラグ回収
            $update_flag = $update_return->wasChanged();
            $create_flag = $update_return->wasRecentlyCreated;

            $update_num = Account_project::increase_only_true($update_flag, $update_num);
            $create_num = Account_project::increase_only_true($create_flag, $create_num);
        }
        // メッセージの内容を作成.
        // カラムの作成のみ実施され、更新がなかった時のmessage
        $create_message = '予算を登録しました。';
        // カラムの更新が実施されたときのmesssage（データ作成があったときでも更新があればこちら）
        // ※0円予算についてもデータが作成されるため、userが混乱する恐れがあるので
        $update_message = $update_num . '件の予算を更新しました。';
        // $messageにユーザへ提示するmessageを格納
        $message = Account_project::decide_message_by_num($update_num, $create_num, $update_message, $create_message);
        // $messageをrequestへ保存
        $request->session()->flash('message', $message);
        return redirect()->action(
            'BudgetController@index', ['selected_date' => $selected_date]
        );
    }
}
