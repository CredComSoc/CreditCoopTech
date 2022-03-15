import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Shop from '../components/userstory4/parent.vue'
import Profile from '../components/min_sida/profile.vue'
import NewArticle from '../components/NewArticle.vue'

const routes = [
  {
    path: '/',
    name: 'Home',
    component: Home,
    props: true
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
    path: '/add_article',
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

export default router
