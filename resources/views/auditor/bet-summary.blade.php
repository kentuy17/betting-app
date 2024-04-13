@extends('layouts.app')

@section('content')
  <div class="container">
    <div class="row col-md-12 justify-content-center">
      <div class="col-lg-6 m-auto p-3">
        <div class="card card-info">
          <div class="card-header">
            <h3 class="card-title">SELECT EVENT</h3>
          </div>
          <form class="form-horizontal">
            <div class="card-body">
              {{-- <div class="form-group row my-2">
                <label for="event-name" class="col-sm-2 col-form-label">Name</label>
                <div class="col-sm-10">
                  <input type="text" required class="form-control" id="event-name" placeholder="Event Name">
                </div>
              </div> --}}
              <div class="form-group row my-2">
                <label for="inputPassword3" class="col-sm-2 col-form-label">DATE</label>
                <div class="col-sm-10">
                  <input type="date" class="form-control" id="sched-date">
                </div>
              </div>
              {{-- <div class="form-group row my-2">
                <label for="inputPassword3" class="col-sm-2 col-form-label">Time Start</label>
                <div class="col-sm-10">
                  <input type="time" class="form-control" id="time-start" value="09:00:00">
                </div>
              </div> --}}
            </div>
            <div class="card-footer">
              <button id="filter-date" class="btn btn-primary float-right mb-2">FILTER</button>
            </div>
          </form>
        </div>
      </div>
    </div>
    <div class="row col-md-12 justify-content-center">
      <div class="card">
        <div class="card-body">
          <table class="table table-striped w-100" id="bet-summary-table">
            <thead>
              <tr>
                <th>EVENT DATE</th>
                <th>FIGHT#</th>
                <th>RESULT</th>
                <th>ACTUAL MERON</th>
                <th>ACTUAL WALA</th>
                <th>NET</th>
                <th>DATE</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
    </div>
  </div>
@endsection

@section('additional-scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
  <script src="{{ asset('js/bet-summary.js') }}" defer></script>
@endsection
