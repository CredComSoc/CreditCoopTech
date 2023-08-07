<template>
  <div id="header-box" class="header-container">
    <header>
      <nav>
        <div class="left-logos" v-if="this.desc">
            <div id="navbar-shop" class="navlogo">
              <router-link :to="{name:'AdminShop'}">
                <figure class="logo-click">
                  <img src="../../assets/navbar_logos/shop.png" />
                  <figcaption class="l-text">{{ $t('nav.shop') }} </figcaption>
                </figure>
              </router-link>
              <!-- Do not remove these, they are for mobile nav. -->
              <router-link :to="{name:'AdminShop'}" v-if="this.isActive" @click="openNav">
                <span class="mob-cap">{{ $t('nav.shop') }} </span>
              </router-link>
            </div>
          <div id="navbar-members" class="navlogo">
             <router-link :to="{name:'AdminAddMember'}">
              <figure class="logo-click">              
                  <img src="../../assets/navbar_logos/add.png" />
                  <figcaption class="l-text"> {{ $t('user.addMember') }} </figcaption>
              </figure>
            </router-link>
            <router-link :to="{name:'AdminAddMember'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap">  {{ $t('nav.members') }}  </span>
            </router-link>
          </div>
          <div id="navbar-members" class="navlogo">
             <router-link :to="{name:'AddCategories'}">
              <figure class="logo-click">              
                  <img src="../../assets/navbar_logos/add.png" />
                  <figcaption class="l-text"> {{ $t('user.addCategory') }} </figcaption>
              </figure>
            </router-link>
            <router-link :to="{name:'AdminAddMember'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap">  {{ $t('nav.members') }}  </span>
            </router-link>
          </div>
          <div id="navbar-economy" class="navlogo">
             <router-link :to="{name:'AdminEconomy'}">
              <figure class="logo-click">              
                  <img src="../../assets/navbar_logos/economy.png" style='height: 25px; width: 25px;'/>
                  <figcaption class="l-text"> {{ $t('user.financialOverview') }} </figcaption>
              </figure>
            </router-link>
            <router-link :to="{name:'AdminEconomy'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap"> {{ $t('Economy') }} </span>
            </router-link>
          </div>
        </div>
        <div class="middle-logo">
          <div id="navbar-home" class="navlogo">
            <figure>
              <router-link :to="{name:'Home'}" >
                <img src="../../assets/navbar_logos/sb.png" />
              </router-link>
            </figure>
          </div>
        </div>
        <div class="right-logos" v-if="this.desc">
          <div id="navbar-notifications" class="navlogo" v-if="!this.isActive" @click.prevent="setNotificationsToSeen">
              <Notifications></Notifications>
          </div> 
          <div id="navbar-chat" class="navlogo">
            <router-link :to="{name:'AdminChat'}">
              <figure class="logo-click">
                  <img src="../../assets/navbar_logos/chat.png" />
                  <figcaption class="l-text"> {{ $t('chat.title') }} </figcaption>
              </figure>
            </router-link>
            <router-link :to="{name: 'AdminChat'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap"> {{ $t('chat.title') }} </span>
            </router-link>
          </div>
          <div id="navbar-settings" class="navlogo">
            <router-link :to="{name:'NetworkSetting'}">
              <figure class="logo-click">
                  <img src="../../assets/navbar_logos/gear.png"/>
                  <figcaption class="l-text"> {{ $t('Preferences') }}</figcaption>
              </figure>
            </router-link>
            <router-link :to="{name:'NetworkSetting'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap"> {{ $t('Preferences') }}</span>
            </router-link>
          </div>
        
        <div id="navbar-logout" class="navlogo">
            <router-link :to="{name:''}" @click="logOut">
              <figure class="logo-click">
                  <img src="../../assets/link_arrow/popup_close.png" alt="$t('nav.sign_out')"/>
                  <figcaption class="l-text">  {{ $t('nav.sign_out') }}  </figcaption>
              </figure>
            </router-link>
            <router-link :to="{name:''}" @click="logOut" v-if="this.isActive">
              <span class="mob-cap">  {{ $t('nav.sign_out') }} </span>
            </router-link>
          </div>
        </div> 
        <div id="bell-container" class="navlogo" v-if="!this.desc && !this.isActive" @click.prevent="setNotificationsToSeen">
            <Notifications></Notifications>
        </div> 
        <!-- "Hamburger menu" / "Bar icon" to toggle the navigation links -->
        <button id="mob-nav-btn" class="icon" @click="openNav">
          <i class="fa fa-bars"></i>
        </button>
      </nav>
    </header>
    
  </div>
  <div id="space">
  </div>

