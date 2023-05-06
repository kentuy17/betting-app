@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <div class="card col-md-8">
        <div class="card-header">{{ __('Transaction History') }}</div>
        <div class="card-body">
          <table class="table table-striped w-100" id="transactions-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Action</th>
                <th>Type</th>
                <th>Processed By ID</th>
                <th>Player ID</th>
                <th>Name</th>
                <th>Amount</th>
                <th>Mobile#</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
          </table>
        </div>
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
<script src="{{ asset('js/transactions.js') }}" defer></script>
@endsection
