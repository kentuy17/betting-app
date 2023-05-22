<template>
  <div class='card bet-boxed-area mb-1'>
    <div class="bet-bg-head items-center grid grid-cols-3">
      <h6><b class="text-lg">FIGHT # </b> <b id="fight-no" class="text-lg">{{ fightNo }}</b></h6>
      <div class="text-center"><span class="btn btn-block btn-sm gradient-status-close btn-lg vue-components">{{ message }}</span></div>
      <div class="nav-credits-wr w-25 w-sm-50 gold-text ml-auto">
        <a href="/refillpoints" class="d-flex align-items-center justify-content-end gp-credits">
          <div class="bg-success add-btn p-1">
            <svg class="svg-inline--fa fa-coins fa-w-16" data-prefix="fas" data-icon="coins" role="img" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" data-fa-i2svg="">
              <path fill="currentColor" d="M0 405.3V448c0 35.3 86 64 192 64s192-28.7 192-64v-42.7C342.7 434.4 267.2 448 192 448S41.3 434.4 0 405.3zM320 128c106 0 192-28.7 192-64S426 0 320 0 128 28.7 128 64s86 64 192 64zM0 300.4V352c0 35.3 86 64 192 64s192-28.7 192-64v-51.6c-41.3 34-116.9 51.6-192 51.6S41.3 334.4 0 300.4zm416 11c57.3-11.1 96-31.7 96-55.4v-42.7c-23.2 16.4-57.3 27.6-96 34.5v63.6zM192 160C86 160 0 195.8 0 240s86 80 192 80 192-35.8 192-80-86-80-192-80zm219.3 56.3c60-10.8 100.7-32 100.7-56.3v-42.7c-35.5 25.1-96.5 38.6-160.7 41.8 29.5 14.3 51.2 33.5 60 57.2z"></path>
            </svg>
          </div>
          <div class="credits-data d-flex ">
            <span class="pr-2 gp-yellow-text font-weight-bold" id="operator-pts">{{ formatMoney(player.points) }}</span>
          </div>
        </a>
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
        <div class="px-2 py-1 border border-black">
          <div>
            <h3 class="font-extrabold text-center m-2 font-tally text-2xl">{{ formatMoney(total.meron) }}</h3>
            <h3 class="font-bold text-black text-center m-2 font-tally"> PERCENT = {{ formatMoney(meronPercentage) }}</h3>
          </div>
        </div>
        <div class="px-2 py-1 border border-black">
          <div>
            <h3 class="font-extrabold text-center m-2 font-tally text-2xl">{{ formatMoney(total.wala) }}</h3>
            <h3 class="font-bold text-black text-center m-2 font-tally"> PERCENT = {{ formatMoney(walaPercentage) }}</h3>
          </div>
        </div>
      </div>
    </div>
    <div class="flex flex-col gap-2 lg:flex-row justify-center my-3 px-5">
      <button @click="updateFight('O')" :disabled="isDisabled.open" class="btn btn-success btn-lg mx-2">
        <span v-show='!isLoading.open'>OPEN</span><span v-show='isLoading.open'>Processing...</span></button>
      <button @click="updateFight('C')" :disabled="isDisabled.close" class="btn btn-danger btn-lg mx-2">
        <span v-show='!isLoading.close'>CLOSE</span><span v-show='isLoading.close'>Processing...</span></button>
      <button @click="doneFight()" :disabled="isDisabled.done" class="btn btn-secondary btn-lg mx-2">
        <span v-show='!isLoading.done'>DONE</span><span v-show='isLoading.done'>Loading...</span></button>
      <button 
        @click="cancelFight()" 
        :disabled='isLoading.cancel' 
        class="btn btn-primary btn-lg mx-2"
        v-show='!isLoading.cancel'>
        <span v-show='!isLoading.cancel'>Cancel Fight</span>
        <span v-show='isLoading.cancel'>Loading...</span>
      </button>
    </div>
  </div>
</template>

<script>
  import { axios } from '@bundled-es-modules/axios';

  export default ({
    data() {
      return {
        fightNo: 0,
        message: '____',
        total: {
          meron: 0,
          wala: 0,
        },
        percentage: {
          meron: 190,
          wala: 190,
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
        });

      window.Echo.channel('bet')
        .listen('.bet', async (e) => {
          e.bet.side === 'M' 
            ? this.total.meron += e.bet.amount
            : this.total.wala += e.bet.amount
        });
    },
    computed: {
      totalSum() {
        return this.total.meron + this.total.wala
      },

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

    },

    methods: {
      formatMoney(value) {
        return isNaN(value) ? '0.00' : new Intl.NumberFormat('en-US')
          .format(value.toFixed(2));
      },

      clear() {
        this.total.meron = this.total.wala = 0
      },

      setFightStatus(data) {
        this.fightNo = data.fight_no
        if(data.status == null) {
          return '_____'
        }
        if(data.status == 'O') {
          this.isDisabled.open = true
          this.isDisabled.close = false
          this.isDisabled.done = true
          return 'OPEN'
        }
        if(data.status == 'C') {
          this.isDisabled.open = false
          this.isDisabled.close = true
          this.isDisabled.done = false
          return 'CLOSE'
        }
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
          this.updateFight('D',result)
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
          if(res.isConfirmed) {
            this.isLoading.cancel = true
            await this.updateFight('D','C')
          }
          this.clear()
          return
        })
        .then(() => this.isLoading.cancel = false)
      },

      async updateFight(status, result=null) {
        try {
          if(status == 'O') this.isLoading.open = true
          if(status == 'C') this.isLoading.close = true

          const {data} = await axios.post('/fight/update-status', {
            status: status,
            result: result,
          })
          
          this.message = status === 'D'
            ? this.setFightStatus(data.data)
            : this.setFightStatus(data)
          
          if(status == 'O') this.isLoading.open = false
          if(status == 'C') this.isLoading.close = false

          return
        } catch (error) {
          console.error(error);
        }
        
      }
    }
  })
</script>

