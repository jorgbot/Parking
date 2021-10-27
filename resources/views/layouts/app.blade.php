<!doctype html>
<html lang="{{ app()->getLocale() }}">
    @include('app.assets_head')
    <body>
    <div id="main">
    @include('app.header')
    @yield('content')
    @yield('scripts')
    </div>
    </body>
</html>
