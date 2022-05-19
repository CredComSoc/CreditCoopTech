import { createRouter, createWebHistory, createWebHashHistory } from 'vue-router'
import { authenticate, checkAdminStatus } from '../serverFetch'
import Home from '../views/Home.vue'
import Navbar from '../components/Navbar.vue'
import Login from '../components/Login.vue'
import Forgot from '../components/Forgot.vue'
import Reset from '../components/Reset.vue'
import Shop from '../components/userstory4/parent.vue'
import Members from '../components/userstory5/members.vue'
import NewArticle from '../components/CreateArticle/NewArticle.vue'
import Profile from '../components/min_sida/profile.vue'
import AdminHome from '../components/AdminSection/AdminHome.vue'
import userProfile from '../components/userstory5/userProfile.vue'
import ShoppingCart from '../components/ShoppingCart/ShoppingCart.vue'
import Chat from '../components/Chat/Chat.vue'

const userRoutes = ['Home', 'Shop', 'Events', 'New_Article', 'Members', 'MemberUserprofile', 'Profile', 'Cart', 'Chat']
const adminRoutes = ['AdminHome']

const routes = [
  // USER ROUTES
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
    path: '/forgot',
    name: 'Forgot',
    component: Forgot
  },
  {
    path: '/reset/:token',
    name: 'Reset',
    component: Reset,
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
    path: '/add/article',
    name: 'New_Article',
    component: NewArticle,
    props: true
  },
  {
    path: '/members',
    name: 'Members',
    component: Members 
  },
  {
    path: '/members/:userprofile',
    name: 'MemberUserprofile',
    component: userProfile,
    props: true
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    props: true
  },
  {
    path: '/cart',
    name: 'Cart',
    component: ShoppingCart,
    props: true
  },
  {
    path: '/chat',
    name: 'Chat',
    component: Chat,
    props: true
  },
  // ADMIN ROUTES
  {
    path: '/admin',
    name: 'AdminHome',
    component: AdminHome
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach(async (to, from) => {
  //Navbar.forceRerender()
  const auth = await authenticate()
  if (to.name !== 'Login' && to.name !== 'Forgot' && to.name !== 'Reset') {
    if (!auth) {
      return { name: 'Login' }
    } else {
      const admin = await checkAdminStatus()
      if (admin) {
        if (userRoutes.includes(to.name)) {
          return { name: 'AdminHome' }
        }
      } else {
        if (adminRoutes.includes(to.name)) {
          return { name: 'Home' }
        }
      }
    }
  } else if (auth) {
    const admin = await checkAdminStatus()
    if (admin) {
      return { name: 'AdminHome' }
    } else {
      return { name: 'Home' }
    }  
  }
}) 

export default router
