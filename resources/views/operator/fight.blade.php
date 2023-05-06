@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12 flex justify-between">
      <div class="card col-md-6 col-sm-12 d-none">
        <div class="card-header">{{ __('x100 Exp.') }}</div>
        <video width="100%">
          <source src="{{ asset('videos/e-sabong.mp4') }}" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>
      <div class="card col-md-6 col-sm-12">
        <div class="fight-container pt-8 px-4">
          <div>
            <p class="text-danger label--sides mb-4 text-4xl font-bold">MERON</p>
            <div>
              <p id="m-total-bets" class="total-bets">0</p>
              <p id="m-win-chance" class="win-chance">180.54%</p>
            </div>
          </div>
          <div>
            <div>
              <p class="inline label text-center font-bold text-3xl">FIGHT # </p>
              <p id="fight-no" class="inline text-3xl font-bold">  </p>
            </div>
            <p class="text-secondary text-center mt-4 font-medium text-3xl" id="fight-status">____</p>
          </div>
          <div>
            <p class="text-primary label--sides mb-4 text-4xl font-bold">WALA</p>
            <div>
              <p id="w-total-bets" class="total-bets">0</p>
              <p id="w-win-chance" class="win-chance">170.54%</p>
            </div>
          </div>
        </div>
        <div class="flex flex-col lg:flex-row lg:gap-0 gap-2 justify-evenly mt-7">
          <button id="open-fight" class="btn btn-success btn-lg">OPEN</button>
          <button id="close-fight" class="btn btn-danger btn-lg">CLOSE</button>
          <button id="done-fight" class="btn btn-secondary btn-lg disabled" disabled>DONE</button>
        </div>
        <div class="flex flex-col lg:flex-row lg:gap-0 gap-2 justify-evenly mt-7">
          <button id="cancel-fight" class="btn btn-primary btn-sm">Cancel Fight</button>
        </div>
      </div>
    </div>
    <div class="my-4 card col-md-12">
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

  .bet-result-chart table tr td {
    padding: 2.5px !important;
    border: 1px solid rgba(0, 0, 0, .178) !important;
  }

  .results .bet-result-chart table tr td div {
      height: 2rem;
      width: 2rem;
  }

  .results .bet-result-chart table tr td .circleRedAll {
    height: 2rem;
    width: 2rem;
    background-color: #f34141;
    border-radius: 1.2rem;
    text-align: center;
    color: #fff;
    line-height: 25px;
    font-weight: 700;
  }

  .results .bet-result-chart table tr td .circleBlueAll {
    height: 2rem;
    width: 2rem;
    background-color: #0d6efd;
    border-radius: 1.2rem;
    text-align: center;
    color: #fff;
    line-height: 25px;
    font-weight: 700;
  }

  .results .bet-result-chart table tr td .circleGreenAll {
    height: 2rem;
    width: 2rem;
    background-color: green;
    border-radius: 1.2rem;
    text-align: center;
    color: #fff;
    line-height: 25px;
    font-weight: 700;
  }
  

</style>
@endsection

@section('additional-scripts')
<script src="{{ asset('js/fight.js') }}" defer></script>
@endsection
