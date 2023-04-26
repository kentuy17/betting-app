@extends('layouts.app')

@section('content')
<div class="container">
  <div class="row justify-content-center">
    <div class="row col-12">
      <!-- Video Player -->
      <div class="col-md-6 sm:mb-2">
        <div class="bet-bg-head">x100 Exp.</div>
        <video width="100%">
          <source src="{{ asset('videos/e-sabong.mp4') }}" type="video/mp4">
          Your browser does not support the video tag.
        </video>
      </div>

      <!-- Betting Component -->
      <div class="col-md-6">
        <div class="bet-boxed-area">
          <div class="bet-bg-head flex flex-nowrap justify-between items-center">
            <h6><b class="text-lg">FIGHT # </b>  <b id="fight-no" class="text-lg">00</b></h6>
            <div>POINTS: <span id="current-pts" class="font-bold">1500.00</span></div>
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
                  <div class="menu-green"><button id="btn-bet-meron" class="bet-button-red-full">MERON</button></div>
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
  <div class="my-4 card col-md-12">
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

  .bet-result-chart table tr td {
    padding: 2.5px !important;
    border: 1px solid rgba(0, 0, 0, .178) !important;
  }

  .results .bet-result-chart table tr td div {
    height: 2rem;
    width: 2rem;
  }

  .results .bet-result-chart table tr td .circleRedAll {
    background-color: #f34141;
  }

  .results .bet-result-chart table tr td .circleBlueAll {
    background-color: #0d6efd;
  }

  .results .bet-result-chart table tr td .circleGreenAll {
    background-color: green;
  }

  .results .bet-result-chart table tr td .circleCancelAll {
    background-color: rgba(0, 0, 0, .178);
  }

  .circleCancelAll,
  .circleBlueAll,
  .circleGreenAll,
  .circleRedAll {
    height: 2rem;
    width: 2rem;
    border-radius: 1.2rem;
    text-align: center;
    color: #fff;
    line-height: 30px;
    font-weight: 700;
  }

  .bet-dark h4,.bet-dark h6,.bet-dark p,.bet-dark a{color:#fff;}
  body.bet-dark .input-group-text{color:#e9ecef;text-align:center;white-space:nowrap;background-color:#080e15;border:1px solid #292f3e;border-radius:0;}
  body.bet-dark .form-control{color:#ffffff;background-color:#080e15;background-clip:padding-box;border:1px solid #292f3e;border-radius:0.25rem;transition:border-color 0.15s ease-in-out, box-shadow 0.15s ease-in-out;}
  .sm-gutters>[class*=col-]{padding-right:7px;padding-left:7px;}
  .fright{float:right;}
  .bet-bg-head{background:#ecf4fe;padding:13px;}
  .bet-boxed-area{border:1px solid #f1f1f1;background:#fff;}
  body.bet-dark .bet-bg-head{background:#1f2638;padding:13px;}
  body.bet-dark .bet-boxed-area{background:#131722;border:none;color:#fff;}
  .bet-up{color:#01aa78!important;}
  .bet-down{color:#eb6a61!important;}
  .bet-button-green-full{background:#0d6efd;color:#fff;padding:9px;text-align:center;text-decoration:none;display:block;width:100%;font-size:20px;transition:all 0.4s ease;}
  .bet-button-green-full:hover{background:blue;text-decoration:none;color:#fff;}
  .bet-button-red-full{background:#eb6a61!important;border:none!important;color:#fff;padding:9px;text-align:center;text-decoration:none;display:block;width:100%;font-size:20px;transition:all 0.4s ease;}
  .bet-button-red-full:hover{text-decoration:none;color:#fff;background:#f34141;}
  .menu-green{background:#0d6efd;}
  .menu-green button{color:#fff!important;}
  .menu-green button:hover{border-bottom:none!important;background:#f34141!important;}
  .bet-buy-sell-form > p{padding:10px;margin-bottom:0;}
  .bet-buy{padding:20px;border-top:1px solid #f1f1f1;border-right:1px solid #f1f1f1;}
  .bet-sell{padding:20px;border-top:1px solid #f1f1f1;}
  body.bet-dark .bet-buy{padding:20px;border-top:1px solid #292f3e;border-right:1px solid #292f3e;}
  body.bet-dark .bet-sell{padding:20px;border-top:1px solid #292f3e;}
  ::-webkit-scrollbar{background:transparent;width:3px;height:10px;border-radius:10px;}
  ::-webkit-scrollbar-thumb{background-color:rgba(255, 255, 255, 0.21);border:solid rgba(0,0,0,0) 2px;transition:all 0.4s ease;}
  ::-webkit-scrollbar-thumb:hover{background-color:rgba(255, 255, 255, 0.8);}
  
  .amounts-bet-btn {
    display: flex;
    flex-wrap: nowrap;
    justify-content: center;
    flex-direction: row;
  }

</style>
@endsection

@section('additional-scripts')
<script src="{{ asset('js/play.js') }}" defer></script>
<script>
  $(function() {
    $('#bet-amount').on('input',function() {
      if($(this).val() < 10) {
        let amt = parseInt($(this).val());
        $(this).val(amt);
      }
    });

    $('#btn-bet-meron').on('click', function() {
      let betAmt = parseFloat($('#bet-amount').val());
      if(betAmt == 0.00 || betAmt < 10) {
        alert('Minimum bet is 10.00');
      }
    });

    $('#btn-bet-wala').on('click', function () {
      let betAmt = parseFloat($('#bet-amount').val());
      if(betAmt == 0.00 || betAmt < 10) {
        alert('Minimum bet is 10.00');
      }
    });
  })

  const copyValueManual = (amt) => $('#bet-amount').val(amt)
</script>
@endsection
