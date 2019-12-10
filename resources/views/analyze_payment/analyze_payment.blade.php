@extends('layouts.app')
@section('title')
    支払実績確認
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
        <!-- 固定費の入力 -->
        <!-- 固定費の月を選択させる -->
        <div>
        <p>支払データを表示する期間と費用科目を選択し、『送信』を押してください。</p>
        <form method="POST" action="{{ route('search_analyze_payment')}}">
            @csrf
            <select name="first_year">
                <!-- yearはコントローラ側で配列にする。マージンは一年 -->
                @if (old('first_year') === null)
                    @foreach($tmp_year as $value)
                        @if ($value === $first_year)
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @else
                    @foreach($tmp_year as $value)
                        @if ($value === old('first_year'))
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            <label for="first_year">年</label>

            <select name="first_month">
                @if (old('first_month') === null)
                    @foreach($tmp_month as $value)
                        @if ($value === $first_month)
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @else
                    @foreach($tmp_month as $value)
                        @if ($value === old('first_month'))
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            <label for="first_month">月</label>～
            <select name="last_year">
                <!-- yearはコントローラ側で配列にする。マージンは一年 -->
                @if (old('last_year') === null)
                    @foreach($tmp_year as $value)
                        @if ($value === $last_year)
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @else
                    @foreach($tmp_year as $value)
                        @if ($value === old('last_year'))
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            <label for="last_year">年</label>

            <select name="last_month">
                @if (old('last_month') === null)
                    @foreach($tmp_month as $value)
                        @if ($value === $last_month)
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @else
                    @foreach($tmp_month as $value)
                        @if ($value === old('last_month'))
                            <option value="{{$value}}" selected>{{$value}}</option>
                        @else
                            <option value="{{$value}}">{{$value}}</option>
                        @endif
                    @endforeach
                @endif
            </select>
            <label for="last_month">月</label>

            <!-- チェック形式で表示するものを選ばせる -->
            <ul>
                @php
                    $i = 0;
                @endphp
                @if ($selected_flag === true)
                    @foreach($genre_name as $value)
                        @if($checked_status[$i] === 0)
                            <li>
                                <label for="check{{$i}}">
                                    <input id="check{{$i}}" type="checkbox" name="search_id[]" value="{{$value->genre_id}}">{{$value->genre_name}}
                                </label>
                            </li>
                        @else
                            <li>
                                <label for="check{{$i}}">
                                    <input id="check{{$i}}" type="checkbox" name="search_id[]" value="{{$value->genre_id}}" checked="checked">{{$value->genre_name}}
                                </label>
                          </li>
                        @endif
                        @php
                            $i++;
                        @endphp
                    @endforeach
                @else
                    @foreach($genre_name as $value)
                        <li>
                            <label for="check{{$i}}">
                                <input id="check{{$i}}" type="checkbox" name="search_id[]" value="{{$value->genre_id}}" checked="checked">{{$value->genre_name}}
                            </label>
                        </li>
                        @php
                            $i++;
                        @endphp
                    @endforeach
                @endif
            </ul>
            <input type="submit" value="送信">
        </form>
        @if (count($payment_data) !== 0)
            @php
                $i = 0;
                $tmp_id = '';
                $tmp_date = '';
                $tmp_genre_name = '';
            @endphp
            @foreach($payment_data as $tmp_value)
                @php
                    $h = 0;
                    $tmp_str='';
                @endphp
                @foreach($tmp_value as $value)
                    @if($h === 0)
                        @if($i === 0)
                            @php
                                $tmp_genre_name = $value['genre_name'];
                            @endphp
                        @else
                            @php
                                $tmp_genre_name .= ',' . $value['genre_name'];
                            @endphp
                        @endif
                    @endif
                    @if($h === 0)
                        @if($i === 0)
                            @php
                                $tmp_id = $value['genre_id'];
                            @endphp
                        @else
                            @php
                                $tmp_id .= ',' . $value['genre_id'];
                            @endphp
                        @endif
                    @endif
                    @if($i === 0)
                        @if($h === 0)
                            @php
                                $tmp_date = $value['selected_date'];
                            @endphp
                        @else
                            @php
                                $tmp_date .= ',' . $value['selected_date'];
                            @endphp
                        @endif
                    @endif
                    @if($h === 0)
                        @php
                            $tmp_str = $value['payment'];
                        @endphp
                    @else
                        @php
                            $tmp_str .= ',' . $value['payment'];
                        @endphp
                    @endif
                    @php
                        $h++;
                    @endphp
                @endforeach
                <div
                    type="hidden"
                    id="payment{{$i}}"
                    style="display:none;"
                    data-val="{{$tmp_str}}"
                ></div>
                @php
                    $i++;
                @endphp
            @endforeach
            <div type="hidden" id="genre_name" style="display:none;" data-val="{{$tmp_genre_name}}"></div>
            <div type="hidden" id="selected_date" style="display:none;" data-val="{{$tmp_date}}"></div>
            <div type="hidden" id="genre_id" style="display:none;" data-val="{{$tmp_id}}"></div>
            <div type="hidden" id="first_year" style="display:none;" data-val="{{$first_year}}"></div>
            <div type="hidden" id="first_month" style="display:none;" data-val="{{$first_month}}"></div>
            <div type="hidden" id="last_year" style="display:none;" data-val="{{$last_year}}"></div>
            <div type="hidden" id="last_month" style="display:none;" data-val="{{$last_month}}"></div>

            <!-- グラフ描写用のフィールドを定義 -->
            <div class="graph-box">
                <canvas id="myChart"></canvas>
            </div>
        @endif
    </div>
    @section('js')
        <script src="https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.8.0/Chart.bundle.js"></script>
        <script src="{{url('js/AnalyzePaymentGraph.js')}}"></script>
    @endsection
@endsection
