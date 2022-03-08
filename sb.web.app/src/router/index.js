import { createRouter, createWebHistory } from 'vue-router'
import Home from '../views/Home.vue'
import Shop from '../components/userstory4/parent.vue'
import Profile from '../components/profile.vue'
import NewArticle from '../components/NewArticle.vue'
import NewArticle2 from '../components/NewArticle2.vue'
import NewArticle3 from '../components/NewArticle3.vue'

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
    path: '/add_article/1',
    name: 'New_Article',
    component: NewArticle
  },
  {
    path: '/add_article/2',
    name: 'New_Article_2',
    component: NewArticle2
  },
  {
    path: '/add_article/3',
    name: 'New_Article_3',
    component: NewArticle3
  }  

]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

export default router
