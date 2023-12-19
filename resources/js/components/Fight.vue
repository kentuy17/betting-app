<template>
  <div class='card bet-boxed-area mb-1'>
    <div v-if="!player.legit" class="mx-2">
      <div class="grid grid-cols-2 bg-os_bg">
        <div class="px-2 py-1 border border-black">
          <div>
            <h3 class="font-extrabold text-center m-2 font-tally text-red-700 text-2xl">{{ formatMoney(ghost.meron) }}
            </h3>
          </div>
        </div>
        <div class="px-2 py-1 border border-black">
          <div>
            <h3 class="font-extrabold text-center m-2 font-tally text-blue-700 text-2xl">{{ formatMoney(ghost.wala) }}
            </h3>
          </div>
        </div>
      </div>
    </div>
    <div class="bet-bg-head items-center grid grid-cols-3">
      <h6><b class="text-lg">FIGHT # </b> <b id="fight-no" class="text-lg">{{ fightNo }}</b></h6>
      <div class="text-center">
        <span class="font-bold btn btn-block btn-sm btn-lg vue-components" :class="fightStatusClass[message]">{{ message
        }}</span>
      </div>
      <div class="nav-credits-wr w-25 w-sm-50 gold-text ml-auto">
        <a href="/refillpoints" class="d-flex align-items-center justify-content-end gp-credits">
          <div class="bg-success add-btn p-1">
            <svg class="svg-inline--fa fa-coins fa-w-16" data-prefix="fas" data-icon="coins" role="img"
              xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
              <path fill="currentColor"
                d="M0 405.3V448c0 35.3 86 64 192 64s192-28.7 192-64v-42.7C342.7 434.4 267.2 448 192 448S41.3 434.4 0 405.3zM320 128c106 0 192-28.7 192-64S426 0 320 0 128 28.7 128 64s86 64 192 64zM0 300.4V352c0 35.3 86 64 192 64s192-28.7 192-64v-51.6c-41.3 34-116.9 51.6-192 51.6S41.3 334.4 0 300.4zm416 11c57.3-11.1 96-31.7 96-55.4v-42.7c-23.2 16.4-57.3 27.6-96 34.5v63.6zM192 160C86 160 0 195.8 0 240s86 80 192 80 192-35.8 192-80-86-80-192-80zm219.3 56.3c60-10.8 100.7-32 100.7-56.3v-42.7c-35.5 25.1-96.5 38.6-160.7 41.8 29.5 14.3 51.2 33.5 60 57.2z">
              </path>
            </svg>
          </div>
          <div class="credits-data d-flex ">
            <span class="pr-2 gp-yellow-text font-weight-bold" id="operator-pts">{{ creditPoints }}</span>
          </div>
        </a>
      </div>
    </div>
    <div class="m-2">
      <div class="grid grid-cols-2">
        <div class="border justify-center items-center flex bg-meroncolor">
          <h3 class="bet-button-red-full">MERON</h3>
        </div>
        <div class="border justify-center items-center flex bg-walacolor">
          <h3 class="bet-button-blue-full">WALA</h3>
        </div>
      </div>
      <div class="grid grid-cols-2 bg-os_bg">
        <div class="px-2 py-1 border border-black">
          <div>
            <h3 class="font-extrabold text-center m-2 font-tally text-2xl">{{ formatMoney(total.meron) }}</h3>
            <h3 class="font-bold text-black text-center m-2 font-tally"> PAYOUT = {{ formatMoney(meronPercentage) }}</h3>
            <div>
              <div class="flex justify-center items-center">
                <h3 class="font-bold text-drawcolor text-center text-sm">
                  <span class='text-player-bet'>{{ formatMoney(player.bets.meron) }}</span> = <span
                    class='text-player-win'>{{ formatMoney(meronWinAmount) }}</span>
                </h3>
              </div>
            </div>
          </div>
        </div>
        <div class="px-2 py-1 border border-black">
          <div>
            <h3 class="font-extrabold text-center m-2 font-tally text-2xl">{{ formatMoney(total.wala) }}</h3>
            <h3 class="font-bold text-black text-center m-2 font-tally"> PAYOUT = {{ formatMoney(walaPercentage) }}</h3>
            <div>
              <div class="flex justify-center items-center">
                <h3 class="font-bold text-drawcolor text-center text-sm">
                  <span class="text-player-bet">{{ formatMoney(player.bets.wala) }}</span> = <span
                    class='text-player-win'>{{ formatMoney(walaWinAmount) }}</span>
                </h3>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
    <div class="grid grid-cols-2 bg-os_bg">
      <div class="px-1 py-1">
        <button type="button" color="#00D7E7" @click="addBet('M')"
          class="button text-ellipsis overflow-clip uppercase bg-os_meron_btn text-sm font-bold is-info is-fullwidth">
          <i class="fa-solid fa-circle-plus text-sm mr-1"></i> BET MERON</button>
      </div>
      <div class="px-1 py-1">
        <button type="button" color="#00D7E7" @click="addBet('W')"
          class="button text-ellipsis overflow-clip uppercase bg-os_wala_btn text-sm font-bold is-info is-fullwidth">
          <i class="fa-solid fa-circle-plus text-sm mr-1"></i> BET WALA</button>
      </div>
    </div>
    <div class="col-md-12">
      <div class="input-group px-4 py-2">
        <money3 class="form-control" :model-modifiers="{ number: true }" v-model="betAmount" v-bind="money"></money3>
        <div class="input-group-append">
          <button @click='clear' class="input-group-text">CLEAR</button>
        </div>
      </div>
    </div>
    <div class="col-md-12">
      <div class="amounts-bet-btn py-2 flex-wrap">
        <button v-for="(amnt, index) in amounts" v-bind:key="index" @click="betManual(amnt)"
          class="btn btn-success btn-sm m-1"> {{ amnt }} </button>
      </div>
    </div>
    <div class="self-end mr-2">
      <a href="javascript:void(0)" data-id="1" id="settings-btn" class="btn btn-link text-primary btn-icon btn-sm play">
        <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-gear"
          viewBox="0 0 16 16">
          <path
            d="M8 4.754a3.246 3.246 0 1 0 0 6.492 3.246 3.246 0 0 0 0-6.492zM5.754 8a2.246 2.246 0 1 1 4.492 0 2.246 2.246 0 0 1-4.492 0z">
          </path>
          <path
            d="M9.796 1.343c-.527-1.79-3.065-1.79-3.592 0l-.094.319a.873.873 0 0 1-1.255.52l-.292-.16c-1.64-.892-3.433.902-2.54 2.541l.159.292a.873.873 0 0 1-.52 1.255l-.319.094c-1.79.527-1.79 3.065 0 3.592l.319.094a.873.873 0 0 1 .52 1.255l-.16.292c-.892 1.64.901 3.434 2.541 2.54l.292-.159a.873.873 0 0 1 1.255.52l.094.319c.527 1.79 3.065 1.79 3.592 0l.094-.319a.873.873 0 0 1 1.255-.52l.292.16c1.64.893 3.434-.902 2.54-2.541l-.159-.292a.873.873 0 0 1 .52-1.255l.319-.094c1.79-.527 1.79-3.065 0-3.592l-.319-.094a.873.873 0 0 1-.52-1.255l.16-.292c.893-1.64-.902-3.433-2.541-2.54l-.292.159a.873.873 0 0 1-1.255-.52l-.094-.319zm-2.633.283c.246-.835 1.428-.835 1.674 0l.094.319a1.873 1.873 0 0 0 2.693 1.115l.291-.16c.764-.415 1.6.42 1.184 1.185l-.159.292a1.873 1.873 0 0 0 1.116 2.692l.318.094c.835.246.835 1.428 0 1.674l-.319.094a1.873 1.873 0 0 0-1.115 2.693l.16.291c.415.764-.42 1.6-1.185 1.184l-.291-.159a1.873 1.873 0 0 0-2.693 1.116l-.094.318c-.246.835-1.428.835-1.674 0l-.094-.319a1.873 1.873 0 0 0-2.692-1.115l-.292.16c-.764.415-1.6-.42-1.184-1.185l.159-.291A1.873 1.873 0 0 0 1.945 8.93l-.319-.094c-.835-.246-.835-1.428 0-1.674l.319-.094A1.873 1.873 0 0 0 3.06 4.377l-.16-.292c-.415-.764.42-1.6 1.185-1.184l.292.159a1.873 1.873 0 0 0 2.692-1.115l.094-.319z">
          </path>
        </svg>
      </a>
    </div>
    <div class="flex gap-2 mb-3" style="display: flex; justify-content: center; flex-wrap: nowrap;">
      <button @click="updateFight('O')" :disabled="isDisabled.open" class="btn btn-success">
        <span v-show='!isLoading.open'>OPEN</span>
        <span v-show='isLoading.open'>Processing...</span></button>
      <button @click="updateFight('C')" :disabled="isDisabled.close" class="btn btn-danger">
        <span v-show='!isLoading.close'>CLOSE</span>
        <span v-show='isLoading.close'>Processing...</span></button>
      <button @click="doneFight()" :disabled="isDisabled.done" class="btn btn-secondary">
        <span v-show='!isLoading.done'>DONE</span>
        <span v-show='isLoading.done'>Loading...</span></button>
      <button @click="cancelFight()" :disabled='isLoading.cancel' class="btn btn-primary" v-show='!isLoading.cancel'>
        <span v-show='!isLoading.cancel'>CANCEL</span>
        <span v-show='isLoading.cancel'>Loading...</span>
      </button>
    </div>
  </div>
  <div class="modal fade" id="modal-undo-win" tabindex="-1" role="dialog" aria-labelledby="exampleModalCenterTitle"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-top">
      <div class="modal-content">
        <div class="modal-header">
          <h5 id="exampleModalLongTitle">Revert Fight Winner</h5>
        </div>
        <div class="modal-body">
          <div class="form-group mt-2">
            <label>Fight #</label>
            <input type="number" class="form-control disabled" id="fight_no">
          </div>
          <div class="revert-btn-group">
            <div class="form-group mt-2">
              <button @click="revertFight('M')" class="btn btn-danger btn-sm">MERON</button>
            </div>
            <div class="form-group mt-2">
              <button @click="revertFight('W')" class="btn btn-primary btn-sm">WALA</button>
            </div>
            <div class="form-group mt-2">
              <button @click="revertFight('D')" class="btn btn-warning btn-sm">DRAW</button>
            </div>
          </div>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { axios } from '@bundled-es-modules/axios';
