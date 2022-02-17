import { createApp } from 'vue'
import App from './App.vue'
import './assets/style.css'
import 'bootstrap/dist/css/bootstrap.min.css'
import 'bootstrap'
import buefy from 'buefy'
import 'buefy/dist/buefy.css'

const app = createApp(App)
app.use(buefy)
app.mount('#app')
