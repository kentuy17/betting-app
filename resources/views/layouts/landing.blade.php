@extends('layouts.app')
@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('plugins/welome-popup.css') }}">
@endsection

@section('content')
  <div class="bg-os_bg min-h-full mt-4">
    <div class="px-4">
      @include('layouts.flash-message')
      <div class="bg-os_black border p-2 mb-8">
        <div class="flex px-2 justify-between">
          @if ($is_online)
            <h3 class="text-red-500 font-bold text-sm">LIVE</h3>
            <i class="fa-solid fa-circle-dot text-xs text-os_blinking_green animate-pulse-fast"></i>
          @else
            <h3 class="text-white text-sm">OFFLINE</h3>
            <i class="fa-solid fa-circle-dot text-xs animate-pulse-fast"></i>
          @endif
        </div>
        <h3 class="text-white font-bold text-3xl text-center mb-4">
          SABONG WORLD WIDE
        </h3> <a href="/play" class="">
          <div class="py-3 bg-os_orange text-white text-center text-sm hover:bg-os_menu_yellow cursor-pointer" style="width:100% !important;">
            <h3 class="font-bold"><i class="fa-solid fa-arrow-up-right-from-square"></i> ENTER GAME CONSOLE
            </h3>
          </div>
        </a>
      </div>
      @if (in_array('Cash-in Operator', $permissions))
        <div class="bg-os_black border p-2 mb-8">
          <div class="flex px-2 justify-between">
            <h3 class="text-white text-sm">ONLINE</h3>
            <i class="fa-solid fa-circle-dot text-xs animate-pulse-fast"></i>
          </div>
          <h3 class="text-white font-bold text-3xl text-center mb-4">
            TRANSACTIONS
          </h3>
          <div class="grid gap-2 grid-cols-2">
            <a href="/transactions" class="">
              <div class="py-3 bg-os_orange text-white text-center text-sm hover:bg-os_menu_yellow cursor-pointer" style="width:100% !important;">
                <h3 class="font-bold"><i class="fa-solid fa-arrow-up-right-from-square"></i> ENTER
                </h3>
              </div>
            </a>
            <button type="button" data-bs-toggle="modal" data-bs-target="#mop-modal" data-bs-config={backdrop:true}>
              <div class="py-3 bg-os_orange text-white text-center text-sm hover:bg-os_menu_yellow cursor-pointer" style="width:100% !important;">
                <h3 class="font-bold"><i class="fa-solid fa-arrow-up-right-from-square"></i> CHANGE MOP
                </h3>
              </div>
            </button>
          </div>
        </div>
      @endif
      @if ($master_agent)
        <div class="bg-os_black border p-2 mb-8">
          <div class="flex px-2 justify-between">
            <h3 class="text-white text-sm">MASTER AGENT</h3>
            <i class="fa-solid fa-circle-dot text-xs animate-pulse-fast"></i>
          </div>
          <h3 class="text-white font-bold text-3xl text-center mb-4">
            DASHBOARD
          </h3> <a href="/master-agent" class="">
            <div class="py-3 bg-os_orange text-white text-center text-sm hover:bg-os_menu_yellow cursor-pointer" style="width:100% !important;">
              <h3 class="font-bold"><i class="fa-solid fa-arrow-up-right-from-square"></i> ENTER DASHBOARD
              </h3>
            </div>
          </a>
        </div>
      @endif
      <div class="bg-os_black border p-2 mb-8" style="display: none;">
        <div class="flex px-2 justify-between">
          <h3 class="text-red-500 font-bold text-sm">LIVE</h3>
          <i class="fa-solid fa-circle-dot text-xs text-os_blinking_green animate-pulse-fast"></i>
        </div>
        <h3 class="text-white font-bold text-3xl text-center mb-4">
          ACTION MOVIE
        </h3> <a href="/watch/movie" class="">
          <div class="py-3 bg-os_orange text-white text-center text-sm hover:bg-os_menu_yellow cursor-pointer" style="width:100% !important;">
            <h3 class="font-bold"><i class="fa-solid fa-arrow-up-right-from-square"></i> ENTER MOVIE CONSOLE
            </h3>
          </div>
        </a>
      </div>
    </div>
  </div>
  <div class="modal fade" id="mop-modal" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
    <div class="modal-dialog modal-dialog-top" role="document">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="exampleModalLongTitle">CHANGE MOP</h5>
          <button type="button" class="close" data-bs-dismiss="modal" aria-label="Close">
            <span aria-hidden="true">&times;</span>
          </button>
        </div>
        <form id="change-mop-form" method="post" action="/transaction/change-mop">
          <input type="hidden" name="_token" value="{{ csrf_token() }}" />
          <div class="modal-body">
            <div class="form-check">
              <input class="form-check-input" type="radio" name="change_mop" id="nica_radio" value="nica" @if ($mop == '09272306987') checked @endif>
              <label class="form-check-label" for="nica_radio">
                <span class="font-bold">KY*E B.</span> - <span class="font-light italic">09272306987</span>
              </label>
            </div>
            <div class="form-check mt-2">
              <input class="form-check-input" type="radio" name="change_mop" id="boyet_radio" value="boyet" @if ($mop == '09954775395') checked @endif>
              <label class="form-check-label" for="boyet_radio">
                <span class="font-bold">ME***R Z.</span> - <span class="font-light italic">09954775395</span>
              </label>
            </div>
          </div>
          <div class="modal-footer">
            <a class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Cancel</a>
            <input type="submit" class="btn btn-primary bg-slate-900 btn-sm" value="Submit">
          </div>
        </form>
      </div>
    </div>
  </div>
@endsection

@section('additional-scripts')
  <script src="{{ asset('plugins/jquery.ihavecookies.min.js') }}"></script>
  <script>
    var options = {
      title: '<i class="fa-solid fa-coins yellow"></i> No more points?',
      message: 'Go to Cash-in page to add points',
      delay: 600,
      expires: 1,
      link: '../deposit',
      onAccept: function() {
        var myPreferences = $.fn.ihavecookies.cookie();
        window.location.href = '/deposit';
      },
      uncheckBoxes: true,
      acceptBtnLabel: 'Cash-in Now!',
      moreInfoLabel: 'More information',
    }

    $(function() {
      let userAgent = navigator.userAgent;
      getPoints().then((points) => {
        if (points < '100' && !userAgent.includes('iOS')) {
          $('body').ihavecookies(options);
        }
      }).then(() => {
        setTimeout(() => {
          $('#gdpr-cookie-advanced').css('display', 'none')
        }, 1000);
      })
    })

    async function getPoints() {
      const response = await fetch('/user/points');
      const data = await response.json();
      return data.points;
    }
  </script>
@endsection
