<template>
  <div>
    <div class="bet-boxed-area">
      <div class="bet-bg-head flex flex-nowrap justify-between items-center">
        <h6><b class="text-lg">FIGHT # </b> <b id="fight-no" class="text-lg">{{ fightNo }}</b></h6>
        <div>POINTS: <span id="current-pts" class="font-bold">{{formatMoney(player.points)}}</span></div>
      </div>
      <div class="text-center">
        <span class="btn btn-block gradient-status-close btn-lg vue-components">{{message}}</span>
      </div>
      <div class="row no-gutters">
        <div class="col-md-6">
          <div class="bet-buy-sell-form">
            <p class="text-center text-xl"><b class="bet-up">{{formatMoney(total.meron)}}</b></p>
            <div class="bet-buy">
              <div>
                <p>PAYOUT: <span class="fright">100% x 0.2 = 0.02</span></p>
              </div>
              <div class="text-center mt-3 mb-3 bet-up">
              </div>
              <div><button @click="addBet('M')" class="bet-button-red-full">MERON</button></div>
            </div>
          </div>
        </div>
        <div class="col-md-6">
          <div class="bet-buy-sell-form">
            <p class="text-center text-xl"><b class="bet-down">{{total.wala}}</b></p>
            <div class="bet-sell">
              <div>
                <p>PAYOUT: <span class="fright">100% x 0.2 = 0.02</span></p>
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
          <input type="number" class="form-control bet-amount" v-model="betAmount" min="0.00">
          <div class="input-group-append"> <button @click="clear" class="input-group-text">CLEAR</button> </div>
        </div>
      </div>
      <div class="col-md-12" style="">
        <div class="amounts-bet-btn py-2">
          <button 
            v-for="(amnt, index) in amounts" 
            v-bind:key="index" 
            @click="betManual(amnt)" 
            class="btn btn-success btn-sm mx-1">
            {{amnt}}
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
        meron: parseFloat(6023.14),
        wala: parseFloat(5000.11)
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
        this.fight = json.data
        this.message = this.setFightStatus(json.data)
        this.player.points = json.points
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
        if (this.betAmount < 10) {
          alert('Minimum bet is 10.00');
          return
        }

        if(this.betAmount > this.player.points) {
          alert('Insuficient Points');
          return
        }

        const { data } = await axios.post('/bet/add', {
            fight_no: this.fightNo,
            amount: this.betAmount,
            side: betSide
          });

        if(data.status == 'OK') {
          this.total.meron += this.betAmount;
          this.player.points -= this.betAmount
        }
      } catch (err) {
        alert(err.response.data.error);
      }
    }
  }
}
</script>