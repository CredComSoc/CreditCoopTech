import { createApp } from 'vue'
import './assets/style.css'
import App from './App.vue'
import router from './router'
import store from './store'
import 'animate.css'
import BootstrapVue3 from 'bootstrap-vue-3'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css'
import { VueWindowSizePlugin } from 'vue-window-size/option-api'

const app = createApp(App)
app.use(store)
app.use(router)
app.use(BootstrapVue3)
app.use(VueWindowSizePlugin)
app.mount('#app')
