@extends('layouts.app')
@section('title')
    実績登録
@endsection
@section('css')
    <link href="{{url('css/base.css')}}" rel="stylesheet" type="text/css">
    <link href="https://use.fontawesome.com/releases/v5.0.8/css/all.css" rel="stylesheet">
@endsection
@section('content')
    <div class="left-margin">
        @if (count($errors) > 0)
            <div class="alert alert-danger">
                <p>入力エラーがあります。</P>
                <ul>
                    @foreach ($errors->all() as $error)
                        <li>{{$error}}</li>
                    @endforeach
                </ul>
            </div>
        @endif
        @if (Session::has('message'))
            <div class="message" id="js-message">
                <div class="message-inner">
                    <div class="close-btn" id="js-close-btn"><i class="fas fa-times"></i></div>
                    <div>
                        {{session('message')}}
                    </div>
                </div>
                <div class="black-background" id="js-black-bg"></div>
            </div>
        @endif
        <h2>固定費実績入力</h2>
        <p>最初に支払実績を入力する月を選択し『月を選択』を押してください。</p>
        <!-- 固定費の入力 -->
        <!-- 固定費の月を選択させる -->
        <div>
            <form method="POST" action="{{ route('fixed_payment_month')}}">
                @csrf
                <select name="year">
                    <!-- yearはコントローラ側で配列にする。マージンは一年 -->
                    @if (old('year') === null)
                        @foreach($tmp_year as $value)
                            @if ($value === $selected_year)
                                <option value="{{$value}}" selected>{{$value}}</option>
                            @else
                                <option value="{{$value}}">{{$value}}</option>
                            @endif
                        @endforeach
                    @else
                        @foreach($tmp_year as $value)
                            @if ($value === old('year'))
                                <option value="{{$value}}" selected>{{$value}}</option>
                            @else
                                <option value="{{$value}}">{{$value}}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label for="year">年</label>

                <select name="month">
                    @if (old('month') === null)
                        @foreach($tmp_month as $value)
                            @if ($value === $selected_month)
                                <option value="{{$value}}" selected>{{$value}}</option>
                            @else
                                <option value="{{$value}}">{{$value}}</option>
                            @endif
                        @endforeach
                    @else
                        @foreach($tmp_month as $value)
                            @if ($value === old('month'))
                                <option value="{{$value}}" selected>{{$value}}</option>
                            @else
                                <option value="{{$value}}">{{$value}}</option>
                            @endif
                        @endforeach
                    @endif
                </select>
                <label for="month">月</label>
                <input type="submit" value="月を選択">
            </form>
            <p>
                支払金額を入力し、『支払送信』を押してください。<br>
                ※支払いについてメモを残すことができます。
            </p>
            <form method="POST" action="{{ route('inp_fixed_payment')}}" onsubmit="replace_inp()">
                @csrf
                <div>
                    <!-- コントローラーより情報を取得 -->
                    <ul class="ul-payment-box">
                        <!-- idの名前を付けるように$iを定義-->
                        @php
                            $i = 1;
                        @endphp
                        @foreach ($fixed_payment_genre as $value)
                            <li>
                                <label class="label-name">{{$value->genre_name}}</label>
                                @if (old('payment') !== null)
                                    <input id="numdata{{$i}}" type="text" name="payment[{{$value->genre_id}}]" value="{{old('payment')[$value->genre_id]}}">
                                @else
                                    <input id="numdata{{$i}}" type="text" name="payment[{{$value->genre_id}}]" value="0">
                                @endif
                                <label>メモ</label>
                                @if (old('memo') !== null)
                                    <input type="text" name="memo[{{$value->genre_id}}]" value="{{old('memo')[$value->genre_id]}}">
                                @else
                                    <input type="text" name="memo[{{$value->genre_id}}]" value="">
                                @endif
                                ※10文字以内で入力可能です。
                            </li>
                            @php
                                $i++;
                            @endphp
                        @endforeach
                    </ul>
                    <input type="hidden" name="fixed_flag" value="{{$fixed_flag}}">
                    <input type="hidden" name="year" value="{{$selected_year}}">
                    <input type="hidden" name="month" value="{{$selected_month}}">
                    <input type="submit" value="支払送信">
                </div>
            </form>
        </div>
        <!-- 支払データが存在するときには登録済みデータ一覧を表示 -->
        <div class="top-margin">
                <h2>登録済み固定費データ一覧</h2>
            @if ($fixed_flag === 1)
                @php
                    $i = 1;
                @endphp
                <table class="table-width">
                    <tr class="table-border">
                        <th class="center-align">科目名</th>
                        <th class="center-align">金額</th>
                        <th class="center-align">メモ</th>
                    </tr>
                    @foreach($fixed_payment as $value)
                        <tr class="table-border">
                            <td class="right-align">{{$value->genre_name}}</td>
                            <td class="right-align"><span id="index-num{{$i}}">{{$value->payment}}</span>円</td>
                            <td class="right-align">{{$value->memo}}</td>
                        </tr>
                        @php
                            $i++
                        @endphp
                    @endforeach
                </table>
                <p class="top-margin">
                    ※すでに登録済みの固定費データです。不足している支払があれば、固定費実績入力より入力してください。
                </p>
            @else
                <p>登録されている固定費データはありません。</p>
            @endif
        </div>
    </div>
    @section('js')
        <script src="{{url('js/SeparatorComma_and_ShowMessage.js')}}"></script>
    @endsection
@endsection
