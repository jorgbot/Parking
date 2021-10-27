<!DOCTYPE HTML>
<html>
    <head>
        <meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
        <title>@yield('title', tt('pages/header.title'))</title>
        @yield('styles')
    </head>
    <body data-mobile-sidebar="button">
        <div class="container-fluid" id="content">
            @yield('content')
        </div>
        @yield('scripts')
    </body>
</html>
