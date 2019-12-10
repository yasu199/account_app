@extends('layouts.app')
@section('title')
    予算支払比較
@endsection
<!-- cssファイルの読み込み -->
@section('css')
    <link href="{{url('css/base.css')}}" rel="stylesheet" type="text/css">
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
        @if ($message !== true)
            <div class="alert alert-danger">
                <p>{{$message}}</p>
                <p>データが登録されているか確認してください。</p>
            </div>
        @endif


    <!-- 予算と支払い実績を比較する月を選択させる -->
        <p>予算と支払いを比較する月を選択してください</p>
        <div>
        <form method="POST" action="{{ route('search_compare_payment')}}">
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

            <input type="submit" value="送信">
        </form>
        <!-- とりあえずすべてのパラメータを渡す -->
        @php
            $i = 0;
            $tmp_genre_name = '';
            $tmp_budget = '';
            $tmp_payment = '';
        @endphp
        @foreach ($genre_name as $value)
            @if ($i === 0)
                @php
                    $tmp_genre_name = $value;
                @endphp
            @else
                @php
                    $tmp_genre_name .= ',' . $value;
                @endphp
            @endif
            @php
                $i++;
            @endphp
        @endforeach
        @php
            $i = 0;
        @endphp
        @foreach ($budget as $value)
            @if ($i === 0)
                @php
                    $tmp_budget = $value;
                @endphp
            @else
                @php
                    $tmp_budget .= ',' . $value;
                @endphp
            @endif
            @php
                $i++;
            @endphp
        @endforeach
        @php
            $i = 0;
        @endphp
        @foreach ($payment as $value)
            @if ($i === 0)
                @php
                    $tmp_payment = $value;
                @endphp
            @else
                @php
                    $tmp_payment .= ',' . $value;
                @endphp
            @endif
            @php
                $i++;
            @endphp
        @endforeach

        <div type="hidden" id="genre_name" style="display:none;" data-val="{{$tmp_genre_name}}"></div>
        <div type="hidden" id="selected_date" style="display:none;" data-val="{{$selected_date}}"></div>
        <div type="hidden" id="selected_year" style="display:none;" data-val="{{$selected_year}}"></div>
        <div type="hidden" id="selected_month" style="display:none;" data-val="{{$selected_month}}"></div>
        <div type="hidden" id="budget" style="display:none;" data-val="{{$tmp_budget}}"></div>
        <div type="hidden" id="payment" style="display:none;" data-val="{{$tmp_payment}}"></div>


<div class="flex">
        <!-- <div class="graph-box"> -->
            @if ($exit_budget === TRUE)
                <!-- 予算の円グラフを出力するための処理 -->
                <div class="graph-box">
                    <canvas id="myCircleBudgetChart"></canvas>
                </div>
                <div type="hidden" id="exit_budget" style="display:none;" data-val="1"></div>
            @else
                <div type="hidden" id="exit_budget" style="display:none;" data-val="0"></div>
            @endif
        <!-- </div> -->
        <!-- <div class="graph-box"> -->
            @if ($exit_payment === TRUE)
                <!-- 支払の円グラフを出力するための処理 -->
                <div class="graph-box">
                    <canvas id="myCirclePaymentChart"></canvas>
                </div>
                <div type="hidden" id="exit_payment" style="display:none;" data-val="1"></div>
            @else
                <div type="hidden" id="exit_payment" style="display:none;" data-val="0"></div>
            @endif
        <!-- </div> -->
</div>


        @if ($exit_budget === TRUE && $exit_payment === FALSE)
            <!-- 予算の棒グラフを出力するための処理 -->
            <div class="graph-box">
                <canvas id="myBarBudgetChart"></canvas>
            </div>
        @elseif ($exit_budget === FALSE && $exit_payment === TRUE)
            <div class="graph-box">
                <canvas id="myBarPaymentChart"></canvas>
            </div>
        @elseif ($exit_budget === TRUE && $exit_payment === TRUE)
            <div class="graph-box">
                <canvas id="myBarChart"></canvas>
            </div>
        @endif
    </div>

    @section('js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.js"></script>
        <script src="{{url('js/ComparePaymentGraph.js')}}"></script>
    @endsection
@endsection
