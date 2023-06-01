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
          <button class="nav-link active" id="admin-users-tab" data-bs-toggle="tab" data-bs-target="#users-panel" type="button" role="tab" aria-controls="deposit" aria-selected="true">
            USERS <span id="badge-online-users" style="display: none;" class="badge bg-success">0</span></button>
        </li>
        <li class="nav-item credit-nav-item">
          <div class="nav-credits-wr w-sm-50 gold-text">
            <a href="#" class="d-flex align-items-center justify-content-end gp-credits">
              <div class="bg-success add-btn">
                Create New User
              </div>
            </a>
          </div>
        </li>
      </ul>
      <div class="tab-content" id="myTabContent">
        <div class="tab-pane fade show active" id="users-panel" role="tabpanel" aria-labelledby="admin-users-tab">
          <table class="table dt-responsive table-striped nowrap w-100" id="admin-users-table">
            <thead>
              <tr>
                <th>#</th>
                <th>USERNAME</th>
                <th>PHONE #</th>
                <th>ROLES</th>
                <th>POINTS</th>
                <th>STATUS</th>
                <th>CREATED AT</th>
                <th>ACTION</th>
              </tr>
            </thead>
          </table>
        </div>
      </div>

    </div>
  </div>
</div>
<!-- Deposit Modal -->
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
          <div class="form-group mt-2">
            <label for="ref-code">Ref-code</label>
            <input type="text" class="form-control" required id="ref-code" placeholder="Ref-code">
          </div>
          <div class="form-group mt-2">
            <label for="trans-pts">Points</label>
            <input type="text" class="form-control" required id="trans-pts" placeholder="Points" onkeyup = "javascript:this.value=Comma(this.value);" >
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
@endsection
@section('additional-scripts')
<script src="{{ asset('js/users.js') }}" defer></script>
@endsection
