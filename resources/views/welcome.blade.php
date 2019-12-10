@extends('layouts.app')
@section('title')
    僕の家計簿
@endsection
@section('css')
    <link href="{{url('css/base.css')}}" rel="stylesheet" type="text/css">
@endsection
@section('content')
    <div class="left-margin">
        <h1>〇使用用途</h1>
        <p>
            ご自身の支出について、変動費・固定費の計12科目でゆるく管理することを目的としています。<br>
            使ってしまった金額をゆるく管理するために、最低限の機能しか設定していません。<br>
            金遣いが荒くなっていないか確認する程度で使ってください。
        </p>
        <h1>〇機能紹介</h1>
        <ul>
            <li>月次予算データ登録</li>
            <p>変動費・固定費の計12科目について、月ごとの予算額を設定できます。</p>
            <li>日次支払データ要録</li>
            <p>変動費・固定費の計12科目について、日ごとの支払額を登録できます。</p>
            <li>月次予算・支払データ比較</li>
            <p>設定した月間の予算と実際に一か月で支払いをした金額について、乖離を確認できます。</p>
            <li>月間支払データ推移確認</li>
            <p>実際に支払いをした金額について、月ごとの金額推移を確認できます。</p>
            <li>日次支払データ確認</li>
            <p>実際に支払いをした金額について、内容を確認できます。</p>
        </ul>
        <h1>〇使用画面イメージ</h1>
        <h2>支払データ登録画面</h2>
        <img class="example-image" src="{{url('image/payment.png')}}" alt="支払データ登録画面">
        <h2>月次予算・支払データ比較画面</h2>
        <img class="example-image" src="{{url('image/compare_payment_pie.png')}}" alt="比較参考">
        <img class="example-image" src="{{url('image/compare_payment_bar.png')}}" alt="比較参考">
        <h2>月間支払データ推移画面</h2>
        <img class="example-image" src="{{url('image/analize_payment.png')}}" alt="支払参考">
        <p class="top-margin">ご興味のある方は、画面右上の登録画面よりユーザ登録・ログインをして頂き、ご利用ください。</p>
    </div>
@endsection
