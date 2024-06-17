import { createApp } from 'vue'
import { createVfm } from 'vue-final-modal'
import Fight from './components/Fight.vue'

const app = createApp(Fight)
const vfm = createVfm()

app.use(vfm).mount("#fight-component")
