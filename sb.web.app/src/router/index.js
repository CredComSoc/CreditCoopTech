import { createRouter, createWebHistory, createWebHashHistory } from 'vue-router'
import { authenticate } from '../serverFetch'
import Home from '../views/Home.vue'
import Login from '../components/Login.vue'
import Shop from '../components/userstory4/parent.vue'
import NewArticle from '../components/CreateArticle/NewArticle.vue'
import StepTwo from '../components/CreateArticle/StepTwo.vue'
import NewArticle3 from '../components/CreateArticle/NewArticle3.vue'
import Profile from '../components/min_sida/profile.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    props: true
  },
  {
    path: '/login',
    name: 'Login',
    component: Login
  },
  {
    path: '/shop',
    name: 'Shop',
    component: Shop
  },
  {
    path: '/events',
    name: 'Events',
    component: Home // SKA BYTAS UT
  },
  {
    path: '/add/article',
    name: 'New_Article',
    component: NewArticle
  },
  {
    path: '/members',
    name: 'Members',
    component: Home // SKA BYTAS UT
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    props: true
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach(async (to, from) => {
  const auth = await authenticate()
  if (to.name !== 'Login') {
    if (!auth) {
      return { name: 'Login' }
    }
  } else if (auth) {
    return { name: 'Home' }
  }
}) 

export default router
