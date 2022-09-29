import { createRouter, createWebHistory } from 'vue-router'
import { authenticate, checkAdminStatus } from '../serverFetch'
import store from '../store'
import Home from '../components/Home/Home.vue'
import Login from '../components/Login/Login.vue'
import Forgot from '../components/Login/Forgot.vue'
import Reset from '../components/Login/Reset.vue'
import Shop from '../components/Shop/Shop.vue'
import Members from '../components/Members/members.vue'
import NewArticle from '../components/CreateArticle/NewArticle.vue'
import Profile from '../components/Profile/Profile.vue'
import AdminHome from '../components/AdminSection/AdminHome.vue'
import userProfile from '../components/Members/userProfile.vue'
import ShoppingCart from '../components/ShoppingCart/ShoppingCart.vue'
import Chat from '../components/Chat/Chat.vue'
import About from '../components/Footer/About.vue'
import FAQ from '../components/FAQ/FAQ.vue'

const userRoutes = ['Home', 'Shop', 'Events', 'New_Article', 'Members', 'MemberUserprofile', 'Profile', 'Cart', 'Chat', 'FAQ']
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
    path: '/FAQ',
    name: 'FAQ',
    component: FAQ,
    props: true
  },
  {
    path: '/chat',
    name: 'Chat',
    component: Chat,
    props: true
  },
  {
    path: '/about',
    name: 'About',
    component: About
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
  routes,
  scrollBehavior: (to, from, savedPosition) => {
    window.scrollTo(0, 0)
  }
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
  } else if (to.name === 'Forgot' || to.name === 'Reset') {
    return { name: to.name }
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
