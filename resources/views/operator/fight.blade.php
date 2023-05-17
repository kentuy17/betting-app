@extends('layouts.app')

@section('additional-styles')
<link rel="stylesheet" href="{{ asset('css/play-sabong.css') }}" type="text/css">
@endsection

@section('content')
<div class="container">
  <div class="row">
    <div class="col-md-6">
      <div class="card">
        <div class="card-header">{{ __('x100 Exp.') }}</div>
        <video width="100%">
          <source src="{{ asset('videos/e-sabong.mp4') }}" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>
    </div>
    <div class="col-md-6" id="fight-component"></div>
    <div class="col-md-12">
      <div class="card">
        <div class="results">
          <div class="bet-result-chart">
            <table id="tblBaccaratResultConsecutive" class="cell-border w-100 dataTable no-footer">
                <tbody></tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .fight-container {
    display: flex;
    justify-content: space-between;
  }
  .total-bets {
    color: blue;
    text-align: center;
    font-weight: bold;
  }
  .win-chance {
    text-align: center;
    font-weight: bold;
  }
  button:disabled {
    cursor: not-allowed;
    pointer-events: all !important;
  }
  .results .bet-result-chart {
    /* max-width: 720px; */
    overflow-x: auto;
    max-height: 300px !important;
    margin: 2rem 0;
  }
</style>
@endsection

@section('additional-scripts')
@vite('resources/js/fight-vue.js')
@vite('public/js/fight.js')
{{-- <script src="{{ asset('js/fight.js') }}" defer></script> --}}
@endsection
