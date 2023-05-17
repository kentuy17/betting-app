@extends('layouts.app')

@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('css/play-sabong.css') }}" type="text/css">
  <link href="https://vjs.zencdn.net/7.8.2/video-js.css" rel="stylesheet" />
@endsection

@section('content')
<div class="container p-0 flex flex-row flex-wrap" id="play-container">
  <div class="col-md-6 ">
    <div class="card mb-2">
      <div class="embed-responsive">
        <div class="bet-bg-head font-bold">{{ $fight->name }}</div>
        <video
          id="my-video"
          class="video-js"
          controls
          preload="auto"
          data-setup="{}"
        >
          <source src="{{ asset('storage/hls/mystream.m3u8') }} " type="application/x-mpegURL" res="9999" label="auto" />
          <p class="vjs-no-js">To view this video please enable JavaScript, and consider upgrading to a web browser that <a href="https://videojs.com/html5-video-support/" target="_blank">supports HTML5 video</a></p>
        </video>
      </div>
    </div>
  </div>

  <!-- Betting Vue Component -->
  <div id="betting-component" class="col-md-6"></div>

  <!-- Results -->
  <div class="card col-md-12 mt-3">
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
@vite('resources/js/play-vue.js')
<script src="https://unpkg.com/vue@3/dist/vue.global.js"></script>
<script src="https://vjs.zencdn.net/ie8/1.1.2/videojs-ie8.min.js"></script>
<script src="https://vjs.zencdn.net/7.8.2/video.js"></script>

<script src="{{ asset('js/play.js') }}" defer></script>
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

    // $('#btn-bet-meron').on('click', function() {
    //   let betAmt = parseFloat($('#bet-amount').val());
    //   if (betAmt == 0.00 || betAmt < 10) {
    //     alert('Minimum bet is 10.00');
    //   }
    // });

    // $('#btn-bet-wala').on('click', function() {
    //   let betAmt = parseFloat($('#bet-amount').val());
    //   if (betAmt == 0.00 || betAmt < 10) {
    //     alert('Minimum bet is 10.00');
    //   }
    // });

    // window.Echo.channel('fight')
    //   .listen('.fight', async (e)=>{
    //     var stat;
    //     var statusDiv = $('#player-fight-status').removeClass('gradient-status-open gradient-status-close');
    //     var fightNoDiv = $('#fight-no');
    //     if(e.fight.status == 'C') {
    //       stat = 'CLOSED';
    //       statusDiv.addClass('gradient-status-close')
    //     } else if(e.fight.status == 'O') {
    //       stat = 'OPEN';
    //       statusDiv.addClass('gradient-status-open')
    //     } else if(e.fight.status == null) {
    //       stat = '____';
    //       statusDiv.addClass('gradient-status-close')
    //     }
        
    //     await setFightStatus(stat);
    //     await setFightNo(e.fight.fight_no);

    //     statusDiv.html(fightStatus());
    //     fightNoDiv.html(fightNo());
    //   })
  })
  
</script>
@endsection
