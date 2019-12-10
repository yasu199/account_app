@extends('layouts.app')
@section('title')
    予算支払比較
@endsection
<!-- cssファイルの読み込み -->
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
    <!-- 予算と支払い実績を比較する月を選択させる -->
        <p>支払明細を表示する月を選択してください</p>
        <form method="POST" action="{{ route('select_date')}}">
            @csrf
            <select name="selected_year">
                <!-- yearはコントローラ側で配列にする。マージンは一年 -->
                @if (old('selected_year') === null)
                    @foreach($tmp_year as $value)
                        @if ($value === $selected_year)
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @else
                    @foreach($tmp_year as $value)
                        @if ($value === old('selected_year'))
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            <label for="selected_year">年</label>

            <select name="selected_month">
                @if (old('selected_month') === null)
                    @foreach($tmp_month as $value)
                        @if ($value === $selected_month)
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @else
                    @foreach($tmp_month as $value)
                        @if ($value === old('selected_month'))
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            <label for="selected_month">月</label>
            <input type="hidden" name="sort_flag" value="{{$sort_flag}}">

            <input type="submit" value="表示">
        </form>
        <p>表示順を選択</p>
        <form method="POST" action="{{ route('sort')}}">
            @csrf
            @if ($sort_flag === 1)
                <input type="radio" name="sort_flag" value="1" checked>日付順
                <input type="radio" name="sort_flag" value="2">費用科目順
            @else
                <input type="radio" name="sort_flag" value="1">日付順
                <input type="radio" name="sort_flag" value="2" checked>費用科目順
            @endif
            <input type="hidden" name="selected_year" value="{{$selected_year}}">
            <input type="hidden" name="selected_month" value="{{$selected_month}}">
            <input type="submit" value="並び替え">
        </form>

        <!-- 支払項目をすべて表示 -->
        @php
            $i = 1;
        @endphp
        <div class="f-container">
            @if (count($payment_data) === 0)
                <p>{{$selected_year}}年{{$selected_month}}月の支払データはありません</p>
            @else
                <div class="tr th-margin">
                    <div class="form-tr no-margin">
                        <span class="cell-1 text-center">支払日</span>
                        <span class="cell-2 text-center">科目名</span>
                        <span class="cell-3 text-center">支払金額</span>
                        <span class="cell-4 text-center">メモ</span>
                        <span class="cell-5 text-center">使用フラグ</span>
                        <span class="cell-6"></span>
                    </div>
                    <div class="delete-button-cell">
                        <div></div>
                    </div>
                </div>
                @foreach($payment_data as $value)
                    @if (old('payment_id') !== null)
                        @if ((int) old('payment_id') === $value->id)
                            <div class="tr tr-margin alert alert-danger">
                                <form class="form-tr no-margin" method="post" action="{{ route('update_payment')}}" onsubmit="return replace_inp({{$i}})">
                                    @csrf
                                    <span class="cell-1">
                                        @php
                                            $target_date = $value->target_month;
                                            $payment_day = substr($target_date, 8, 2);
                                        @endphp
                                        @if ($value->status === 1)
                                            {{$selected_year}}年{{$selected_month}}月
                                            <input type="hidden" id="year{{$i}}"  name="payment_year"  value="{{$selected_year}}">
                                            <input type="hidden" id="month{{$i}}" name="payment_month" value="{{$selected_month}}">
                                            <span type="hidden" id="pay_day{{$i}}" style="display:none;" data-val="{{$payment_day}}"></span>
                                            <select name="payment_day" id="day{{$i}}" data-old-value="{{old('payment_day')}}">
                                            </select>
                                            <lable for="payment_day">日</label>
                                        @else
                                            <p class="no-margin">{{$selected_year}}年{{$selected_month}}月</p>
                                            <input type="hidden" name="payment_year"  value="{{$selected_year}}">
                                            <input type="hidden" name="payment_month" value="{{$selected_month}}">
                                            <input type="hidden" name="payment_day" value="{{$payment_day}}">
                                        @endif
                                    </span>
                                    <span class="cell-2 text-center">
                                        <select name="genre">
                                            @foreach ($all_genres as $genre_value)
                                                @if ((int) old('genre') === $genre_value->genre_id)
                                                    <option value="{{$genre_value->genre_id}}" selected>{{$genre_value->genre_name}}</option>
                                                @else
                                                    <option value="{{$genre_value->genre_id}}"         >{{$genre_value->genre_name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </span>
                                    <span class="cell-3 text-center">
                                        <input type="text" name="payment" id="payment{{$i}}" value="{{old('payment')}}">
                                    </span>
                                    <span class="cell-4 text-center">
                                        <input type="text" name="memo" value="{{old('memo')}}">
                                    </span>
                                    <span class="cell-5 text-center">
                                        @if ((int) old('flag') === 1)
                                            <p class="no-margin"><input type="radio" name="flag" value="1" checked="checked">表示</p>
                                            <input type="radio" name="flag" value="0"                  >非表示
                                        @else
                                            <p class="no-margin"><input type="radio" name="flag" value="1"                  >表示</p>
                                            <input type="radio" name="flag" value="0" checked="checked">非表示
                                        @endif
                                    </span>
                                    <span class="cell-6 text-right">
                                        <input type="hidden" name="payment_id" value="{{$value->id}}">
                                        <input type="hidden" name="sort_flag" value="{{$sort_flag}}">
                                        <input type="submit" value="更新">
                                    </span>
                                </form>
                                <form class="delete-button-cell" method="post" action="{{ route('delete_payment')}}">
                                    @csrf
                                        <input type="hidden" name="payment_id" value="{{$value->id}}">
                                        <input type="hidden" name="selected_year" value="{{$selected_year}}">
                                        <input type="hidden" name="selected_month" value="{{$selected_month}}">
                                        <input type="hidden" name="sort_flag" value="{{$sort_flag}}">
                                        <input type="submit" value="削除">
                                </form>
                            </div>
                            @php
                                $i++;
                            @endphp
                        @else
                            <div class="tr tr-margin">
                                <form class="form-tr no-margin" method="post" action="{{ route('update_payment')}}" onsubmit="return replace_inp({{$i}})">
                                    @csrf
                                    <span class="cell-1">
                                        @php
                                            $target_date = $value->target_month;
                                            $payment_day = substr($target_date, 8, 2);
                                        @endphp
                                        @if ($value->status === 1)
                                            {{$selected_year}}年{{$selected_month}}月
                                            <input type="hidden" id="year{{$i}}"  name="payment_year"  value="{{$selected_year}}">
                                            <input type="hidden" id="month{{$i}}" name="payment_month" value="{{$selected_month}}">
                                            <span type="hidden" id="pay_day{{$i}}" style="display:none;" data-val="{{$payment_day}}"></span>
                                            <select name="payment_day" id="day{{$i}}">
                                            </select>
                                            <lable for="payment_day">日</label>
                                        @else
                                            <p class="no-margin">{{$selected_year}}年{{$selected_month}}月</p>
                                            <input type="hidden" name="payment_year"  value="{{$selected_year}}">
                                            <input type="hidden" name="payment_month" value="{{$selected_month}}">
                                            <input type="hidden" name="payment_day" value="{{$payment_day}}">
                                        @endif
                                    </span>
                                    <span class="cell-2 text-center">
                                        <select name="genre">
                                            @foreach ($all_genres as $genre_value)
                                                @if ($value->genre_id === $genre_value->genre_id)
                                                    <option value="{{$genre_value->genre_id}}" selected>{{$genre_value->genre_name}}</option>
                                                @else
                                                    <option value="{{$genre_value->genre_id}}"         >{{$genre_value->genre_name}}</option>
                                                @endif
                                            @endforeach
                                        </select>
                                    </span>
                                    <span class="cell-3 text-center">
                                        <input type="text" name="payment" id="payment{{$i}}" value="{{$value->payment}}">
                                    </span>
                                    <span class="cell-4 text-center">
                                        <input type="text" name="memo" value="{{$value->memo}}">
                                    </span>
                                    <span class="cell-5 text-center">
                                        @if ($value->flag === 1)
                                            <p class="no-margin"><input type="radio" name="flag" value="1" checked="checked">表示</p>
                                            <input type="radio" name="flag" value="0"                  >非表示
                                        @else
                                            <p class="no-margin"><input type="radio" name="flag" value="1"                  >表示</p>
                                            <input type="radio" name="flag" value="0" checked="checked">非表示
                                        @endif
                                    </span>
                                    <span class="cell-6 text-right">
                                        <input type="hidden" name="payment_id" value="{{$value->id}}">
                                        <input type="hidden" name="sort_flag" value="{{$sort_flag}}">
                                        <input type="submit" value="更新">
                                    </span>
                                </form>
                                <form class="delete-button-cell" method="post" action="{{ route('delete_payment')}}">
                                    @csrf
                                        <input type="hidden" name="payment_id" value="{{$value->id}}">
                                        <input type="hidden" name="selected_year" value="{{$selected_year}}">
                                        <input type="hidden" name="selected_month" value="{{$selected_month}}">
                                        <input type="hidden" name="sort_flag" value="{{$sort_flag}}">
                                        <input type="submit" value="削除">
                                </form>
                            </div>
                            @php
                                $i++;
                            @endphp
                        @endif

                    @else
                        <div class="tr tr-margin">
                            <form class="form-tr no-margin" method="post" action="{{ route('update_payment')}}" onsubmit="return replace_inp({{$i}})">
                                @csrf
                                <span class="cell-1">
                                    @php
                                        $target_date = $value->target_month;
                                        $payment_day = substr($target_date, 8, 2);
                                    @endphp
                                    @if ($value->status === 1)
                                        {{$selected_year}}年{{$selected_month}}月
                                        <input type="hidden" id="year{{$i}}"  name="payment_year"  value="{{$selected_year}}">
                                        <input type="hidden" id="month{{$i}}" name="payment_month" value="{{$selected_month}}">
                                        <span type="hidden" id="pay_day{{$i}}" style="display:none;" data-val="{{$payment_day}}"></span>
                                        <select name="payment_day" id="day{{$i}}">
                                        </select>
                                        <lable for="payment_day">日</label>
                                    @else
                                        <p class="no-margin">{{$selected_year}}年{{$selected_month}}月</p>
                                        <input type="hidden" name="payment_year"  value="{{$selected_year}}">
                                        <input type="hidden" name="payment_month" value="{{$selected_month}}">
                                        <input type="hidden" name="payment_day" value="{{$payment_day}}">
                                    @endif
                                </span>
                                <span class="cell-2 text-center">
                                    <select name="genre">
                                        @foreach ($all_genres as $genre_value)
                                            @if ($value->genre_id === $genre_value->genre_id)
                                                <option value="{{$genre_value->genre_id}}" selected>{{$genre_value->genre_name}}</option>
                                            @else
                                                <option value="{{$genre_value->genre_id}}"         >{{$genre_value->genre_name}}</option>
                                            @endif
                                        @endforeach
                                    </select>
                                </span>
                                <span class="cell-3 text-center">
                                    <input type="text" name="payment" id="payment{{$i}}" value="{{$value->payment}}">
                                </span>
                                <span class="cell-4 text-center">
                                    <input type="text" name="memo" value="{{$value->memo}}">
                                </span>
                                <span class="cell-5 text-center">
                                    @if ($value->flag === 1)
                                        <p class="no-margin"><input type="radio" name="flag" value="1" checked="checked">表示</p>
                                        <input type="radio" name="flag" value="0"                  >非表示
                                    @else
                                        <p class="no-margin"><input type="radio" name="flag" value="1"                  >表示</p>
                                        <input type="radio" name="flag" value="0" checked="checked">非表示
                                    @endif
                                </span>
                                <span class="cell-6 text-right">
                                    <input type="hidden" name="payment_id" value="{{$value->id}}">
                                    <input type="hidden" name="sort_flag" value="{{$sort_flag}}">
                                    <input type="submit" value="更新">
                                </span>
                            </form>
                            <form class="delete-button-cell" method="post" action="{{ route('delete_payment')}}">
                                @csrf
                                    <input type="hidden" name="payment_id" value="{{$value->id}}">
                                    <input type="hidden" name="selected_year" value="{{$selected_year}}">
                                    <input type="hidden" name="selected_month" value="{{$selected_month}}">
                                    <input type="hidden" name="sort_flag" value="{{$sort_flag}}">
                                    <input type="submit" value="削除">
                            </form>
                        </div>
                        @php
                            $i++;
                        @endphp
                    @endif
                @endforeach
            @endif
        </div>
    </div>
    @section('js')
        <script src="{{url('js/SeparatorComma_and_TargetDate_for_IndexPayment.js')}}"></script>
    @endsection
@endsection
