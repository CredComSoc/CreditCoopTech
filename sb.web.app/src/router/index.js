import { createRouter, createWebHistory, createWebHashHistory } from 'vue-router'
import { authenticate, checkAdminStatus } from '../serverFetch'
import Home from '../views/Home.vue'
import Navbar from '../components/Navbar.vue'
import Login from '../components/Login.vue'
import Shop from '../components/userstory4/parent.vue'
import Members from '../components/userstory5/members.vue'
import NewArticle from '../components/CreateArticle/NewArticle.vue'
import StepTwo from '../components/CreateArticle/StepTwo.vue'
import NewArticle3 from '../components/CreateArticle/NewArticle3.vue'
import Profile from '../components/min_sida/profile.vue'
<<<<<<< HEAD
import AdminHome from '../components/AdminSection/AdminHome.vue'

const userRoutes = ['Home', 'Shop', 'Events', 'New_Article', 'Members']
const adminRoutes = ['AdminHome']
=======
import userProfile from '../components/userstory5/userProfile.vue'
>>>>>>> origin/robin-linus-3

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
    component: Members 
  },
  {
    path: '/profile',
    name: 'Profile',
    component: Profile,
    props: true
  },
<<<<<<< HEAD
  // ADMIN ROUTES
  {
    path: '/admin',
    name: 'AdminHome',
    component: AdminHome
=======
  {
    path: '/members/:userprofile',
    name: 'MemberUserprofile',
    component: userProfile,
    props: true
>>>>>>> origin/robin-linus-3
  }
]

const router = createRouter({
  history: createWebHistory(process.env.BASE_URL),
  routes
})

router.beforeEach(async (to, from) => {
  //Navbar.forceRerender()
  const auth = await authenticate()
  if (to.name !== 'Login') {
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
