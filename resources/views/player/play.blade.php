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
</style>
@endsection

@section('content')
<div class="max-w-full min-w-full min-h-screen shadow-md bg-os_event_body_black row m-0 g-2" id="play-container">
  <div class="col-md-7 my-0">
    <div class="card mb-0">
      <div id="video-stream-container" class="embed-responsive">
        <input type="hidden" id="video-display" value="{{ $video_display }}">
        <div class="bet-bg-head font-bold" id="event-name">{{ $fight->name }}</div>
        <div id="header-closed" class="bet-bg-head font-bold" style="display: none;">EVENT CLOSED</div>
        <img id="poster-img" style="display: none;" src="{{ asset('img/10-streak-win-promo-poster.png') }}" alt="10-streak-win-promo">
        <div id="mux-player" style="width:100%;height:0px;position:relative;padding-bottom:56.25%;">
          <iframe src="https://app.onestream.live/api/v2/rtmp/auth/stream/embed_player?key=d_auth_2078804_1tt42mfs4"
            width="100%" height="100%" style="width:100%;height:100%;position:absolute;left:0px;top:0px;overflow: hidden;"
            scrolling="no" frameborder="0" allow="autoplay" allowfullscreen> </iframe>
        </div>
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
<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
<script src="https://vjs.zencdn.net/7.8.2/video.js"></script>
<script src="https://cdn.jsdelivr.net/npm/@mux/mux-player"></script>
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
    if(showVideo == true) {
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
