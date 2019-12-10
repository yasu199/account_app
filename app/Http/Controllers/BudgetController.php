<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use Illuminate\Support\Facades\DB;
// ユーザ認証のため、使用
use Illuminate\Support\Facades\Auth;


// モデルの宣言
use App\Budget;

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
            $selected_date = date('Ym');
            $selected_date = (int) $selected_date;
        } else {
            $selected_date = $request->selected_date;
            $selected_date = (int) $selected_date;
        }
        // 月の値を取得
        // 変動費
        $variable_budget = DB::table('genres')
                            ->leftjoin('budgets', 'genres.genre_id', '=', 'budgets.genre_id')
                            ->where([
                                      ['budgets.user_id', '=', $user_id],
                                      ['budgets.target_month', '=', $selected_date],
                                      ['genres.status', '=', 1]
                                    ])
                            ->get();
        // 固定費
        $fixed_budget = DB::table('genres')
                          ->leftjoin('budgets', 'genres.genre_id', '=', 'budgets.genre_id')
                          ->where([
                                    ['budgets.user_id', '=', $user_id],
                                    ['budgets.target_month', '=', $selected_date],
                                    ['genres.status', '=', 2]
                                  ])
                          ->get();
        // 入力画面側で新規登録or更新の情報をformで送信する情報に付与するためのフラグを設定
            // すでにデータが存在する場合
        $flag = 1;
        if (isset($variable_budget[0]->budget) === FALSE) {
            $flag = 2;
            $variable_budget = DB::table('genres') ->where([['status', '=', 1]]) ->get();
            $fixed_budget = DB::table('genres')->where([['status', '=', 2]])->get();
        }
        
        return view('budget.budget', compact('selected_date', 'variable_budget', 'fixed_budget', 'flag'));
    }




    public function month(Budget_monthRequest $request) {
      // ログイン認証はミドルウェアに投げてある。
      // バリデーションエラー時、同一のviewにリダイレクトされてしまう。
      // postで受けるこのコントローラで画面表示をすると、戻されたviewから再度送信をし、バリデーションエラーがあった場合、エラー表示が出てしまうので、getで受ける画面表示用コントローラに入力値を渡すようにしている。
      $selected_date = $request->selected_date;
      $year = substr($selected_date, 0, 4);
      $month = substr($selected_date, 4, 2);

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
        // 予算データを連想配列として取得
        $budget = $request->budget;
        // 予算入力月を取得
        $selected_date = $request->selected_date;
        $selected_date = (int) $selected_date;
        // データ登録等の成否を確認するflag
        $message_flag  = false;
        // 表示をするmessage
        $message = '';
        // データを受けっとたのちの処理
        // データがupdateもしくはinsertか判断
        $flag = $request->flag;
        // message用に件数を取得
        $update_num = 0;
        $false_num = 0;
        if ($flag === "1") {
            // updateの処理を実行
            // updateのときはflagをtrueにしてしまう
            $message_flag = true;
            // foreach文で予算の配列データを順番にupdate
            foreach($budget as $key=>$value) {
                // update処理
                // 万が一のカラム漏れがあると困るので確認
                $exit_check = DB::table('budgets')
                              ->where([
                                  ['user_id', '=', Auth::id()],
                                  ['genre_id', '=', $key],
                                  ['target_month', '=', $selected_date]
                              ])
                              ->exists();
                if ($exit_check) {
                    $update_return = DB::table('budgets')
                                    ->where([
                                        ['user_id', '=', Auth::id()],
                                        ['genre_id', '=', $key],
                                        ['target_month', '=', $selected_date]
                                    ])
                                    ->update(['budget' => $value]);
                    if ($update_return === 1) {
                        $update_num++;
                    }
                } else {
                    $create_return = DB::table('budgets')
                                      ->create([
                                          'user_id' => Auth::id(),
                                          'genre_id' => $key,
                                          'budget' => $value,
                                          'target_month' => $selected_date
                                      ]);
                    if (isset($create_return->budget) === true) {
                        // $flag=１で処理が走る固定費は、本来すべての科目にデータが存在するはず
                        // システムの不具合で登録が落ちているだけなので、ここではあくまでも更新件数としてみる
                        // 0円のときはユーザの画面に表示されていた金額と同一になるので更新件数には含めない
                        if ($create_return->budget !== 0) {
                            $update_num++;
                        }
                    }
                }
            }
            $message = $update_num . '件の予算を更新しました。';
        } else {
            $message_flag = true;
            // insertの処理を実行
            $budget_ins = new Budget();

            foreach($budget as $key=>$value) {
              // flag制御で分けてはいるが、一度httpリクエストに不具合が出ると新しいデータが
              // 重複して生成される可能性があるので、いちいち存在チェック
                $exit_check = $budget_ins
                              ->where([
                                  ['user_id', '=', Auth::id()],
                                  ['genre_id', '=', $key],
                                  ['target_month', '=', $selected_date]
                              ])
                              ->exists();
                if ($exit_check) {
                    $update_return = DB::table('budgets')
                                    ->where([
                                        ['user_id', '=', Auth::id()],
                                        ['genre_id', '=', $key],
                                        ['target_month', '=', $selected_date]
                                    ])
                                    ->update(['budget' => $value]);
                } else {
                    // データベースに挿入
                    $create_return = $budget_ins
                                      ->create([
                                          'user_id' => Auth::id(),
                                          'genre_id' => $key,
                                          'budget' => $value,
                                          'target_month' => $selected_date
                                      ]);
                    if (isset($create_return->budget) === false) {
                        // データがないときは失敗した時なので、フラグをfalseへ
                        $message_flag = false;
                        $false_num++;
                    }
                }

            }
            if ($message_flag === false) {
                $message = $false_num . '件の予算登録に失敗しました。予算金額を確認してください。';
            } else {
                $message = '予算を登録しました。';
            }
        }
        // $messageをrequestへ保存
        $request->session()->flash('message', $message);
        return redirect()->action(
            'BudgetController@index', ['selected_date' => $selected_date]
        );
    }
}
