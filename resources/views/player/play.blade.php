@extends('layouts.app')

@section('additional-styles')
  <link rel="stylesheet" href="{{ asset('css/play-sabong.css') }}" type="text/css">
@endsection

@section('content')
<div class="container">
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

      <!-- Betting Component -->
      <div class="col-md-6">
        <div class="bet-boxed-area">
          <div class="bet-bg-head flex flex-nowrap justify-between items-center">
            <h6><b class="text-lg">FIGHT # </b> <b id="fight-no" class="text-lg">00</b></h6>
            <div>POINTS: <span id="current-pts" class="font-bold">1500.00</span></div>
          </div>
          <div class="text-center">
            <span class="btn btn-block gradient-status-close btn-lg" id="player-fight-status">_____</span>
          </div>
          <div class="row no-gutters">
            <div class="col-md-6">
              <div class="bet-buy-sell-form">
                <p class="text-center text-xl"><b class="bet-up">6,023.14</b></p>
                <div class="bet-buy">
                  <div>
                    <p>PAYOUT: <span class="fright">100% x 0.2 = 0.02</span></p>
                  </div>
                  <div class="text-center mt-3 mb-3 bet-up">
                  </div>
                  <div><button id="btn-bet-meron" class="bet-button-red-full">MERON</button></div>
                </div>
              </div>
            </div>
            <div class="col-md-6">
              <div class="bet-buy-sell-form">
                <p class="text-center text-xl"><b class="bet-down">6,023.14</b></p>
                <div class="bet-sell">
                  <div>
                    <p>PAYOUT: <span class="fright">100% x 0.2 = 0.02</span></p>
                  </div>
                  <div class="text-center mt-3 mb-3 bet-down">
                  </div>
                  <div><button id="btn-bet-wala" class="bet-button-green-full">WALA</button></div>
                </div>
              </div>
            </div>
          </div>
          <div class="col-md-12">
            <div class="input-group px-4 py-2" style="">
              <input type="number" class="form-control" id="bet-amount" value="0.02323476" min="0.00">
              <div class="input-group-append"> <button id="clear-bet" onclick="$('#bet-amount').val('0')" class="input-group-text">CLEAR</button> </div>
            </div>
          </div>
          <div class="col-md-12" style="">
            <div class="amounts-bet-btn py-2">
              <button class="btn btn-success btn-sm mx-1" id="btnManual20" value="20" onclick="copyValueManual(this.value)">20</button>
              <button class="btn btn-success btn-sm mx-1" id="btnManual10k" value="50" onclick="copyValueManual(this.value)">50</button>
              <button class="btn btn-success btn-sm mx-1" id="btnManual300" value="100" onclick="copyValueManual(this.value)">100</button>
              <button class="btn btn-success btn-sm mx-1" id="btnManual100" value="500" onclick="copyValueManual(this.value)">500</button>
              <button class="btn btn-success btn-sm mx-1" id="btnManual500" value="1000" onclick="copyValueManual(this.value)">1k</button>
              <button class="btn btn-success btn-sm mx-1" id="btnManual1000" value="2000" onclick="copyValueManual(this.value)">2k</button>
              <button class="btn btn-success btn-sm mx-1" id="btnManual2000" value="5000" onclick="copyValueManual(this.value)">5k</button>
            </div>
          </div>

        </div>
      </div>
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
<script src="{{ asset('js/play.js') }}" defer></script>
<script>
  const useState = (defaultValue) => {
    let value = defaultValue;
    const getValue = () => value
    const setValue = newValue => value = newValue
    return [getValue, setValue];
  }

  const copyValueManual = (amt) => $('#bet-amount').val(amt)  

  const [fightNo, setFightNo] = useState(0);

  const [fightStatus, setFightStatus] = useState('____');

  $(function() {
    $('#bet-amount').on('input', function() {
      if ($(this).val() < 10) {
        let amt = parseInt($(this).val());
        $(this).val(amt);
      }
    });

    $('#btn-bet-meron').on('click', function() {
      let betAmt = parseFloat($('#bet-amount').val());
      if (betAmt == 0.00 || betAmt < 10) {
        alert('Minimum bet is 10.00');
      }
    });

    $('#btn-bet-wala').on('click', function() {
      let betAmt = parseFloat($('#bet-amount').val());
      if (betAmt == 0.00 || betAmt < 10) {
        alert('Minimum bet is 10.00');
      }
    });

    window.Echo.channel('fight')
      .listen('.fight', async (e)=>{
        var stat;
        var statusDiv = $('#player-fight-status').removeClass('gradient-status-open gradient-status-close');
        var fightNoDiv = $('#fight-no');
        if(e.fight.status == 'C') {
          stat = 'CLOSED';
          statusDiv.addClass('gradient-status-close')
        } else if(e.fight.status == 'O') {
          stat = 'OPEN';
          statusDiv.addClass('gradient-status-open')
        } else if(e.fight.status == null) {
          stat = '____';
          statusDiv.addClass('gradient-status-close')
        }
        
        await setFightStatus(stat);
        await setFightNo(e.fight.fight_no);

        statusDiv.html(fightStatus());
        fightNoDiv.html(fightNo());
      })
  })
  
</script>
@endsection
