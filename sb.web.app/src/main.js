import { createApp } from 'vue'
import App from './App.vue'
import router from './router'
import store from './store'
import 'animate.css'
import BootstrapVue3 from 'bootstrap-vue-3'
import 'bootstrap/dist/css/bootstrap.css'
import 'bootstrap-vue-3/dist/bootstrap-vue-3.css'
import './assets/style.css'
import { VueWindowSizePlugin } from 'vue-window-size/option-api'
import VueSplide from '@splidejs/vue-splide'
import VueHotjar from 'vue-hotjar-next'
import i18n from './i18n'

const app = createApp(App)
app.use(store)
app.use(router)
app.use(BootstrapVue3)
app.use(VueWindowSizePlugin)
app.use(VueSplide)
app.use(i18n)

/*
app.use(VueHotjar, {
  id: 3081354
}) */

app.mount('#app')
