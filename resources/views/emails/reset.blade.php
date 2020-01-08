<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <!-- Scripts -->
    <script src="{{ asset('js/app.js') }}" defer></script>
    <!-- Fonts -->
    <link rel="dns-prefetch" href="//fonts.gstatic.com">

    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @yield('css')
</head>
<body>
  <h1>パスワードリセット</h1>
  <p>
      <a href="{{route('welcome_page')}}">私の家計簿帖</a>
      よりパスワードリセットのお知らせです。<br>
      以下のURLよりパスワードリセット処理を実施してください。
  </p>
  <p>
      URL&nbsp;:&nbsp;<a href="{{$reset_url}}">{{$reset_url}}</a>
  </p>
</body>
</html>