</template>

<script>
// Component that represent the navbar, is responsive for mobile aswell
import { useRouter } from 'vue-router'
import { logout, setNotificationsToSeen, getCart } from '../../serverFetch.js'
import Notifications from '../Navbar/Notifications.vue'
const router = useRouter()

export default {
  components: {
    Notifications
  },
  data () {
    return {
      desc: true, // is in desktop mode of navbar
      isActive: false, // if mobile version has its button pressed
      dropdownActive: false, // if a dropdown menu is active
      newNotifications: [],
      oldNotifications: [],
      componentKey: 0
    }
  },
  name: 'Navbar',
  props: ['screenWidth'],
  watch: {
    // When screen resize, make navbar responsive
    screenWidth: {
      handler: function (scrWidth) {
        this.handleScrWidth(scrWidth)
      }
    }
  },
  mounted () {
    this.handleScrWidth(this.screenWidth)
    this.resizeNav()
    window.addEventListener('resize', this.resizeNav)
    window.addEventListener('click', (e) => {
      const profileDrop = document.getElementById('profile-content')
      if (profileDrop != null) {
        profileDrop.style.display = 'none'
        const profileLogo = document.getElementById('profile-logo')
        profileLogo.classList.remove('active-dropdown')
      }
      
      if (this.dropdownActive) {
        let dropdown = document.getElementById('upload-dropdown')
        if (dropdown !== null) {
          dropdown.style.display = 'none'
        }
        dropdown = document.getElementById('bell-dropdown')
        if (dropdown != null) {
          dropdown.style.display = 'none'
        }
        let logo = document.getElementById('add-logo')
        if (logo !== null) {
          logo.classList.remove('active-dropdown')
        }

        logo = document.getElementById('bell-logo')
        if (logo !== null) {
          logo.classList.remove('active-dropdown')
        }
        this.dropdownActive = false
      } else {
        if ([...e.target.classList].includes('notice')) {
          const dropdown = document.getElementById('bell-dropdown')
          dropdown.style.display = 'block'
          this.dropdownActive = true
          const logo = document.getElementById('bell-logo')
          logo.classList.add('active-dropdown')
        }
      }
    })
  },
  methods: {
    // open mobile version of navbar
    openNav () {
      if (this.desc) {
        this.desc = false
        this.isActive = false
      } else {
        this.desc = true
        this.isActive = true
      }
      this.resizeNav()
    },
    handleScrWidth (scrWidth) {
      if (scrWidth <= 1025 && !this.isActive) {
        this.desc = false
      } else {
        this.desc = true
        if (scrWidth > 1025) {
          this.isActive = false
          const box = document.getElementById('header-box')
          box.style.height = 'fit-content'
          box.style.overflow = 'inherit'
        }
      }
    },
    // make height for mobile navbar responsive and scrollable
    resizeNav () {
      if (this.isActive) {
        const box = document.getElementById('header-box')
        const height = window.innerHeight

        if (height < 720) {
          box.style.height = '' + height + 'px'
        } else {
          box.style.height = 'fit-content'
        }

        box.style.overflow = 'scroll'
      } else {
        const box = document.getElementById('header-box')
        box.style.height = 'fit-content'
        box.style.overflow = 'inherit'
      }
    },
    highlightLogo () {
      if (!this.isActive) {
        const logo = document.getElementById('profile-logo')
        logo.classList.add('active-dropdown')
      }
    },
    displayDropdown () {
      if (!this.dropdownActive && !this.isActive) {
        const content = document.getElementById('profile-content')
        content.style.display = 'block'
      }
    },
    logOut () {
      logout().then(() => {
        window.location.reload()
      })
    },
    moveNotification (notification) {
      this.newNotifications.splice(this.newNotifications.indexOf(notification), 1)
      this.oldNotifications.unshift(notification)
    },
    setNotificationsToSeen
  }
}
</script>

<style scoped>
* {
  margin: 0;
  padding: 0;
  box-sizing: border-box;
}

html {
  scroll-behavior: smooth;
}

#space {
  height:80px;
}

.header-container {
  width: 100%;
  height: 80px;
  top: 0;
  z-index: 3;
  position: fixed;
  background: #fff;
  border-bottom: 1px solid black;
}

header {
  margin-left: auto;
  margin-right: auto;
  height: 100%;
  margin-bottom: 0;
  margin-top: 0;
  width: 75%;
}

 nav {
  background-color: white;
  overflow: visible;
  display: flex;
  margin-bottom: 0;
  margin-top: 0;
  flex-direction: row;
  justify-content: space-evenly;
}

