<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <title>Cadabra Express</title>
    <base href="/">

    <!--[if lt IE 9]>
    <script src="https://oss.maxcdn.com/html5shiv/3.7.3/html5shiv.min.js"></script>
    <script src="https://oss.maxcdn.com/respond/1.4.2/respond.min.js"></script>
    <![endif]-->

    <link rel="stylesheet" href="/css/theme.css">
    <link rel="stylesheet" href="/css/main.css">

    <script>
        const _token = '{{ csrf_token() }}';
        const currentUser = <?php echo auth()->user() ? json_encode(auth()->user()->toArray()) : 'null'; ?>;
    </script>
    @include('partials.javascript')
</head>
<body class="horizontal-navigation">
    @include('partials.header')

    @if(Auth::check())
        @include('partials.navbar')
    @endif

    <div class="page-content">
        @include('common.alerts')
        @yield('content')
    </div>

    @include('common.modals')
    <div id="templates"></div>

    {{--@include('partials.footer')--}}
    <script src="/js/vendor.js"></script>
    <script src="/js/app.js"></script>
    
    @yield('page-scripts')
</body>
</html>
