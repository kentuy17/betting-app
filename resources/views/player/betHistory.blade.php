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
                <th>Fight Number</th>
                <th>Name</th>
                <th>Side</th>
                <th>Bet Amount</th>
                <th>Win Amount</th>
                <th>percent</th>
                <th>date</th>
                <th>status</th>
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
<script src="{{ asset('js/betHistory.js') }}" defer></script>
@endsection
