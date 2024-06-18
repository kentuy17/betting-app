@extends('layouts.app-sub')

@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('css/operator.css') }}">
  <style>
    .nav-orig {
      --bs-nav-link-padding-x: 1rem;
      --bs-nav-link-padding-y: .5rem;
      --bs-nav-link-font-weight: ;
      --bs-nav-link-color: var(--bs-link-color);
      --bs-nav-link-hover-color: var(--bs-link-hover-color);
      --bs-nav-link-disabled-color: #6c757d;
      display: flex;
      flex-wrap: wrap;
      padding-left: 0;
      margin-bottom: 0;
      list-style: none;
    }
  </style>
@endsection

@section('content')
  <div class="container mt-4">
    <div class="row justify-content-center">
      <div class="col-md-12 card">
        <div class="card-header">
          <ul class="nav-orig nav-tabs" style="display: flex;" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
              <button class="nav-link active" id="deposit-tab" data-bs-toggle="tab" data-bs-target="#deposit-panel" type="button" role="tab" aria-controls="deposit" aria-selected="true">
                Deposits <span id="badge-deposit" style="display: none;" class="badge bg-danger">0</span></button>
            </li>
            <li class="nav-item" role="presentation">
              <button class="nav-link" id="withdraw-tab" data-bs-toggle="tab" data-bs-target="#withdraw" type="button" role="tab" aria-controls="withdraw" aria-selected="false">
                Withdrawals <span id="badge-withdraw" style="display: none;" class="badge bg-danger">0</span>
                <span id="badge-withdraw-unverified" data-bs-toggle="tooltip" title="Missing Ref-code" style="display: none;" class="badge bg-warning">4</span></button>
            </li>
            <li class="nav-item credit-nav-item">
              <div class="nav-credits-wr w-25 w-sm-50 gold-text">
                <a href="/platform/deposit" class="d-flex align-items-center justify-content-end gp-credits">
                  <div class="bg-success add-btn">
                    <svg class="svg-inline--fa fa-coins fa-w-16" data-prefix="fas" data-icon="coins" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                      <path fill="currentColor"
                        d="M0 405.3V448c0 35.3 86 64 192 64s192-28.7 192-64v-42.7C342.7 434.4 267.2 448 192 448S41.3 434.4 0 405.3zM320 128c106 0 192-28.7 192-64S426 0 320 0 128 28.7 128 64s86 64 192 64zM0 300.4V352c0 35.3 86 64 192 64s192-28.7 192-64v-51.6c-41.3 34-116.9 51.6-192 51.6S41.3 334.4 0 300.4zm416 11c57.3-11.1 96-31.7 96-55.4v-42.7c-23.2 16.4-57.3 27.6-96 34.5v63.6zM192 160C86 160 0 195.8 0 240s86 80 192 80 192-35.8 192-80-86-80-192-80zm219.3 56.3c60-10.8 100.7-32 100.7-56.3v-42.7c-35.5 25.1-96.5 38.6-160.7 41.8 29.5 14.3 51.2 33.5 60 57.2z">
                      </path>
                    </svg>
                  </div>
                  <div class="credits-data d-flex "><small class="mr-1">CREDITS:</small>
                    <small class="pr-2 gp-yellow-text font-weight-bold" id="operator-pts">{{ number_format(Auth::user()->points, 2) }}</small>
                  </div>
                </a>
              </div>
            </li>
          </ul> <!-- END NAV -->
        </div>

        <!-- TABLE START -->
        <div class="tab-content card-body" id="myTabContent">
          <div class="tab-pane fade show active" id="deposit-panel" role="tabpanel" aria-labelledby="deposit-tab">
            <table class="table dt-responsive table-striped nowrap w-100" id="player-transaction-table">
              <thead>
                <tr>
                  <th>Status</th>
                  {{-- <th>Outlet</th> --}}
                  <th>Receiver</th>
                  <th>Amount</th>
                  <th>Submitted</th>
                  <th>Ref-Code #</th>
                  <th>Note</th>
                  <th>Processed by</th>
                </tr>
              </thead>
            </table>
          </div>
          <div class="tab-pane fade" id="withdraw" role="tabpanel" aria-labelledby="withdraw-tab">
            <table class="table dt-responsive table-striped nowrap w-100" id="player-withdraw-table">
              <thead>
                <tr>
                  <th>Status</th>
                  {{-- <th>Outlet</th> --}}
                  <th>Receiver</th>
                  <th>Amount</th>
                  <th>Submitted</th>
                  <th>Ref-Code #</th>
                  <th>Note</th>
                  <th>Processed by</th>
                </tr>
              </thead>
            </table>
          </div>
        </div>


        {{-- <div class="card col-md-12">
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
      </div> --}}
      </div>
    </div>
  </div>
@endsection

@section('additional-scripts')
  <script src="{{ asset('js/player-transaction.js') }}" defer></script>
@endsection
