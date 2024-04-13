@extends('layouts.app')

@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('css/play-sabong.css') }}" type="text/css">
  <link rel="stylesheet" href="{{ asset('css/operator.css') }}" type="text/css">
  {{-- <link rel="stylesheet" href="https://vjs.zencdn.net/7.8.2/video-js.css" /> --}}
  <link href="https://unpkg.com/video.js@7/dist/video-js.min.css" rel="stylesheet">
  <link href="https://unpkg.com/silvermine-videojs-quality-selector@1.1.2/dist/css/quality-selector.css" rel="stylesheet">

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
      padding-bottom: 62.375%;
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
      /* background-image: url("{{ asset('img/video-unavailable.webp') }}"); */
      background-image: url("{{ asset('img/10-streak-win-promo-poster.png') }}");
      object-fit: cover;
      background-size: cover;
      width: -webkit-fill-available;
      width: -moz-available;
      height: fit-content;
    }

    /* #clappr{ width: 100%;height: 100%;position: relative; min-height: 320px; margin-bottom: 25px;}
                                                                                          #clappr > div{ width:100%;height:100%;position: absolute;} */
    #play-container {
      display: flex;
      align-content: flex-start;
    }

    #event-name,
    #header-closed {
      overflow: hidden;
      height: 20px;
    }

    .running-text,
    .running-text-closed {
      position: absolute;
      white-space: nowrap;
      animation: floatText 15s infinite ease-in-out;
    }

    @-webkit-keyframes floatText {
      from {
        right: -50%;
      }

      to {
        right: 100%;
      }
    }
  </style>
@endsection

@section('content')
  <div class="max-w-full min-w-full min-h-screen shadow-md bg-os_event_body_black row m-0 g-2" id="play-container">
    <div class="col-md-12 my-0">
      <div class="card mb-0">
        <div id="video-stream-container" class="embed-responsive">
          <input type="hidden" id="video-display" value="{{ $video_display }}">
          <div class="bet-bg-head font-bold" id="event-name">
            <p class="running-text">{{ $fight->name }}</p>
          </div>
          <div id="header-closed" class="bet-bg-head font-bold" style="display: none;">
            <p class="running-text-closed">EVENT CLOSED</p>
          </div>
          <img id="poster-img" style="display: none;" src="{{ asset('img/poster.png') }}" alt="10-streak-win-promo">

          {{-- DaCast --}}
          {{-- <div id="mux-player" style="width:100%;height:0px;position:relative;padding-bottom:56.25%;">
            <script id="a3e4275e-c022-b1b4-c231-3bef437168da-live-11b41ea8-e33c-6818-4331-1cb7d38e87cf" width="100%" height="100%" src="https://player.dacast.com/js/player.js?contentId=a3e4275e-c022-b1b4-c231-3bef437168da-live-11b41ea8-e33c-6818-4331-1cb7d38e87cf" class="dacast-video"></script>
          </div> --}}

          <div id="mux-player" class="mx-[-2]" style="width:100%;height:0px;position:relative;padding-bottom:65.25%;">
            <iframe id="videojs" src="https://player.onestream.live/embed?token=MjA3ODgwNA==&type=up" style="overflow:hidden; overflow-x:hidden; overflow-y:hidden; height:100%; width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px" scrolling="no" frameborder="0" allow="autoplay;" allow-same-origin="true" allowfullscreen> </iframe>
          </div>

          {{-- Onestream --}}
          {{-- <div id="mux-player" style="width:100%;height:0px;position:relative;padding-bottom:56.25%;">
            <iframe src="https://player.onestream.live/embed?token=ZF9hdXRoXzIwNzg4MDRfMXR0NDJtZnM0&type=psk" style="position:absolute;width:100%;height:100%;" scrolling="no" frameborder="0" allow="autoplay" allowfullscreen> </iframe>
          </div> --}}

          {{-- Wowza --}}
          {{-- <div id="mux-player" style="width:100%;height:0px;position:relative;padding-bottom:56.25%;">
            <video id="videojs" class="video-js vjs-fluid vjs-default-skin vjs-big-play-centered" controls preload="auto" autoplay muted playsinline fluid="true">
              <source src="https://5caf24a595d94.streamlock.net:1937/urdzwgjrqg/urdzwgjrqg/playlist.m3u8" type="application/x-mpegURL">
              <p class="vjs-no-js">
                To view this video please enable JavaScript, and consider upgrading to a web browser that
                <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a>
              </p>
            </video>
          </div> --}}

          {{-- 5Cent --}}
          {{-- <div id="mux-player" style="width:100%;height:0px;position:relative;padding-bottom:56.25%;">
            <iframe src="https://iframes.5centscdn.com/videojs/hls/aHR0cHM6Ly9vcWdkcmdhemw0cm0taGxzLWxpdmUuNWNlbnRzY2RuLmNvbS9zd3cvZDBkYmU5MTUwOTFkNDAwYmQ4ZWU3ZjI3ZjA3OTEzMDMuc2RwL3BsYXlsaXN0Lm0zdTg=" frameBorder="0" allow="autoplay; encrypted-media" allowfullscreen style="overflow:hidden;overflow-x:hidden;overflow-y:hidden;height:100%;width:100%;position:absolute;top:0px;left:0px;right:0px;bottom:0px"></iframe>
          </div> --}}
        </div>
      </div>
    </div>

    <!-- Betting Vue Component -->
    <div id="betting-component" class="col-md-5 mt-0"></div>

    <!-- Results -->
    <div class="card col-md-12 mt-0">
      <div class="card-header font-bold">RESULTS</div>
      <div class="results">
        <div class="bet-result-chart">
          <table id="tblBaccaratResultConsecutive" class="cell-border w-100 dataTable no-footer">
            <tbody></tbody>
          </table>
        </div>
      </div>

    </div>
  </div>
