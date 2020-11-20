<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">

    <link rel="stylesheet" href="{{ asset('/backend/plugins/fontawesome-free/css/all.min.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/trix/1.2.3/trix.min.css" integrity="sha512-sC2S9lQxuqpjeJeom8VeDu/jUJrVfJM7pJJVuH9bqrZZYqGe7VhTASUb3doXVk6WtjD0O4DTS+xBx2Zpr1vRvg==" crossorigin="anonymous" />
    <!-- Styles -->
    <link href="{{ asset('css/app.css') }}" rel="stylesheet">

    <style>
        .level {
            display: flex;
            align-items: center;
        }

        .flex {
            flex: 1;
        }

        [v-cloak] {
            display: none;
        }

        .ml-a {
            margin-left: auto;
        }

        .ais-highlight > em {
            background: yellow;
            font-style: normal;
        }
    </style>
    <script>
        window.app = {!! json_encode([
            'signedIn' => auth()->check(),
            'user' => auth()->user(),
        ]) !!}
    </script>

    <link href="https://cdnjs.cloudflare.com/ajax/libs/trix/0.11.1/trix.css" rel="stylesheet">
    
    @stack('styles')
</head>
<body>
    <div id="app">
        
        @include('layouts._nav')

        @yield('content')

        <flash message="{{ session('flash') }}"></flash>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/v-tooltip"></script>

    <script src="{{ asset('js/app.js') }}"></script>
    <script type="text/javascript" charset="utf-8" async defer>
        $(function () {
            $('[data-toggle="tooltip"]').tooltip('enable')
        })      
    </script>

    @stack('scripts')
</body>
</html>
