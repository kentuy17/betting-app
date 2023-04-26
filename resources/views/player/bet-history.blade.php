@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card col-md-12">
        <div class="card-header">{{ __('Bet History') }}</div>
          <table class=" w-100" id="bethistory-table">
            <thead>
              <tr>
                <th>EVENT</th>
                <th>FIGHT #</th>
                <th>SIDE</th>
                <th>BET AMOUNT</th>
                <th>WIN AMOUNT</th>
                <th>PERCENT %</th>
                <th>DATE</th>
                <th>WINNER</th>
              </tr>
            </thead>
            {{-- <tbody>
            </tbody> --}}
          </table>
        </div>
        <div class="card-body">
          @if (session('status'))
          <div class="alert alert-success" role="alert">
            {{ session('status') }}
          </div>
          @endif

        </div>
      </div>
    </div>
  </div>
</div>
@endsection

@section('additional-scripts')
<script src="{{ asset('js/bet-history.js') }}" defer></script>
@endsection
