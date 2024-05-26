@extends('layouts.react-app')
@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('css/tailwind.css') }}">
@endsection

@section('content')
  <div id="users-list"></div>
@endsection

@section('additional-scripts')
  @viteReactRefresh
  @vite('resources/js/main.jsx')
@endsection
