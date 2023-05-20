@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card col-md-12">
        <div class="card-header font-bold">{{ __('TRANSACTION HISTORY') }}</div>
        <div class="card-body">
          <table class="table table-striped" style="width: 100%" id="player-transaction-table">
            <thead>
              <tr>
                <th>Status</th>
                <th>Outlet</th>
                <th>Receiver</th>
                <th>Ref/Control #</th>
                <th>Amount</th>
                <th>Submitted</th>
                <th>Completed</th>
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
<script src="{{ asset('js/player-transaction.js') }}" defer></script>
@endsection
