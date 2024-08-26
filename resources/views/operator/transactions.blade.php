@extends('layouts.app-sub')
@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('css/operator.css') }}">
  <link href="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/css/select2.min.css" rel="stylesheet" />
  <style>
    .receipt-container {
      max-height: 400px;
      max-width: 350px;
      overflow: hidden;
      margin: auto;
    }

    .full-length {
      max-height: 800px !important;
    }

    #trans-receipt {
      /* max-width: 350px;
                                                height: 800px; */
      margin: -40px 0 0 0;
    }

    .modal-content .select2-search {
      pointer-events: auto;
      color: black !important;
    }

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
  <input type="hidden" id="dummy_id" value={{ $dummy->id }} />
  <div class="container">
    <div class="row justify-content-center">
      <div class="col-md-12 card">
        <div class="card-header" style="padding: 5px 5px 0 !important;">
          <ul class="nav-orig nav-tabs" id="myTab" role="tablist">
            @if (hasAccess('Cash-in Operator'))
              <li class="nav-item" role="presentation">
                <button class="text-xs px-1 nav-link {{ $cashin }}" id="deposit-tab" data-bs-toggle="tab" data-bs-target="#deposit-panel" type="button" role="tab" aria-controls="deposit" aria-selected="true">
                  DEPOSIT <span id="badge-deposit" style="display: none;" class="text-xs px-1 py-0 badge bg-danger">0</span>
                  <span id="badge-unpaid" style="display: none;" class="text-xs px-1 py-0 badge bg-warning">0</span></button> {{-- <button
            onclick="showNotification('fuck yeah!')">Show Notif</button> --}}
              </li>
            @endif
            @if (hasAccess('Cash-out Operator'))
              <li class="nav-item" role="presentation">
                <button class="text-xs px-1 nav-link {{ $cashout }}" id="withdraw-tab" data-bs-toggle="tab" data-bs-target="#withdraw" type="button" role="tab" aria-controls="withdraw" aria-selected="false">
                  WITHDRAW <span id="badge-withdraw" style="display: none;" class="text-xs px-1 py-0 badge bg-danger">0</span>
                  <span id="badge-withdraw-unverified" data-bs-toggle="tooltip" title="Missing Ref-code" style="display: none;" class="text-xs badge bg-warning">0</span></button>
              </li>
            @endif
            @if (in_array(Auth::user()->id, [1, 6]) && !session()->has('katok'))
              <li class="nav-item" role="presentation">
                <button class="text-xs px-1 nav-link {{ $cashout }}" id="agent-tab" data-bs-toggle="tab" data-bs-target="#agent" type="button" role="tab" aria-controls="agent" aria-selected="false">
                  AGENT CI <span id="badge-agent" style="display: none;" class="text-xs px-1 py-0 badge bg-danger">0</span>
                  <span id="badge-agent-unverified" data-bs-toggle="tooltip" title="Missing Ref-code" style="display: none;" class="text-xs badge bg-warning">0</span></button>
              </li>
              <li class="nav-item credit-nav-item">
                <button type="button" title="modal-settings" class="btn btn-block btn-sm btn-secondary active" data-bs-toggle="modal" data-bs-target="#settings-modal">
                  <i class="fa-solid fa-gear"></i></button>
              </li>
            @endif
            <li class="nav-item credit-nav-item" @if (Auth::user()->id == 1 && !session()->has('katok')) style="margin-left: 10px;" @endif>
              <button type="button" class="btn btn-block btn-sm btn-success active" data-bs-toggle="modal" data-bs-target="#manual-request-modal">ADD PTS</button>
              {{-- <div class="nav-credits-wr w-25 w-sm-50 gold-text">
                <button type="button" class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#add-pts-modal">
                  <svg class="svg-inline--fa fa-coins fa-w-16" data-prefix="fas" data-icon="coins" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
                    <path fill="currentColor"
                      d="M0 405.3V448c0 35.3 86 64 192 64s192-28.7 192-64v-42.7C342.7 434.4 267.2 448 192 448S41.3 434.4 0 405.3zM320 128c106 0 192-28.7 192-64S426 0 320 0 128 28.7 128 64s86 64 192 64zM0 300.4V352c0 35.3 86 64 192 64s192-28.7 192-64v-51.6c-41.3 34-116.9 51.6-192 51.6S41.3 334.4 0 300.4zm416 11c57.3-11.1 96-31.7 96-55.4v-42.7c-23.2 16.4-57.3 27.6-96 34.5v63.6zM192 160C86 160 0 195.8 0 240s86 80 192 80 192-35.8 192-80-86-80-192-80zm219.3 56.3c60-10.8 100.7-32 100.7-56.3v-42.7c-35.5 25.1-96.5 38.6-160.7 41.8 29.5 14.3 51.2 33.5 60 57.2z">
                    </path>
                  </svg>
                  ADD PTS
                </button>
              </div> --}}
            </li>
          </ul>
        </div>
        <div class="card-body" style="padding: 5px !important;">
          <div class="tab-content" id="myTabContent">
            @if (Auth::user()->user_role->name == 'Cash-in Operator' || hasAccess('Cash-in Operator'))
              <div class="tab-pane fade show {{ $cashin }}" id="deposit-panel" role="tabpanel" aria-labelledby="deposit-tab">
                <table class="table dt-responsive table-striped nowrap w-100" id="deposit-trans-table">
                  <thead>
                    <tr>
                      <th>Player</th>
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
                      {{-- <th>#</th> --}}
                      <th>Player</th>
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
              <div class="tab-pane fade" id="agent" role="tabpanel" aria-labelledby="agent-tab">
                <table class="table dt-responsive table-striped nowrap w-100" id="agent-trans-table">
                  <thead>
                    <tr>
                      <th>Player</th>
                      <th>Amount</th>
                      <th>Mobile#</th>
                      <th>Agent</th>
                      {{-- <th>Ref Code</th> --}}
                      <th>Date</th>
                      {{-- <th>Status</th> --}}
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
  </div>
  </div>
  <!-- Deposit Modal -->
  <div class="modal fade" id="modal-center" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
      <div class="modal-content">
        <div class="modal-header pb-3">
          <h5 class="modal-title" id="exampleModalLongTitle">Modal title</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="deposit-form" method="post">
          <div class="modal-body pt-0">
            <input type="hidden" name="trans-id" id="trans-id">
            <div class="form-group align-center receipt-container">
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
                <option value="update">UPDATE</option>
              </select>
            </div>
            <div class="form-group mt-2" style="display: none">
              <label for="trans-note">Note:</label> {{-- <textarea name="trans-note" class="form-control" id="trans-note"
              cols="30" rows="1"></textarea> --}} <select name="trans-note" id="trans-note" class="form-control">
                <option value="Duplicate receipt">Duplicate receipt</option>
                <option value="Wrong receipt">Wrong receipt</option>
              </select>
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
                <option value="cancel">CANCEL</option>
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
  {{-- Request Utang Modal --}}
  <div class="modal fade" id="manual-request-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Manual Cash-in</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="manual-request-form" method="post">
          <div class="modal-body">
            <div class="form-group row">
              <label for="player-username">
                Player:
                <select class="player-username form-control" id="player-username" required></select>
              </label>
              {{-- <input type="text" class="form-control" id="player-username" placeholder="Player" required> --}}
            </div>
            <div class="form-group mt-2">
              <label for="manual-request-amount">Amount:</label>
              <input type="number" class="form-control" id="manual-request-amount" placeholder="0.00" required>
            </div>
            <div class="form-group mt-2">
              <label for="manual-request-action">Action</label>
              <select name="manual-request-action" class="form-control" id="manual-request-action">
                <option value="utang">UTANG</option>
                <option value="paid">PAID</option>
              </select>
            </div>
            <div class="form-group mt-2" style="display: none">
              <label for="manual-request-note">Ref-code:</label>
              <input type="text" class="form-control" id="manual-request-ref" placeholder="REF-CODE" required>
            </div>
            <div class="form-group mt-2">
              <label for="manual-request-note">Note:</label>
              <textarea name="manual-request-note" class="form-control" required id="manual-request-note" cols="30" rows="1"></textarea>
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
  {{-- Settings Modal --}}
  <div class="modal fade" id="settings-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">Filter Settings</h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="filter-settings-form" method="post">
          <div class="modal-body row">
            <div class="form-group col-sm-6 col-md-6 mb-2">
              <label for="date-from">From:</label>
              <input type="date" class="form-control" id="date-from">
            </div>
            <div class="form-group col-sm-6 col-md-6 mb-2">
              <label for="date-to">To:</label>
              <input type="date" class="form-control" id="date-to">
            </div>
            <div class="form-group mt-2">
              <label>Status:</label>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="filter-status[]" value="pending" id="checkbox-status-pending" checked>
                <label class="form-check-label" for="checkbox-status-pending">
                  Pending
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="filter-status[]" value="completed" id="checkbox-status-completed" checked>
                <label class="form-check-label" for="checkbox-status-completed">
                  Completed
                </label>
              </div>
              <div class="form-check">
                <input class="form-check-input" type="checkbox" name="filter-status[]" value="failed" id="checkbox-status-failed">
                <label class="form-check-label" for="checkbox-status-failed">
                  Failed
                </label>
              </div>
            </div>
            @if (Auth::user()->id === 1)
              <div class="form-group mt-2">
                <label>Morph:</label>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="filter-morph[]" value="0" id="checkbox-morph-0" checked>
                  <label class="form-check-label" for="checkbox-morph-0">
                    All
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="filter-morph[]" value="1" id="checkbox-morph-1" checked>
                  <label class="form-check-label" for="checkbox-morph-1">
                    Player
                  </label>
                </div>
                <div class="form-check">
                  <input class="form-check-input" type="checkbox" name="filter-morph[]" value="2" id="checkbox-morph-2">
                  <label class="form-check-label" for="checkbox-morph-2">
                    CSR
                  </label>
                </div>
              </div>
            @endif
          </div>
          <div class="modal-footer">
            <a class="btn btn-secondary btn-sm" data-dismiss="modal">Close</a>
            <input type="submit" class="btn btn-primary bg-slate-900 btn-sm" value="Apply">
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
        <div class="modal-header">
          <h5 id="bethistory-head"></h5>
          <button type="button" class="close" data-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <div class="modal-body pt-0">
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
        <div class="modal-footer"> {{-- <a class="btn btn-secondary btn-sm" data-dismiss="modal">CLOSE</a> --}} </div>
      </div>
    </div>
  </div>
@endsection
@section('additional-scripts')
  <script src="https://cdnjs.cloudflare.com/ajax/libs/moment.js/2.29.4/moment.min.js"></script>
  <script src="https://cdn.jsdelivr.net/npm/select2@4.1.0-rc.0/dist/js/select2.min.js"></script>
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
    const DUMMY_ID = document.getElementById('dummy_id').value;

    $('#trans-receipt').on('click', function() {
      $('.receipt-container').toggleClass('full-length');
    })

    $(document).ready(function() {
      $("#player-username").select2({
        width: '100%',
        dropdownParent: $("#manual-request-modal .modal-content"),
        ajax: {
          url: '/transaction/palautang',
          dataType: 'json',
          type: "GET",
          data: function(params) {
            var query = {
              term: params.term,
            }

            // Query parameters will be ?search=[term]&type=public
            return query;
          },
          processResults: function(data) {
            return {
              results: $.map(data.data, function(item) {
                return {
                  text: item.username,
                  id: item.id
                }
              })
            };
          }
        }
      });
    });
  </script>
  @vite('public/js/transactions.js')
  {{-- @vite('public/js/withdraw.js') --}}
  @vite('public/js/topups.js')

  {{-- <script src="{{ asset('js/transactions.js') }}" defer></script> --}}
  <script src="{{ asset('js/withdraw.js') }}" defer></script>
  {{-- <script src="{{ asset('js/topups.js') }}" defer></script> --}}
@endsection
