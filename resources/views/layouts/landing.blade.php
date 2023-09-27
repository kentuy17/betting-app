@extends('layouts.app')
@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('plugins/welome-popup.css') }}">
@endsection

@section('content')
  <div class="bg-os_bg min-h-full mt-4">
    <div class="px-4">
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
      @if (Auth::user()->role_id == 1)
        <div class="bg-os_black border p-2 mb-8">
          <div class="flex px-2 justify-between">
            <h3 class="text-white text-sm">ONLINE</h3>
            <i class="fa-solid fa-circle-dot text-xs animate-pulse-fast"></i>
          </div>
          <h3 class="text-white font-bold text-3xl text-center mb-4">
            TRANSACTIONS
          </h3> <a href="/transactions" class="">
            <div class="py-3 bg-os_orange text-white text-center text-sm hover:bg-os_menu_yellow cursor-pointer" style="width:100% !important;">
              <h3 class="font-bold"><i class="fa-solid fa-arrow-up-right-from-square"></i> ENTER TRANS PAGE
              </h3>
            </div>
          </a>
        </div>
      @endif
      @if ($agent->is_master_agent)
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
      getPoints().then((points) => {
        if (points < '100') {
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
