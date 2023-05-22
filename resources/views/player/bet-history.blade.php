@extends('layouts.app')

@section('content')
<div class="container mt-4">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card col-md-12">
        <div class="card-header font-bold">{{ __('BET HISTORY') }}</div>
        <div class="card-body">
          <table class="table table-striped" style="width: 100%" id="bethistory-table">
            <thead>
              <tr>
                <th>EVENT</th>
                <th>FIGHT #</th>
                <th>SIDE</th>
                <th>BET AMOUNT</th>
                <th>WIN AMOUNT</th>
                <th>PERCENT %</th>
                <th>DATE</th>
                <th>STATUS</th>
              </tr>
            </thead>
          </table>
        </div>
          
      </div>
    </div>
  </div>
</div>
@endsection

@section('additional-scripts')
<script src="{{ asset('js/bet-history.js') }}" defer></script>
@endsection
