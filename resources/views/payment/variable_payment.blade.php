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
        <h2>変動費実績入力</h2>
        <p>支払実績を入力する日にちを選択してください。</p>
        <!-- 月の選択 月は選択されているようにする -->
        <form method="POST" action="{{route('inp_variable_payment')}}" onsubmit="replace_inp()">
            @csrf
            <select name="year" id="year">
                <!-- yearはコントローラ側で配列にする。マージンは一年 -->
                @if (old('year') === null)
                    @foreach($tmp_year as $value)
                        @if ($value === $now_year)
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

            <select name="month" id="month">
                @if (old('month') === null)
                    @foreach($tmp_month as $value)
                        @if ($value === $now_month)
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

            <select name="day" id="day" data-old-value="{{old('day')}}"></select>
            <label for="day">日</label>

            <!-- 変動費の入力 -->
            <div>
                <p>
                    支払金額を入力し、『支払送信』を押してください。<br>
                    ※支払いについてメモを残すことができます。
                </p>
                <!-- コントローラーより情報を取得 -->
                <ul class="ul-payment-box">
                    <!-- idの名前を付けるように$iを定義-->
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($variable_payment as $value)
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
                            ※10文字以内で入力が可能です。
                        </li>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </ul>
                <input type="submit" value="支払送信">
            </div>
        </form>
    </div>
@endsection
@section('js')
    <script src="{{url('js/SeparatorComma_and_TargetDate.js')}}"></script>
@endsection