@endsection

@section('additional-scripts')
  @vite(['resources/js/play-vue.js', 'public/js/play.js'])
  <script src="https://unpkg.com/vue@3/dist/vue.global.prod.js"></script>
  {{-- <script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script> --}}
  {{-- <script src="https://vjs.zencdn.net/7.8.2/video.js"></script> --}}
  {{-- <script src="https://cdn.jsdelivr.net/npm/@mux/mux-player"></script> --}}
  <script src="https://cdn.jsdelivr.net/npm/can-autoplay@3.0.0/build/can-autoplay.js"></script>
  <script src="https://unpkg.com/video.js@7/dist/video.min.js"></script>
  <script type="text/javascript">
    var player = videojs('videojs', {
      autoplay: true
    });


    player.ready(function() {
      var promise = player.play();
      player.muted(false);

      if (promise !== undefined) {
        promise.then(function() {
          // Autoplay started!
        }).catch(function(error) {
          // Autoplay was prevented.
        });
      }
    });
    canAutoplay.video().then(({
      result
    }) => {
      if (result !== true) {
        player.ready(function() {
          player.play();
        });
      }
    })
  </script>
  <script>
    const useState = (defaultValue) => {
      let value = defaultValue;
      const getValue = () => value
      const setValue = newValue => value = newValue
      return [getValue, setValue];
    }

    const copyValueManual = (amt) => $('.bet-amount').val(amt)

    const [fightNo, setFightNo] = useState(0);

    const [fightStatus, setFightStatus] = useState('____');

    $(function() {
      $('.bet-amount').on('input', function() {
        if (parseFloat($(this).val()) < 10) {
          let amt = parseFloat($(this).val());
          $(this).val(amt);
        }
      });

      var showVideo = $('#video-display').val();
      if (showVideo == true) {
        $('#event-name').show();
        $('#header-closed').hide();
        $('#poster-img').hide();
        $('#mux-player').show();
      } else {
        $('#event-name').hide();
        $('#header-closed').show();
        $('#poster-img').show();
        $('#mux-player').hide();
      }
    })
  </script>
@endsection