import { Money3Component } from 'v-money3'

export default ({
  components: {
    money3: Money3Component,
  },
  data() {
    return {
      fightNo: 0,
      betAmount: 0,
      message: '____',
      fightStatusClass: {
        OPEN: 'gradient-status-open',
        CLOSE: 'gradient-status-close',
        '_____': 'gradient-status-pending',
      },
      amounts: [20, 50, 100, 500, 1000, 5000, 10000],
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
      isDisabled: {
        open: false,
        close: true,
        done: true,
      },
      isLoading: {
        open: false,
        close: false,
        done: false,
        cancel: false,
      },
      bets: {
        meron: 0,
        wala: 0,
      },
      player: {
        points: 0,
        id: '',
        legit: true,
        bets: {
          meron: 0,
          wala: 0,
        },
      },
      money: {
        decimal: '.',
        thousands: ',',
        precision: 0,
        masked: false,
        shouldRound: false,
      },
      ghost: {
        meron: 0,
        wala: 0,
        points: 'UNLIMITED',
      },
      autobots: {
        meron: 0,
        wala: 0,
      }
    }

  },
  mounted() {
    fetch('fight/current')
      .then(resp => resp.json())
      .then(json => {
        this.fight = json.current
        this.player.points = json.points
        this.message = this.setFightStatus(json.current)
        this.total = json.bets
        this.player.bets = json.player
        this.player.id = json.id
        this.player.legit = json.legit
      });

    window.Echo.channel('fight')
      .listen('.fight', async (e) => {
        if (e == null)
          return

        if (e.fight.curr) {
          if ((e.fight.prev.game_winner == 'D' || e.fight.prev.game_winner == 'C') && this.playerTotalBets > 0) {
            if (this.player.legit) {
              alert(`Returned ${this.formatMoney(this.playerTotalBets)} points!`)
              this.tada()
            }
            this.daog = true
            this.player.points += this.playerTotalBets
          }

          if (e.fight.prev.game_winner == 'M' && this.meronWinAmount > 0) {
            if (this.player.legit) {
              alert(`Congratulations! MERON Wins ${this.formatMoney(this.meronWinAmount)}`)
              this.tada()
            }
            this.daog = true
            this.player.points += this.meronWinAmount
          }

          if (e.fight.prev.game_winner == 'W' && this.walaWinAmount > 0) {
            if (this.player.legit) {
              alert(`Congratulations! WALA Wins ${this.formatMoney(this.walaWinAmount)}`)
              this.tada()
            }
            this.daog = true
            this.player.points += this.walaWinAmount
          }

          if (this.daog && !this.player.legit) {
            this.tada()
          }

          this.daog = false
          this.fight = e.fight.curr
          this.total.meron = this.total.wala = 0
          this.player.bets.meron = this.player.bets.wala = 0
        }
        else {
          this.fight = e.fight
        }

        if (this.fight.status == null) {
          this.ghost.meron = this.ghost.wala = 0
        }

        this.message = this.setFightStatus(this.fight)
      });

    window.Echo.channel('bet')
      .listen('.bet', async (e) => {
        if (e.bet.side === 'M') {
          this.total.meron = this.total.meron + e.bet.amount
        } else {
          this.total.wala = this.total.wala + e.bet.amount
        }

        if (e.bet.user_id == this.player.id && !this.player.legit) {
          this.player.points -= e.bet.amount
          this.betAmount = e.bet.amount
          e.bet.side == 'M'
            ? this.player.bets.meron += this.betAmount
            : this.player.bets.wala += this.betAmount
        } else {
          e.bet.side == 'M'
            ? this.ghost.meron += e.bet.amount
            : this.ghost.wala += e.bet.amount
        }
      });
  },
  computed: {
    totalSum() {
      return this.total.meron + this.total.wala
    },

    // GET FROM EACH SIDE
    meronComm() {
      return this.total.meron * 13 / 100
    },

    walaComm() {
      return this.total.wala * 13 / 100
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
    },

    playerTotalBets() {
      return this.player.bets.meron + this.player.bets.wala
    },

    creditPoints() {
      return this.player.legit ? this.formatMoney(this.player.points) : this.ghost.points
    },

  },

  methods: {
    formatMoney(value) {
      return isNaN(value) ? '0.00' : new Intl.NumberFormat('en-US')
        .format(value.toFixed(2));
    },

    clear() {
      this.betAmount = 0
    },

    setFightStatus(data) {
      this.fightNo = data.fight_no
      if (data.status == null) {
        return '_____'
      }
      if (data.status == 'O') {
        this.isDisabled.open = true
        this.isDisabled.close = false
        this.isDisabled.done = true
        return 'OPEN'
      }
      if (data.status == 'C') {
        this.isDisabled.open = false
        this.isDisabled.close = true
        this.isDisabled.done = false
        return 'CLOSE'
      }
    },

    betManual(amount) {
      this.betAmount = amount == 'ALL-IN' ? this.player.points : amount
    },

    tada() {
      var audio = new Audio('/music/tada.mp3');
      return audio.play();
    },

    doneFight() {
      Swal.fire({
        title: 'RESULT:',
        showCancelButton: true,
        showCloseButton: false,
        showDenyButton: true,
        allowOutsideClick: false,
        confirmButtonText: 'MERON',
        confirmButtonColor: 'red',
        denyButtonText: 'WALA',
        denyButtonColor: 'blue',
        cancelButtonText: 'DRAW',
        cancelButtonColor: 'green',
        allowEscapeKey: false
      })
        .then((result) => {
          if (result.isConfirmed) {
            alert('MERON WINS')
            return 'M';
          } else if (result.isDenied) {
            alert('WALA WINS');
            return 'W';
          } else {
            alert('DRAW');
            return 'D';
          }
        })
        .then((result) => {
          Swal.showLoading()
          this.updateFight('D', result)
          this.clear()
        });
    },

    cancelFight() {
      Swal.fire({
        title: 'CANCEL FIGHT?',
        showCancelButton: true,
        showCloseButton: false,
        allowOutsideClick: false,
        confirmButtonText: 'YES',
        cancelButtonText: 'NO',
        allowEscapeKey: false
      })
        .then(async (res) => {
          if (res.isConfirmed) {
            this.isLoading.cancel = true
            await this.updateFight('D', 'C')
          }
          this.clear()
          return
        })
        .then(() => this.isLoading.cancel = false)
    },

    async updateFight(status, result = null) {
      try {
        if (status == 'O') this.isLoading.open = true
        if (status == 'C') this.isLoading.close = true

        const { data } = await axios.post('/fight/update-status', {
          status: status,
          result: result,
        })

        this.message = status === 'D'
          ? this.setFightStatus(data.data)
          : this.setFightStatus(data)

        if (status == 'O') this.isLoading.open = false
        if (status == 'C') this.isLoading.close = false

        return
      } catch (error) {
        console.error(error);
      }
    },

    revertWinFight() {
      $('#modal-undo-win').modal('show')
    },

    async revertFight(result) {
      try {
        const { data } = await axios.post('/fight/revertresult', {
          fight_no: $('#fight_no').val(),
          result: result,
        })
        $('#modal-undo-win').modal('hide');
      } catch (error) {
        console.error(error);
      }

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

        if (this.betAmount > 1500 && this.player.legit) {
          alert('Maximum bet is 1,500.00')
          return
        }

        if (this.player.legit) {
          if (!confirm(`Bet ${this.betAmount.toFixed(2)}?`)) {
            return
          }
        }

        const { data } = await axios.post('/bet/add', {
          fight_no: this.fightNo,
          amount: this.betAmount,
          side: betSide
        });

        if (data.status == 'OK' && this.player.legit) {
          this.player.points -= this.betAmount
          betSide == 'M'
            ? this.player.bets.meron += this.betAmount
            : this.player.bets.wala += this.betAmount
          this.clear()
        }

      } catch (err) {
        alert(err.response.data.error);
      }
    }
  }
})
</script>

<style>
.revert-btn-group {
  display: flex;
  flex-direction: row;
  justify-content: space-evenly;
}
</style>
