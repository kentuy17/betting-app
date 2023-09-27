@extends('layouts.app')

@section('additional-styles')
  <style>
    .card-commission {
      position: relative;
      display: flex;
      flex-direction: column;
      min-width: 0;
      word-wrap: break-word;
      background-color: #fff;
      background-clip: border-box;
      border: 0 solid rgba(0, 0, 0, .125);
      border-radius: 1rem;
    }
  </style>
@endsection

@section('content')
  <div class="agent-navbar"></div>
  <div class="container mt-3">
    <div class="row justify-content-center">
      <div class="col-md-10">
        <div id="master-agent"></div>
      </div>
    </div>
  </div>
@endsection

@section('additional-scripts')
  @viteReactRefresh
  @vite('resources/react/master-agent.jsx')
@endsection