#bell-container {
  position: absolute;
  left:5%;
  top:30%;
}

a {
  text-decoration: none;
  color: black;
}

a:hover {
  color: black;
}

.l-text {
  font-family: 'Roboto';
  font-weight: 300;
  font-style: normal;
  font-size: 12px;
  text-align: center;
}

.logo-text {
  margin-bottom: 40px;
}

.left-logos, .right-logos {
  display: flex;
  align-items: center;
  gap: 35px;
}

.middle-logo {
  flex-shrink: 0;
  margin-left: 40px;
  margin-right: 20px;
  margin-bottom: 3px;
  margin-top: 3px;
  height: 100%;
}

.middle-logo:hover {
  transform: scale(1.05);
}

#notice {
  position: absolute;
  height: 11px;
  width: 11px;
  margin-top: 4px;
}

#cart-notice-container {
  position: absolute;
  text-align: center;
  margin-top: -58px;
  margin-left: 14px;
  font-size: 14px;
}

#cart-notice {
  position: absolute;
  height: 20px;
  width: 20px;

}

#cart-notice-text {
  position: relative;
  height: 20px;
  width: 20px;
  margin-top: -2px;
  margin-left: 19px;
}

#cart-notice-mobile {
  position: absolute;
  height: 20px;
  width: 20px;
  margin-left: 28px;
  margin-top: 4px;
}

#cart-notice-text-mobile {
  position: relative;
  height: 20px;
  width: 20px;
  margin-left: 75px;
  margin-top: 3px;
}

figure {
  display: inline-block;
  text-align: center;
}

.navlogo {
  flex-shrink: 0;
}

figcaption {
  margin-top: 7px;
}

.dropdown {
  position: relative;
  display: inline-block;
}

.dropdown-content a, #prev-list-content p, #new-list-content p {
  color:black;
  text-decoration: none;
  font-family: Roboto;
  font-weight: 300;
  font-size: 12px;
  font-style: normal;
}

.dropdown-content {
  display: none;
  position: absolute;
  background-color: #E5E5E5;
  min-width: 160px;
  box-shadow: 0px 8px 16px 0px rgba(0,0,0,0.2);
  z-index: 1;
  margin-top: 2px;
}

.dropdown-content a {
  color: black;
  padding: 12px 16px;
  text-decoration: none;
  display: block;
  border-bottom: 1px solid #CBCACA;
}
 /* Style the links inside the navigation bar  */
.dropdown-content a:hover {
  display: block;
  background-color: #E5F0FD;
}

.active-dropdown {
  border-bottom: 2px solid black;
  transform: scale(1.05);
}

.logo-click:hover {
    border-bottom: 2px solid black;
    transform: scale(1.05);
}

.icon {
    display: none;
}

@media (min-width: 1100px) {
  #profile-dropdown:hover .dropdown-content {
    display: block;  
    border-top: 1px;
  }
}

@media (max-width: 1025px) {

 .header-container {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
 }

 /* Hide scrollbar for Chrome, Safari and Opera */
 .header-container::-webkit-scrollbar {
    display: none;
 }

  header {
    width: 100%;
  }

  header nav {
    overflow: hidden;
    background-color: #fff;
    margin: 0;
    flex-direction: column-reverse;
  }

  nav .middle-logo {
    align-self: center;
    justify-self: center;
    order: 4;
  }

  nav .left-logos {
    border-top: 1px solid #CBCACA;
    order: 3;
    margin-bottom: 30px;
    padding-top: 25px;
  }

  /* Hide the links inside the navigation menu (except for logo/home) */
  .left-logos, .right-logos {
    flex-direction: column;
    margin: 0;
    gap: 30px;
    align-items: center;
  }

  .left-logos .navlogo, .right-logos .navlogo {
    width: 100%;
    border-bottom: 1px solid #CBCACA;
  }

  .left-logos .navlogo, .right-logos .navlogo {
    height: 50px;
  }

  .l-text {
    color: black;
    text-decoration: none;
    font-size: 0px;
  }

  .left-logos figure, .right-logos figure {
    margin-left: 70px;
  }

  .mob-cap {
    position: absolute;
    left: 50%;
    font-family: Roboto;
    font-size: 20px;
  }

  /* Style the hamburger menu */
  .icon {
    position: absolute;
    top:0;
    right:0;
    transform: scale(2.4);
    margin-right: 20px;
    margin-top: 25px;
    display: block;
    border: none;
    background: #fff;
    cursor: pointer;
  }

  nav a:hover {
    color: grey;
  }
}

</style>
