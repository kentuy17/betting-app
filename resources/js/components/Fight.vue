<template>
  <div class='card mb-2'>
    <div class="fight-container pt-8 lg:px-5 px-3">
      <div class='flex justify-center flex-col col-lg-4'>
        <p class="text-danger mb-4 text-3xl font-bold align-self-center">MERON</p>
        <div class='text-2xl'>
          <p id="m-total-bets" class="total-bets">0</p>
          <p id="m-win-chance" class="win-chance text-white">190.00</p>
        </div>
      </div>
      <div class='flex justify-center flex-col col-lg-4 mx-3 text-white'>
        <p class="inline label text-center font-bold text-2xl">FIGHT #</p>
        <p class="text-center inline text-3xl font-bold"> {{ fightNo }} </p>
        <p class="text-secondary text-center mt-4 font-medium text-3xl" id="fight-status">{{ message }}</p>
      </div>
      <div class='flex justify-center flex-col col-lg-4'>
        <p class="text-primary mb-4 text-3xl font-bold align-self-center">WALA</p>
        <div class='text-2xl'>
          <p id="w-total-bets" class="total-bets">0</p>
          <p id="w-win-chance" class="win-chance text-white">190.00</p>
        </div>
      </div>
    </div>
    <div class="flex flex-col lg:flex-row justify-center mt-7 px-5">
      <button @click="updateFight('O')" :disabled="isDisabled.open" class="btn btn-success btn-lg m-2">
        <span v-show='!isLoading.open'>OPEN</span><span v-show='isLoading.open'>Processing...</span></button>
      <button @click="updateFight('C')" :disabled="isDisabled.close" class="btn btn-danger btn-lg m-2">
        <span v-show='!isLoading.close'>CLOSE</span><span v-show='isLoading.close'>Processing...</span></button>
      <button @click="doneFight()" :disabled="isDisabled.done" class="btn btn-secondary btn-lg m-2">
        <span v-show='!isLoading.done'>DONE</span><span v-show='isLoading.done'>Loading...</span></button>
    </div>
    <div class="flex flex-col lg:flex-row lg:gap-0 justify-evenly mt-7 mb-4 px-5">
      <button id="cancel-fight" v-show='!isLoading.cancel' :disabled='isLoading.cancel' @click="cancelFight()" class="btn btn-primary btn-sm">
        Cancel Fight</button>
      <button v-show='isLoading.cancel' :disabled='!isLoading.cancel' class="btn btn-primary btn-sm">
        Loading...</button>
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
      }

    },
    mounted() {
      fetch('fight/current')
        .then(resp => resp.json())
        .then(json => {
          this.fight = json.current
          this.message = this.setFightStatus(json.current)
          this.total = json.bets
        });

      window.Echo.channel('bet')
        .listen('.bet', async (e) => {
          // console.log(e, 'bet');
          if (e.bet.side === 'M') {
            this.total.meron = this.total.meron + e.bet.amount
          } else {
            this.total.wala = this.total.wala + e.bet.amount
          }
        });
    },
    methods: {
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

