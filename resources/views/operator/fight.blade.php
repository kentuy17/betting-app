@extends('layouts.app')

@section('additional-styles')
<link rel="stylesheet" href="{{ asset('css/play-sabong.css') }}" type="text/css">
<link rel="stylesheet" href="{{ asset('css/operator.css') }}" type="text/css">
<link rel="stylesheet" href="https://vjs.zencdn.net/7.8.2/video-js.css"/>
<style>
  .offline-embeds-channel-info-panel {
    background: rgba(0,0,0,.6);
    background: var(--color-background-overlay-alt);
    width: 320px;
    display: none !important;
  }
  video {
    width: 100%;
    height: auto;
  }
  .hide {
    display:none;
  }
  #sabong-aficionado {
    position: relative;
    padding-bottom: 56.25%; /* 16:9 */
    /* padding-top: 25px; */
    height: 0;
  }
  #sabong-aficionado object, #sabong-aficionado iframe {
    position: absolute;
    top: 0;
    left: 0;
    width: 100%;
    height: 100%;
  }
  #video-unavailable {
    background-image: url("{{ asset('img/video-unavailable.webp') }}");
    object-fit: cover;
    background-size: cover;
    width: -webkit-fill-available;
    width: -moz-available;
    height: fit-content;
  }
</style>
@endsection

@section('content')
<div class="max-w-full min-w-full min-h-screen shadow-md bg-os_event_body_black row m-0 g-2" id="play-container">
  <div class="col-md-7 my-1">
    <div class="card mb-0">
      <div id="video-stream-container" class="embed-responsive">
        <div class="bet-bg-head font-bold">{{ $fight->name }}</div>
        <div id="sabong-aficionado">
          <img id="video-unavailable" src="{{ asset('img/video-unavailable.webp') }}">
        </div>
      </div>
    </div>
  </div>
  {{-- <div class="col-md-8" id="fight-component"></div> --}}
  <div id="fight-component" class="col-md-5 mt-0"></div>
  <div class="col-md-12">
    <div class="card">
      <div class="results">
        <div class="bet-result-chart">
          <table id="tblBaccaratResultConsecutive" class="cell-border w-100 dataTable no-footer">
              <tbody></tbody>
          </table>
        </div>
      </div>
    </div>
  </div>
</div>

<style>
  .fight-container {
    display: flex;
    justify-content: space-between;
  }
  .total-bets {
    color: blue;
    text-align: center;
    font-weight: bold;
  }
  .win-chance {
    text-align: center;
    font-weight: bold;
  }
  button:disabled {
    cursor: not-allowed;
    pointer-events: all !important;
  }
  .results .bet-result-chart {
    /* max-width: 720px; */
    overflow-x: auto;
    max-height: 300px !important;
    margin: 2rem 0;
  }
</style>
@endsection

@section('additional-scripts')
@vite('resources/js/fight-vue.js')
@vite('public/js/fight.js')
<script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
<script src="https://vjs.zencdn.net/7.8.2/video.js"></script>
<script src= "https://player.twitch.tv/js/embed/v1.js"></script>
<script>
  try {
    var options = {
      channel: "sabongaficionado", // TODO: Change this to the streams username you want to embed
      width: 640,
      height: 360,
      controls: false,
      muted: false,
      allowfullscreen: true,
    };

    var player = new Twitch.Player("sabong-aficionado", options);

    player.addEventListener(Twitch.Player.READY, initiate)
    player.setVolume(0.5);
    player.setMuted(false);

    function initiate() {
      player.addEventListener(Twitch.Player.ONLINE, handleOnline);
      player.addEventListener(Twitch.Player.OFFLINE, handleOffline);
      player.removeEventListener(Twitch.Player.READY, initiate);
    }

    function handleOnline() {
      $('#sabong-aficionado').find('iframe').css('z-index','1')
      player.removeEventListener(Twitch.Player.ONLINE, handleOnline);
      player.addEventListener(Twitch.Player.OFFLINE, handleOffline);
      player.setMuted(false);
    }

    function handleOffline() {
      $('#sabong-aficionado').find('iframe').css('z-index','-100')
      player.removeEventListener(Twitch.Player.OFFLINE, handleOffline);
      player.addEventListener(Twitch.Player.ONLINE, handleOnline);
      player.setMuted(true);
    }
  } catch (error) {
    console.log(error);
  }
</script>
@endsection
