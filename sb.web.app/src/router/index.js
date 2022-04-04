import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Shop from '../components/userstory4/parent.vue'
import Profile from '../components/profile.vue'
import NewArticle from '../components/CreateArticle/NewArticle.vue'
import StepTwo from '../components/CreateArticle/StepTwo.vue'
import NewArticle3 from '../components/CreateArticle/NewArticle3.vue'

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
    path: '/profile',
    name: 'Profile',
    component: Profile
  },
  {
    path: '/add/article',
    name: 'New_Article',
    component: NewArticle
  } 
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
