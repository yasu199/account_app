<?php

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', 'HomeController@index')->name('home');
// 予算入力画面初期ページ
Route::get('budget/{selected_date?}', 'BudgetController@index')->middleware('auth')->name('budget');
// 予算入力画面の月選択後のページ
Route::POST('month', 'BudgetController@month')->middleware('auth')->name('budget_month');
// 予算入力を実行したときのページ
Route::POST('inp_budget', 'BudgetController@budget')->middleware('auth')->name('inp_budget');
// 実績値の入力画面へ
Route::get('variable_payment/{target_date?}', 'PaymentController@variable_index')->middleware('auth')->name('variable_payment');
Route::get('fixed_payment/{selected_year?}/{selected_month?}', 'PaymentController@fixed_index')->middleware('auth')->name('fixed_payment');
// 実績値の入力画面へ
Route::POST('inp_variable_payment', 'PaymentController@variable_payment')->middleware('auth')->name('inp_variable_payment');
// 固定費については金額の入力処理と月の取得処理を分けて処理
Route::POST('inp_fixed_payment', 'PaymentController@fixed_payment')->middleware('auth')->name('inp_fixed_payment');
Route::POST('fixed_payment_month', 'PaymentController@fixed_payment_month')->middleware('auth')->name('fixed_payment_month');
// 支払データを分析するために支払データを折れ線グラフで表示
// 表示用ルート
Route::get('analyze_payment/{first_date?}/{last_date?}/{genre_id?}', 'AnalyzePaymentController@page_view')->middleware('auth')->name('analyze_payment');
// 表示する条件をユーザから受け取り、データをpage_viewに渡すためのコントローラ
Route::post('search_analyze_payment', 'AnalyzePaymentController@search_payment')->middleware('auth')->name('search_analyze_payment');
// 予算データと支払いデータを比較するために、円グラフ、棒グラフで比較させる
// 表示用ルート
Route::get('compare_payment/{selected_year?}/{selected_month?}', 'ComparePaymentController@page_vies')->middleware('auth')->name('compare_payment');
// 表示する月をユーザから受け取り、データを渡す
Route::post('search_compare_payment', 'ComparePaymentController@search_compare_payment')->middleware('auth')->name('search_compare_payment');
// 支払いデータを一覧で表示し、編集、削除、インデックス参照をするためのページ
// 表示用ルート
Route::get('index_payment/{selected_year?}/{selected_month?}/{sort_flag?}', 'IndexPaymentController@index')->middleware('auth')->name('index_payment');
// 表示する月を選択させたときのコントローラ
Route::post('select_date', 'IndexPaymentController@select_date')->middleware('auth')->name('select_date');
// 表示の並び替え用
Route::post('sort_index', 'IndexPaymentController@sort')->middleware('auth')->name('sort');
// データ変更用
Route::post('update_payment', 'IndexPaymentController@update_payment')->middleware('auth')->name('update_payment');
// 削除用
Route::post('delete_payment', 'IndexPaymentController@delete_payment')->middleware('auth')->name('delete_payment');
