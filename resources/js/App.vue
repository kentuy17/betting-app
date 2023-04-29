<script>
export default {
    data() {
      return {
        message: '_____',
        fight: [],
        fightNo: 0
      }
    },
    mounted() {
      fetch('fight/current')
        .then(resp => resp.json())
        .then(json => {
          this.fight = json.data
          this.message = this.setFightStatus(json.data)
        });
    },
    methods: {
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
      }
    }
}
</script>