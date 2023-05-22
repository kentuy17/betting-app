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
          <button class="nav-link active" id="refill-tab" data-bs-toggle="tab" data-bs-target="#refill-panel" type="button" role="tab" aria-controls="refill" aria-selected="true">
            Refill <span id="badge-refill" style="display: none;" class="badge bg-danger">0</span></button>
        </li>
        <li class="nav-item" role="presentation">
          <button class="nav-link" id="remit-tab" data-bs-toggle="tab" data-bs-target="#remit" type="button" role="tab" aria-controls="remit" aria-selected="false">
            Remit <span id="badge-remit" style="display: none;" class="badge bg-danger">0</span> 
            <span id="badge-remit-unverified" data-bs-toggle="tooltip" title="Missing Ref-code" style="display: none;" class="badge bg-warning">4</span></button>
        </li>
        <li class="nav-item credit-nav-item">
          <div class="nav-credits-wr w-25 w-sm-50 gold-text">
            <a href="/platform/refill" class="d-flex align-items-center justify-content-end gp-credits">
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
        <div class="tab-pane fade show active" id="refill-panel" role="tabpanel" aria-labelledby="refill-tab">
          <table class="table dt-responsive table-striped nowrap w-100" id="refill-trans-table">
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
        <div class="tab-pane fade" id="remit" role="tabpanel" aria-labelledby="remit-tab">
          <table class="table dt-responsive table-striped nowrap w-100" id="remit-trans-table">
            <thead>
              <tr>
                <th>#</th>
                <th>Type</th>
                <th>Player</th>
                <th>Processed By</th>
                <th>Amount</th>
                <th>Ref Code</th>
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
<!-- Refill Modal -->
<div class="modal fade" id="modal-center" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="refill-form" method="post">
        <div class="modal-body">
          <input type="hidden" name="trans-id" id="trans-id">
          <div class="form-group align-center">
            <img id="trans-receipt" data-storage="{{ asset('storage/') }}" src="" alt="">
          </div>
          <div class="form-group mt-2">
            <label for="ref-code">Ref-code</label>
            <input type="text" class="form-control" required id="ref-code" placeholder="Ref-code">
          </div>
          <div class="form-group mt-2">
            <label for="trans-pts">Points</label>
            <input type="number" class="form-control" required id="trans-pts" placeholder="Points">
          </div>
          <div class="form-group mt-2">
            <label for="trans-action">Action</label>
            <select name="trans-action" class="form-control" id="trans-action">
              <option value="approve">APPROVE</option>
              <option value="reject">REJECT</option>
            </select>
          </div>
          <div class="form-group mt-2" style="display: none">
            <label for="trans-note">Note:</label>
            <textarea name="trans-note" class="form-control" id="trans-note" cols="30" rows="1"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <a class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</a>
          <input type="submit" class="btn btn-primary bg-slate-900 btn-sm" value="Submit">
        </div>
      </form>
    </div>
  </div>
</div>

<!-- remit Modal -->
<div class="modal fade" id="remit-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
  <div class="modal-dialog modal-dialog-top" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
        <button type="button" class="close" data-dismiss="modal" aria-label="Close">
          <span aria-hidden="true">&times;</span>
        </button>
      </div>
      <form id="remit-form" method="post">
        <div class="modal-body">
          <input type="hidden" name="remit-id" id="remit-id">
          <div class="form-group mt-2">
            <label for="remit-ref-code">Ref-code</label>
            <input type="text" class="form-control" id="remit-ref-code" placeholder="Ref-code">
          </div>
          <div class="form-group mt-2">
            <label for="remit-action">Action</label>
            <select name="remit-action" class="form-control" id="remit-action">
              <option value="approve">APPROVE</option>
              <option value="reject">REJECT</option>
              <option value="update">UPDATE</option>
            </select>
          </div>
          <div class="form-group mt-2" style="display: none">
            <label for="remit-note">Note:</label>
            <textarea name="remit-note" class="form-control" id="remit-note" cols="30" rows="1"></textarea>
          </div>
        </div>
        <div class="modal-footer">
          <a class="btn btn-secondary btn-sm" data-dismiss="modal">Cancel</a>
          <input type="submit" class="btn btn-primary bg-slate-900 btn-sm" value="Submit">
        </div>
      </form>
    </div>
  </div>
</div>
@endsection
@section('additional-scripts')
<script src="{{ asset('js/refill.js') }}" defer></script>
<script src="{{ asset('js/remit.js') }}" defer></script>
@endsection
