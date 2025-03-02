
@extends('adminlte::page')


@section('content_header')

@stop

@section('content')
    <p class="text-xl">Welcome to this beautiful admin panel.</p>
@stop

@section('css')
    {{-- Add here extra stylesheets --}}
    {{-- <link rel="stylesheet" href="/css/admin_custom.css"> --}}
    @vite(['resources/css/app.css'])

    

@stop

@section('js')
    <script> console.log("Hi, I'm using the Laravel-AdminLTE package!"); </script>
@stop