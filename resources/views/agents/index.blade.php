@extends('layouts.app-sub')

@section('content')
  <div class="container mt-3">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <div class="card col-md-12">
          <div class="card-header font-bold">{{ __('MY PLAYERS') }}</div>
          <div class="card-body">
            <table class="table table-striped" style="width: 100%" id="agent-players-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>USERNAME</th>
                  <th>COMMISSION</th>
                  <th>DATE JOINED</th>
                  <th>PLAYER PTS</th>
                  <th>STATUS</th>
                  <th>ACTION</th>
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
  <script src="{{ asset('js/agent-players.js') }}" defer></script>
@endsection
