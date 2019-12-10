@extends('layouts.app')
@section('title')
    予算登録
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

        <p>最初に予算を設定する月を選び、『月を選択』を押してください。</p>

        <!-- 月の選択 月は選択されているようにする -->
        <form method="POST" action="{{route('budget_month')}}">
            @csrf
            <label>予算設定月：</lable>
            <select name="selected_date">
              <!-- 12か月分を回す -->

                @php
                    $now_date = date("m");
                    $now_year = date("Y");
                    $now_date = (int) $now_date;
                    $now_year = (int) $now_year;
                    $now_year_wr = $now_year;
                    $now_year *= 100;
                @endphp
                @if (isset($selected_date) === FALSE)
                    @php
                        $selected_date = $now_date + $now_year;
                    @endphp
                @endif
                @if (old('selected_date') !== null)
                    @php
                        $selected_date = old('selected_date');
                    @endphp
                @endif
                <!-- $now_date,$now_yearをコントローラーで定義する -->
                @for ($i = 0; $i <= 12; $i++)
                    @if ($selected_date === ($now_date + $now_year))
                        <option value="{{$now_date + $now_year}}" selected>{{$now_year_wr}}年{{$now_date}}月</option>
                    @else
                        <option value="{{$now_date + $now_year}}">{{$now_year_wr}}年{{$now_date}}月</option>
                    @endif
                    @if (($now_date + 1) < 13)
                        @php
                            $now_date++;
                        @endphp
                    @else
                        @php
                            $now_date = 1;
                            $now_year+= 100;
                            $now_year_wr++;
                        @endphp
                    @endif
                @endfor
            </select>
            <input type="submit" value="月を選択">
        </form>
        <!-- 変動費の入力 -->
        <div class="top-margin">
            <p>
                月を選択後、予算を入力。<br>
                変更する場合は記載されている金額を変更し、『予算送信』を押してください。<br>
            </p>
            <form method="POST" action="{{ route('inp_budget')}}" onsubmit="replace_inp()">
                @csrf
                <!-- onsubmitで送信するときに入力のカンマ区切りを修正 -->
                <input type="hidden" name="selected_date" value="{{$selected_date}}">
              <!-- 変動費部分 -->
              <!-- 予算入力があった場合は -->
                <!-- コントローラーより情報を取得 -->
                <p>変動費</p>
                <ul class="ul-box">
                    <!-- idに対して異なる名前を付けるため、iで変化をつける -->
                    @php
                        $i = 1;
                    @endphp
                    @foreach ($variable_budget as $value)
                        <li>
                            <label class="label-name">{{$value->genre_name}}</label>
                            @if (old('budget') !== null)
                                <input id="numdata{{$i}}" type="text" name="budget[{{$value->genre_id}}]" value="{{old('budget')[$value->genre_id]}}">
                            @elseif (isset($value->budget) === TRUE)
                                <input id="numdata{{$i}}" type="text" name="budget[{{$value->genre_id}}]" value="{{$value->budget}}">
                            @else
                                <input id="numdata{{$i}}" type="text" name="budget[{{$value->genre_id}}]" value="0">
                            @endif
                        </li>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </ul>
            <!-- 予算入力があった場合は -->
              <!-- コントローラーより情報を取得 -->
                <p>固定費</p>
                <ul class="ul-box">
                    <!-- idに対して異なる名前を付けるため、iで変化をつける -->
                    @foreach ($fixed_budget as $value)
                        <li>
                            <label class="label-name">{{$value->genre_name}}</label>
                            @if (old('budget') !== null)
                                <input id="numdata{{$i}}" type="text" name="budget[{{$value->genre_id}}]" value="{{old('budget')[$value->genre_id]}}">
                            @elseif (isset($value->budget) === TRUE)
                                <input id="numdata{{$i}}" type="text" name="budget[{{$value->genre_id}}]" value="{{$value->budget}}">
                            @else
                                <input id="numdata{{$i}}" type="text" name="budget[{{$value->genre_id}}]" value="0">
                            @endif
                        </li>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                </ul>
                <input type="hidden" name="flag" value="{{$flag}}">

                <input type="submit" value="予算送信">
            </form>
            <p>
                ※初めから入力欄に記入してある金額は、すでに設定がされている予算です。<br>
                予算送信ボタンを押すと、予算として入力欄に記入されている金額が設定されます。<br>
                金額を変更しない科目がある場合は、その科目入力欄の数字を変更せずに送信してください。
            </p>
        </div>
    </div>
    @section('js')
        <!-- <script src="{{url('js/separator_comma.js')}}"></script>
        <script src="{{url('js/show_message.js')}}"></script> -->
        <script src="{{url('js/SeparatorComma_and_ShowMessage.js')}}"></script>
    @endsection
@endsection
