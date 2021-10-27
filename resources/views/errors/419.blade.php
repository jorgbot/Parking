@extends('layouts.app')
@section('content')
    <h3>{{ tt('pages/welcome.errors.419') }}</h3>
@endsection
@section('scripts')
    <script>
        location = '/login';
    </script>
@endsection
