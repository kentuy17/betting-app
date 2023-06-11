@extends('layouts.app')
@section('additional-styles')
<link rel="stylesheet" href="{{ asset('css/operator.css') }}">
@endsection
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="requests-tab" data-bs-toggle="tab" data-bs-target="#requests" type="button" role="tab" aria-controls="requests" aria-selected="false">
            RESET PASSWORD REQUEST</button>
        </li>
        <li class="nav-item credit-nav-item">
          <div class="nav-credits-wr w-25 w-sm-50 gold-text">
            <a href="/refillpoints" class="d-flex align-items-center justify-content-end gp-credits">
              <div class="bg-success add-btn">
                <svg class="svg-inline--fa fa-coins fa-w-16" data-prefix="fas" data-icon="coins" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                  <path fill="currentColor" d="M0 405.3V448c0 35.3 86 64 192 64s192-28.7 192-64v-42.7C342.7 434.4 267.2 448 192 448S41.3 434.4 0 405.3zM320 128c106 0 192-28.7 192-64S426 0 320 0 128 28.7 128 64s86 64 192 64zM0 300.4V352c0 35.3 86 64 192 64s192-28.7 192-64v-51.6c-41.3 34-116.9 51.6-192 51.6S41.3 334.4 0 300.4zm416 11c57.3-11.1 96-31.7 96-55.4v-42.7c-23.2 16.4-57.3 27.6-96 34.5v63.6zM192 160C86 160 0 195.8 0 240s86 80 192 80 192-35.8 192-80-86-80-192-80zm219.3 56.3c60-10.8 100.7-32 100.7-56.3v-42.7c-35.5 25.1-96.5 38.6-160.7 41.8 29.5 14.3 51.2 33.5 60 57.2z"></path>
                </svg>
              </div>
              <div class="credits-data d-flex "><small class="mr-1">CREDITS:</small>
                <small class="pr-2 gp-yellow-text font-weight-bold" id="operator-pts">{{ number_format(Auth::user()->points, 2) }}</small>
              </div>
            </a>
          </div>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="requests" role="tabpanel" aria-labelledby="requests-tab">
          <table class="table dt-responsive table-striped nowrap w-100" id="requests-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Status</th>
                <th>UserName</th>
                <th>Phone Number</th>
                <th>New Password</th>
                <th>Date</th>
                <th>Action</th>
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
<script src="{{ asset('js/requestpasswordreset.js') }}" defer></script>
@endsection
