@extends('layouts.app-sub')

@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('css/play-sabong.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/operator.css') }}" type="text/css">
  {{-- <link rel="stylesheet" href="https://vjs.zencdn.net/7.8.2/video-js.css" /> --}}
  {{-- <link href="https://unpkg.com/video.js@7/dist/video-js.min.css" rel="stylesheet"> --}}
  {{-- <link href="https://unpkg.com/silvermine-videojs-quality-selector@1.1.2/dist/css/quality-selector.css" rel="stylesheet"> --}}
  <style>
    .offline-embeds-channel-info-panel {
      background: rgba(0, 0, 0, .6);
      background: var(--color-background-overlay-alt);
      width: 320px;
      display: none !important;
    }

    video {
      width: 100%;
      height: auto;
    }

    .hide {
      display: none;
    }

    #sabong-aficionado {
      position: relative;
      padding-bottom: 56.25%;
      /* 16:9 */
      /* padding-top: 25px; */
      height: 0;
    }

    #sabong-aficionado object,
    #sabong-aficionado iframe {
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

    /* #clappr{ width: 100%;height: 100%;position: relative; min-height: 320px; margin-bottom: 25px;} */
    /* #clappr > div{ width:100%;height:100%;position: absolute;} */
    #header-fight {
      display: flex;
      justify-content: space-between;
      flex-direction: row;
    }

    body {
      margin: 0 !important;
    }
  </style>
@endsection

@section('content')
  <div class="max-w-full min-w-full min-h-screen shadow-md bg-os_event_body_black row m-0 g-2" id="play-container">
    <div class="col-md-7 my-1">
      <div class="card mb-0">
        <div id="video-stream-container" class="embed-responsive">
          <div id="header-fight" class="bet-bg-head font-bold">
            <div id="event-name" style="display:none;">{{ $fight->name }}</div>
            <div id="header-closed" class="bet-bg-head font-bold" style="display: none;">EVENT CLOSED</div>
            <div class="form-check form-switch">
              <label class="text-black mr-2" id="jeno-label" for="switch-video-display"></label>
              <input class="form-check-input" type="checkbox" id="switch-video-display" @if ($setting) checked @endif>
            </div>
          </div>
          <img id="poster-img" style="display: none;" src="{{ asset('img/poster.png') }}" alt="10-streak-win-promo">

          {{-- DaCast --}}
          {{-- <div id="mux-player" style="width:100%;height:0px;position:relative;padding-bottom:56.25%;">
            <script id="a3e4275e-c022-b1b4-c231-3bef437168da-live-11b41ea8-e33c-6818-4331-1cb7d38e87cf" width="100%" height="100%" src="https://player.dacast.com/js/player.js?contentId=a3e4275e-c022-b1b4-c231-3bef437168da-live-11b41ea8-e33c-6818-4331-1cb7d38e87cf" class="dacast-video"></script>
          </div> --}}
          {{-- allow="autoplay;encrypted-media" autoplay  --}}
          <div style="position:relative;padding-bottom:56.25%;overflow:hidden;height:0;max-width:100%;">
            <iframe id="a3e4275e-c022-b1b4-c231-3bef437168da-live-11b41ea8-e33c-6818-4331-1cb7d38e87cf" src="https://iframe.dacast.com/live/a3e4275e-c022-b1b4-c231-3bef437168da/11b41ea8-e33c-6818-4331-1cb7d38e87cf" width="100%" height="100%" frameborder="0" scrolling="no" style="position:absolute;top:0;left:0;"></iframe>
          </div>

          {{-- OneStream --}}
          {{-- <div id="mux-player" style="width:100%;height:0px;position:relative;padding-bottom:56.25%;">
            <iframe src="https://player.onestream.live/embed?token=ZF9hdXRoXzIwNzg4MDRfMXR0NDJtZnM0&type=psk" style="position:absolute;width:100%;height:100%;" scrolling="no" frameborder="0" allow="autoplay" allowfullscreen> </iframe>
          </div> --}}

          {{-- Wowza --}}
          {{-- <div id="mux-player" style="width:100%;height:0px;position:relative;padding-bottom:56.25%;">
            <video id="videojs" class="video-js vjs-fluid vjs-default-skin vjs-big-play-centered" controls preload="auto" autoplay muted>
              <source src="https://5caf24a595d94.streamlock.net:1937/urdzwgjrqg/urdzwgjrqg/playlist.m3u8" type="application/x-mpegURL">
              <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a web browser that
                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
              </p>
            </video>
          </div> --}}

          {{-- 5cent --}}
          {{-- <div id="mux-player" style="width:100%;height:0px;position:relative;padding-bottom:56.25%;">
            <iframe src="https://iframes.5centscdn.com/videojs/hls/aHR0cHM6Ly9vcWdkcmdhemw0cm0taGxzLWxpdmUuNWNlbnRzY2RuLmNvbS9zd3cvZDBkYmU5MTUwOTFkNDAwYmQ4ZWU3ZjI3ZjA3OTEzMDMuc2RwL3BsYXlsaXN0Lm0zdTg=" frameBorder="0" allow="autoplay; encrypted-media" allowfullscreen style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px"></iframe>
          </div> --}}
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

  <!-- Deposit Modal -->
  <div class="modal fade" id="modal-settings" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle" aria-hidden="true">
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
  {{-- <script src="{{ asset('js/fight.js') }}" defer></script> --}}
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  {{-- <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
  <script src="https://vjs.zencdn.net/7.8.2/video.js"></script> --}}
  {{-- <script src="https://cdn.jsdelivr.net/npm/@mux/mux-player"></script> --}}
  {{-- <script src="https://unpkg.com/video.js@7/dist/video.min.js"></script> --}}
  {{-- <script type="text/javascript">
    var player = videojs('videojs', {
      autoplay: true
    });
  </script> --}}
  {{-- <script type="text/javascript" src="https://cdn.jsdelivr.net/npm/clappr@latest/dist/clappr.min.js" defer></script> --}}
@endsection
