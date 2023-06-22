@extends('layouts.app')
@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('css/operator.css') }}">
@endsection
@section('content')
  @php
    $cashin = $cashout = '';
    switch (Auth::user()->user_role->name) {
      case 'Cash-in Operator':
        $cashin = 'active';
        break;
      case 'Cash-out Operator':
        $cashout = 'active';
        break;
      default:
        $cashin = 'active';
        break;
    }
  @endphp
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12">
        <ul class="nav nav-tabs" id="myTab" role="tablist">
          @if (hasAccess('Cash-in Operator'))
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ $cashin }}" id="deposit-tab" data-bs-toggle="tab" data-bs-target="#deposit-panel" type="button" role="tab" aria-controls="deposit" aria-selected="true">
                DEPOSIT <span id="badge-deposit" style="display: none;" class="badge bg-danger">0</span></button>
              {{-- <button onclick="showNotification('fuck yeah!')">Show Notif</button> --}}
            </li>
          @endif
          @if (hasAccess('Cash-out Operator'))
            <li class="nav-item" role="presentation">
              <button class="nav-link {{ $cashout }}" id="withdraw-tab" data-bs-toggle="tab" data-bs-target="#withdraw" type="button" role="tab" aria-controls="withdraw" aria-selected="false">
                WITHDRAW <span id="badge-withdraw" style="display: none;" class="badge bg-danger">0</span>
                <span id="badge-withdraw-unverified" data-bs-toggle="tooltip" title="Missing Ref-code" style="display: none;" class="badge bg-warning">0</span></button>
            </li>
          @endif
          <li class="nav-item credit-nav-item">
            <div class="nav-credits-wr w-25 w-sm-50 gold-text">
              <a href="/refillpoints" class="d-flex align-items-center justify-content-end gp-credits">
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
        </ul>
        @if (Auth::user()->user_role->name == 'Cash-in Operator' || hasAccess('Cash-in Operator'))
          <div class="tab-content" id="myTabContent">
            <div class="tab-pane fade show {{ $cashin }}" id="deposit-panel" role="tabpanel" aria-labelledby="deposit-tab">
              <table class="table dt-responsive table-striped nowrap w-100" id="deposit-trans-table">
                <thead>
                  <tr>
                    <th>#</th>
                    <th>Player</th>
                    <th>Outlet</th>
                    <th>Amount</th>
                    <th>Mobile#</th>
                    <th>Operator</th>
                    <th>Ref Code</th>
                    <th>Date</th>
                    <th>Status</th>
                    <th>Action</th>
                  </tr>
                </thead>
              </table>
            </div>
        @endif
        @if (Auth::user()->user_role->name == 'Cash-out Operator' || hasAccess('Cash-out Operator'))
          <div class="tab-pane fade show {{ $cashout }}" id="withdraw" role="tabpanel" aria-labelledby="withdraw-tab">
            <table class="table dt-responsive table-striped nowrap w-100" id="withdraw-trans-table">
              <thead>
                <tr>
                  <th>#</th>
                  <th>Player</th>
                  <th>Outlet</th>
                  <th>Amount</th>
                  <th>Mobile#</th>
                  <th>Processed By</th>
                  <th>Ref Code</th>
                  <th>Date</th>
                  <th>Status</th>
                  <th>Action</th>
                </tr>
              </thead>
            </table>
          </div>
        @endif
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
              <input type="text" class="form-control" required id="trans-pts" placeholder="Points" onkeyup="javascript:this.value=Comma(this.value);">
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
  <!-- Withdraw Modal -->
  <div class="modal fade" id="withdraw-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="withdraw-form" method="post">
          <div class="modal-body">
            <input type="hidden" name="withdraw-id" id="withdraw-id">
            <div class="form-group mt-2">
              <label for="withdraw-ref-code">Ref-code</label>
              <input type="text" class="form-control" id="withdraw-ref-code" placeholder="Ref-code">
            </div>
            <div class="form-group mt-2">
              <label for="withdraw-action">Action</label>
              <select name="withdraw-action" class="form-control" id="withdraw-action">
                <option value="approve">APPROVE</option>
                <option value="reject">REJECT</option>
                <option value="update">UPDATE</option>
              </select>
            </div>
            <div class="form-group mt-2" style="display: none">
              <label for="withdraw-note">Note:</label>
              <textarea name="withdraw-note" class="form-control" id="withdraw-note" cols="30" rows="1"></textarea>
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
  <!--revert points-->
  <div class="modal fade" id="modal-undo-points" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="exampleModalLongTitle">UNDO POINTS</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="deposit-undo-form" method="post">
          <div class="modal-body">
            <input type="hidden" name="undo-id" id="undo-id">
            <div class="form-group align-center">
              <img id="trans-receipt-undo" data-storage="{{ asset('storage/') }}" src="" alt="">
            </div>
            <div class="form-group mt-2">
              <label for="ref-code-undo">Ref-code</label>
              <input type="text" class="form-control disabled" id="ref-code-undo">
            </div>
            <div class="form-group mt-2">
              <label for="trans-pts-undo">Current Points</label>
              <input type="text" class="form-control disabled" id="trans-pts-undo">
            </div>
            <div class="form-group mt-2">
              <label for="updated-trans-pts">Updated Points</label>
              <input type="text" class="form-control" required id="updated-trans-pts" placeholder="Points" onkeyup="javascript:this.value=Comma(this.value);">
            </div>
            <div class="form-group mt-2">
              <label for="trans-note-undo">Note:</label>
              <textarea name="trans-note" class="form-control" id="trans-note-undo" cols="30" rows="1"></textarea>
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
  <!-- PLEYAH BET HISTORY -->
  <div class="modal fade" id="bethistory-modal" tabindex="-1" role="dialog" aria-labelledby="BET HISTORY" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">
        <div class="modal-body">
          <table class="table dt-responsive table-striped nowrap w-100" id="bethistory-table">
            <thead>
              <tr>
                <th>FIGHT #</th>
                <th>SIDE</th>
                <th>BET AMOUNT</th>
                <th>PERCENT %</th>
                <th>STATUS</th>
                <th>WIN AMOUNT</th>
                <th>CURRENT POINTS</th>
                <th>DATE</th>
              </tr>
            </thead>
          </table>
        </div>
        <div class="modal-footer">
          <a class="btn btn-secondary btn-sm" data-dismiss="modal">CLOSE</a>
        </div>
      </div>
    </div>
  </div>
@endsection
@section('additional-scripts')
  <script>
    function Comma(Num) { //function to add commas to textboxes
      Num += '';
      Num = Num.replace(',', '');
      Num = Num.replace(',', '');
      Num = Num.replace(',', '');
      Num = Num.replace(',', '');
      Num = Num.replace(',', '');
      Num = Num.replace(',', '');
      x = Num.split('.');
      x1 = x[0];
      x2 = x.length > 1 ? '.' + x[1] : '';
      var rgx = /(\d+)(\d{3})/;
      while (rgx.test(x1))
        x1 = x1.replace(rgx, '$1' + ',' + '$2');
      return x1 + x2;
    }
  </script>
  <script src="{{ asset('js/transactions.js') }}" defer></script>
  <script src="{{ asset('js/withdraw.js') }}" defer></script>
@endsection
