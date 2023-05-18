<template>
  <div class="card bet-boxed-area mb-1">
    <div class="bet-bg-head items-center grid grid-cols-3">
      <h6><b class="text-lg">FIGHT # </b> <b id="fight-no" class="text-lg">{{ fightNo }}</b></h6>
      <div class="text-center"><span class="btn btn-block btn-sm gradient-status-close btn-lg vue-components">{{ message
      }}</span></div>
      <div>POINTS: <a id="current-pts" href="/deposit" class="underline font-bold">{{ formatMoney(player.points) }}</a>
      </div>
    </div>
    <div class="m-2">
      <div class="grid grid-cols-2">
        <div class="border justify-center items-center flex bg-meroncolor">
          <h3 class="bet-button-red-full">MERON</h3></div>
        <div class="border justify-center items-center flex bg-walacolor">
          <h3 class="bet-button-blue-full">WALA</h3></div>
      </div>
      <div class="grid grid-cols-2 bg-os_bg">
        <div class="px-2 py-1">
          <div>
            <h3 class="font-bold text-center m-2 font-tally">{{ formatMoney(total.meron) }}</h3>
            <h3 class="font-bold text-black text-center m-2 font-tally"> PAYOUT = {{ formatMoney(meronPercentage) }}</h3>
            <div>
              <div class="flex justify-center items-center">
                <h3 class="font-bold text-drawcolor text-center text-sm">{{ formatMoney(meronWinAmount) }}</h3>
              </div>
            </div>
          </div>
        </div>
        <div class="px-2 py-1">
          <div>
            <h3 class="font-bold text-center m-2 font-tally">{{ formatMoney(total.wala) }}</h3>
            <h3 class="font-bold text-black text-center m-2 font-tally"> PAYOUT = {{ formatMoney(walaPercentage) }}</h3>
            <div>
              <div class="flex justify-center items-center">
                <h3 class="font-bold text-drawcolor text-center text-sm">{{ formatMoney(walaWinAmount) }}</h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-2 bg-os_bg">
    <div class="px-1 py-1">
      <button type="button" color="#00D7E7" @click="addBet('M')" class="button text-ellipsis overflow-clip uppercase bg-os_meron_btn text-sm font-bold is-info is-fullwidth">
        <i class="fa-solid fa-circle-plus text-sm mr-1"></i> BET MERON</button>
    </div>
    <div class="px-1 py-1">
      <button type="button" color="#00D7E7" @click="addBet('W')" class="button text-ellipsis overflow-clip uppercase bg-os_wala_btn text-sm font-bold is-info is-fullwidth">
        <i class="fa-solid fa-circle-plus text-sm mr-1"></i> BET WALA</button>
    </div>
    </div>
    <div class="col-md-12">
      <div class="input-group px-4 py-2"><input type="number" v-model='betAmount' class="form-control bet-amount" min="0">
        <div class="input-group-append">
          <button @click='clear' class="input-group-text">CLEAR</button></div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="amounts-bet-btn py-2 flex-wrap">
        <button v-for="(amnt, index) in amounts" 
          v-bind:key="index" 
          @click="betManual(amnt)"
          class="btn btn-success btn-sm m-1">
          {{ amnt }}
        </button>
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
        meron: 0,
        wala: 0,
      },
      payout: {
        meron: 0,
        wala: 0,
      },
      percentage: {
        meron: 187,
        wala: 187,
      },
      player: {
        points: 0,
        id: '',
        bets: {
          meron: 0,
          wala: 0,
        },
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
        this.player.id = json.id
        this.total = json.bets,
          this.player.bets = json.player
      })
      .then(() => {
        window.Echo.private('user.' + this.player.id)
          .listen('Result', async (e) => {
            console.log(e);
            alert('Congratulations! You win ' + e.bet.win_amount)
            this.player.points += e.bet.win_amount
          });
      })

    window.Echo.channel('fight')
      .listen('.fight', async (e) => {
        // console.log(e);
        if (e == null) return

        if (e.fight.curr) {
          this.fight = e.fight.curr
          this.total.meron = this.total.wala = 0
        }
        else {
          this.fight = e.fight
        }

        this.message = this.setFightStatus(this.fight)
      });

    window.Echo.channel('bet')
      .listen('.bet', async (e) => {
        // console.log(e);
        if (e.bet.side === 'M') {
          this.total.meron = this.total.meron + e.bet.amount
        } else {
          this.total.wala = this.total.wala + e.bet.amount
        }
      });


  },
  watch: {
    // 
  },

  computed: {
    totalSum() {
      return this.total.meron + this.total.wala
    },

    // GET FROM EACH SIDE
    meronComm() {
      return this.total.meron * 10 / 100
    },

    walaComm() {
      return this.total.wala * 10 / 100
    },

    meronPercentage() {
      let win = this.totalSum - this.meronComm
      return win / this.total.meron * 100
    },

    walaPercentage() {
      let win = this.totalSum - this.walaComm
      return win / this.total.wala * 100
    },

    meronWinAmount() {
      return (this.player.bets.meron * this.meronPercentage) / 100
    },

    walaWinAmount() {
      return (this.player.bets.wala * this.walaPercentage) / 100
    }

    // GET FROM 5% TOTAL BETS
    // commission() {
    //   return this.totalSum * 10 / 100
    // },

    // meronPercentage() {
    //   let gross = this.totalSum - this.total.meron
    //   let tong = gross * 0.1
    //   let net = (gross - tong) / this.total.meron * 100
    //   return net + 100
    // },

    // walaPercentage() {
    //   // let gross = this.totalSum - this.commission
    //   // return gross / this.total.wala * 100
    //   let gross = this.totalSum - this.total.wala
    //   let tong = gross * 0.1
    //   return ((gross - tong) * 2) + 100
    // },

    // meronWinAmount() {
    //   return this.meronPercentage * this.player.bets.meron / 100
    // },

    // walaWinAmount() {
    //   return this.walaPercentage * this.player.bets.wala / 100
    // }


  },

  methods: {
    formatMoney(value) {
      return isNaN(value) ? '0.00' : new Intl.NumberFormat('en-US')
        .format(value.toFixed(2));
    },

    setFightStatus(data) {
      this.fightNo = data.fight_no
      if (data.status == null) {
        return '_____'
      }
      if (data.status == 'O') {
        return 'OPEN'
      }
      if (data.status == 'C') {
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

    async addBet(betSide) {
      try {
        if (this.message !== 'OPEN') {
          alert('Cannot Bet')
          return
        }

        if (this.betAmount < 10) {
          alert('Minimum bet is 10.00')
          return
        }

        if (this.betAmount > this.player.points) {
          alert('Insuficient Points')
          return
        }

        if (!confirm(`Bet ${this.betAmount}?`)) {
          return
        }

        const { data } = await axios.post('/bet/add', {
          fight_no: this.fightNo,
          amount: this.betAmount,
          side: betSide
        });

        if (data.status == 'OK') {
          this.player.points -= this.betAmount
          betSide == 'M'
            ? this.player.bets.meron += this.betAmount
            : this.player.bets.wala += this.betAmount
        }

      } catch (err) {
        alert(err.response.data.error);
      }
    }
  }
}
</script>