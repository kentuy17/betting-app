@extends('layouts.app')

@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('css/play-sabong.css') }}" type="text/css">
@endsection

@section('content')
<div class="container" id="play-container">
  <div class="row justify-content-center">
    <div class="row col-12">
      <!-- Video Player -->
      <div class="col-md-6 sm:mb-2">
        <div class="bet-bg-head font-bold">x100 Exp.</div>
        <video width="100%">
          <source src="{{ asset('videos/e-sabong.mp4') }}" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>

      <!-- Betting Vue Component -->
      <div id="betting-component" class="col-md-6"></div>
    </div>

  </div>
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
      if ($(this).val() < 10) {
        let amt = parseInt($(this).val());
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
