<template>
  <div>
    <div class="bet-boxed-area">
      <div class="bet-bg-head flex flex-nowrap justify-between items-center">
        <h6><b class="text-lg">FIGHT # </b> <b id="fight-no" class="text-lg">{{ fightNo }}</b></h6>
        <div>POINTS: <a id="current-pts" href='/deposit' class="underline font-bold">{{formatMoney(player.points)}}</a></div>
      </div>
      <div class="text-center">
        <span class="btn btn-block gradient-status-close btn-lg vue-components">{{message}}</span>
      </div>
      <!--  -->
      <div class="row no-gutters">
        <div class="col-md-6">
          <div class="bet-buy-sell-form">
            <p class="text-center text-xl"><b class="bet-meron">{{formatMoney(total.meron)}}</b></p>
            <div class="bet-buy">
              <div>
                <p>PAYOUT: <span class="fright">{{ percentage.meron }} = {{ formatMoney(payout.meron) }}</span></p>
              </div>
              <div class="text-center mt-3 mb-3 bet-up">
              </div>
              <div><button @click="addBet('M')" class="bet-button-red-full">MERON</button></div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="bet-buy-sell-form">
            <p class="text-center text-xl"><b class="bet-wala">{{ formatMoney(total.wala) }}</b></p>
            <div class="bet-sell">
              <div>
                <p>PAYOUT: <span class="fright">{{ percentage.wala }} = {{ formatMoney(payout.wala) }}</span></p>
              </div>
              <div class="text-center mt-3 mb-3 bet-down">
              </div>
              <div><button @click="addBet('W')" class="bet-button-green-full">WALA</button></div>
            </div>
          </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="input-group px-4 py-2" style="">
          <input type="number" class="form-control bet-amount" v-model="betAmount" min="0">
          <div class="input-group-append"> <button @click="clear" class="input-group-text">CLEAR</button> </div>
        </div>
      </div>
      <div class="col-md-12">
        <div class="amounts-bet-btn py-2 flex-wrap">
          <button 
            v-for="(amnt, index) in amounts" 
            v-bind:key="index" 
            @click="betManual(amnt)" 
            class="btn btn-success btn-sm m-1">
            {{ amnt }}
          </button>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
export default {
  data() {
    return {
      message: '_____',
      fight: [],
      fightNo: 0,
      betAmount: 0,
      amounts: [20, 50, 100, 500, 1000, 2000, 5000],
      total: {
        meron: 0.00,
        wala: 0.00,
      },
      payout: {
        meron: 0.00,
        wala: 0.00,
      },
      percentage: {
        meron: 187.00,
        wala: 187.00,
      },
      player: {
        points: 0
      }
    }
  },
  mounted() {
    fetch('fight/current')
      .then(resp => resp.json())
      .then(json => {
        this.fight = json.current
        // console.log(json, 'json');
        this.message = this.setFightStatus(json.current)
        this.player.points = json.points
        this.total = json.bets
      }); 

    window.Echo.channel('fight')
      .listen('.fight', async (e)=>{
        // console.log(e);
        if(e == null) return

        if(e.fight.curr) {
          this.fight = e.fight.curr
          this.total.meron = this.total.wala = '0.00'
        } 
        else {
          this.fight = e.fight  
        }

        this.message = this.setFightStatus(this.fight)
      });

    window.Echo.channel('bet')
      .listen('.bet', async (e)=>{
        // console.log(e);
        if(e.bet.side === 'M') {
          this.total.meron = this.total.meron + e.bet.amount
        } else {
          this.total.wala = this.total.wala + e.bet.amount
        }
      });
  },
  watch: {
    // 
  },

  methods: {
    formatMoney(value) {
      return new Intl.NumberFormat('en-US').format(value);
    },

    setFightStatus(data) {
      this.fightNo = data.fight_no
      if(data.status == null) {
        return '_____'
      }
      if(data.status == 'O') {
        return 'OPEN'
      }
      if(data.status == 'C') {
        return 'CLOSE'
      }
    },

    betManual(amount) {
      this.betAmount = amount
    },

    clear() {
      this.betAmount = 0
    },

    topUp() {
      window.location.href = 'add-credits'
    },

    async betMeron () {
      const { data } = await axios.post('/bet/add', {
          fight_no: this.fightNo,
          amount: this.betAmount,
          side: 'M'
        });
      console.log(data);
    },

    async addBet (betSide) {
      try {
        if(this.message !== 'OPEN') {
          alert('Cannot Bet')
          return
        }

        if (this.betAmount < 10) {
          alert('Minimum bet is 10.00')
          return
        }

        if(this.betAmount > this.player.points) {
          alert('Insuficient Points')
          return
        }

        const { data } = await axios.post('/bet/add', {
            fight_no: this.fightNo,
            amount: this.betAmount,
            side: betSide
          });

        if(data.status == 'OK') {
          this.player.points -= this.betAmount
        }

      } catch (err) {
        alert(err.response.data.error);
      }
    }
  }
}
</script>