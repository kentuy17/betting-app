import {createApp} from 'vue'
import axios from 'axios';
import Bet from './components/Bet.vue'


//create an axios instance in order to use it globally with same config
const instance = axios.create({
  baseURL: import.meta.env.VUE_APP_API_URL,
  withCredentials: false,
  headers: {
    Accept: 'application/json',
    'Content-Type': 'application/json',
  },

})

const app = createApp(Bet)

app.config.globalProperties.axios = instance;
app.mount("#betting-component")
// createApp(Bet).mount("#betting-component")