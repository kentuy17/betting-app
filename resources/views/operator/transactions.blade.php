@extends('layouts.app')
@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="col-md-12">
      <ul class="nav nav-tabs" id="myTab" role="tablist">
        <li class="nav-item" role="presentation">
          <button class="nav-link active" id="deposit-tab" data-bs-toggle="tab" data-bs-target="#deposit-panel" type="button" role="tab" aria-controls="deposit" aria-selected="true">Deposits</button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="withdraw-tab" data-bs-toggle="tab" data-bs-target="#withdraw" type="button" role="tab" aria-controls="withdraw" aria-selected="false">Withdrawals</button>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="deposit-panel" role="tabpanel" aria-labelledby="deposit-tab">
          <table class="table dt-responsive table-striped nowrap w-100" id="deposit-trans-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Type</th>
                <th>Player</th>
                <th>Operator</th>
                <th>Amount</th>
                <th>Mobile#</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="tab-pane fade" id="withdraw" role="tabpanel" aria-labelledby="withdraw-tab">
          <table class="table dt-responsive table-striped nowrap w-100" id="withdraw-trans-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Type</th>
                <th>Player</th>
                <th>Operator</th>
                <th>Amount</th>
                <th>Mobile#</th>
                <th>Date</th>
                <th>Status</th>
                <th>Action</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>
      
    </div>
  </div>
</div>
<!-- Modal -->
<div class="modal fade" id="modal-center" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="deposit-form" method="post">
        <div class="modal-body">
          <input type="hidden" name="trans-id" id="trans-id">
          <div class="form-group align-center">
            <img id="trans-receipt" data-storage="{{ asset('storage/') }}" src="" alt="">
          </div>
          <div class="form-group">
            <label for="ref-code">Ref-code</label>
            <input type="text" class="form-control" required id="ref-code" placeholder="Ref-code">
          </div>
          <div class="form-group">
            <label for="trans-pts">Points</label>
            <input type="number" class="form-control" required id="trans-pts" placeholder="Points">
          </div>
        </div>
        <div class="modal-footer">
          <button class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</button>
          <input type="submit" class="btn btn-primary bg-slate-900 btn-sm" value="Submit">
        </div>
      </form>
    </div>
  </div>
</div>


@endsection
@section('additional-scripts')
{{-- <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script> --}}
<script src="{{ asset('js/transactions.js') }}" defer></script>
<script src="{{ asset('js/withdraw.js') }}" defer></script>
@endsection
