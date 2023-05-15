<template> 
  <div id="header-box" class="header-container">
    <header>
      <nav> 
        <div class="left-logos" v-if="this.desc">
            <div id="navbar-shop" class="navlogo">
              <router-link :to="{name:'Shop'}">
                <figure class="logo-click">
                  <img src="../../assets/navbar_logos/shop.png" />
                  <figcaption class="l-text"> {{ $t('nav.shop') }} </figcaption>
                </figure>
              </router-link>
              <!-- Do not remove these, they are for mobile nav. -->
              <router-link :to="{name:'Shop'}" v-if="this.isActive" @click="openNav">
                <span class="mob-cap"> {{ $t('nav.shop') }} </span>
              </router-link>
            </div>
          <div id="navbar-cart" class="navlogo">
            <router-link :to="{name:'Cart'}">
              <figure class="logo-click">
                  <img src="../../assets/navbar_logos/cart.png" />
                  <figcaption class="l-text">  {{ $t('nav.cart') }}  </figcaption>
              </figure>
              <div v-if="this.$store.state.myCartSize > 0" id="cart-notice-container">
                <img v-if="!this.isActive" id="cart-notice" class="cart-notice" src="../../assets/navbar_logos/notice.png"/>
                <p v-if="!this.isActive" id="cart-notice-text">{{this.$store.state.myCartSize}}</p>
                <img v-if="this.isActive" id="cart-notice-mobile" class="cart-notice" src="../../assets/navbar_logos/notice.png"/>
                <p v-if="this.isActive" id="cart-notice-text-mobile">{{this.$store.state.myCartSize}}</p>
              </div>
            </router-link>
            <router-link :to="{name:'Cart'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap">  {{ $t('nav.cart') }}  </span>
            </router-link>
          </div>
          <div id="navbar-article" class="navlogo">
            <router-link :to="{name:'New_Article'}">
                <figure id="add-logo" :class="[`logo-click`,`add`]">
                    <img class="add" src="../../assets/navbar_logos/add.png" />
                    <figcaption :class="[`l-text`,`add`]">  {{ $t('nav.new_item') }}  </figcaption>
                </figure>
            </router-link>
            <router-link :to="{name:'New_Article'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap">  {{ $t('nav.new_item') }}  </span>
            </router-link>
          </div>
          <div id="navbar-members" class="navlogo">
             <router-link :to="{name:'Members'}">
              <figure class="logo-click">              
                  <img src="../../assets/navbar_logos/members.png" />
                  <figcaption class="l-text">  {{ $t('nav.members') }}  </figcaption>
              </figure>
            </router-link>
            <router-link :to="{name:'Members'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap">  {{ $t('nav.members') }}  </span>
            </router-link>
          </div>
          <div id="navbar-faq" @mouseover="displayFAQDropdown" class="navlogo">
            <div id="faq-dropdown" class="dropdown"></div>
            <router-link :to="{name:'FAQ', params:{tab: 'faq'}}">
              <figure id="faq-logo" @mouseover="highlightFAQLogo" class="logo-click">
                  <img src="../../assets/navbar_logos/question.png" style="height:24px; width:24px" />
              
                  <figcaption class="l-text"> {{ $t('faq.faq') }} </figcaption>
              </figure>
            </router-link>
            <!--To add another link in the dropdownmeny, add another router-link here, then follow the comments in FAQ.vue--> 
            <div id="faq-content" @mouseover="highlightFAQLogo" class="dropdown-content">
                  <div id="navbar-faq-dropdown-faq"><router-link :to="{name:'FAQ', params:{tab: 'faq'}}">{{ $t('faq.common_questions') }}</router-link></div>
                  <router-link :to="{name:'FAQ', params:{tab: 'trading_tips'}}">{{ $t('faq.trading_tips') }}</router-link><!--FAQ/TradingTips.vue-->
                  <router-link :to="{name:'FAQ', params:{tab: 'information'}}">{{ $t('faq.association_information') }}</router-link> <!--FAQ/Information.vue-->
                  <router-link :to="{name:'FAQ', params:{tab: 'policy'}}">{{ $t('faq.policy') }}</router-link><!--FAQ/Policy.vue-->
                </div>
            <router-link :to="{name: 'FAQ'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap"> FAQ </span>
            </router-link>
          </div>
        </div>
        
        <div class="middle-logo">
          <div id="navbar-home" class="navlogo">
            <figure>
              <router-link :to="{name:'Home'}" >
                <img src="/nav_logo.png" />
              </router-link>
            </figure>
          </div>
        </div>
        <div class="right-logos" v-if="this.desc">
          <div id="navbar-notifications" class="navlogo" v-if="!this.isActive" @click.prevent="setNotificationsToSeen">
              <Notifications></Notifications>
          </div> 
          <div id="navbar-chat" class="navlogo">
            <router-link :to="{name:'Chat'}">
              <figure class="logo-click">
                  <img src="../../assets/navbar_logos/chat.png" />
                  <figcaption class="l-text"> {{ $t('chat.title') }} </figcaption>
              </figure>
            </router-link>
            <router-link :to="{name: 'Chat'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap"> {{ $t('chat.title') }} </span>
            </router-link>
          </div>

          <div id="navbar-profile" @mouseover="displayDropdown" class="navlogo">
              <div id="profile-dropdown" class="dropdown">
                <router-link :to="{name:'Profile', params:{tab: 'profile'}}">
                  <figure id="profile-logo" @mouseover="highlightLogo" class="logo-click">
                    <img src="../../assets/navbar_logos/profile.png" alt="profil knapp"/>
                    <figcaption class="l-text">  {{ $t('nav.my_account') }}  </figcaption>
                  </figure>
                </router-link>
                <div id="profile-content" @mouseover="highlightLogo" class="dropdown-content">
                  <div id="navbar-profile-dropdown-profile"><router-link :to="{name:'Profile', params:{tab: 'profile'}}"> {{ $t('nav.my_profile') }} </router-link></div>
                  <router-link :to="{name:'Profile', params:{tab: 'purchases'}}"> {{ $t('nav.my_buy_sell') }} </router-link>
                  <router-link :to="{name:'Profile', params:{tab: 'articles'}}"> {{ $t('nav.my_items') }} </router-link>
                  <!--<router-link :to="{name:'Profile', params:{tab: 'requests'}}"> {{ $t('nav.my_purchase_requests') }} </router-link>-->
                  <router-link :to="{name:'Profile', params:{tab: 'economy'}}"> {{ $t('nav.balance') }} </router-link>
                </div>
              </div>
              <router-link :to="{name:'Profile', params:{tab: 'profile'}}" v-if="this.isActive" @click="openNav">
                <span class="mob-cap"> {{ $t('nav.my_account') }} </span>
              </router-link>
          </div>
          <div id="navbar-event" class="navlogo">
            <router-link :to="{name:'Event'}">
              <figure class="logo-click">
                  <img src="../../assets/navbar_logos/events.png" />
                  <figcaption class="l-text">  {{ $t('nav.events') }}  </figcaption>
              </figure>
            </router-link>
            <router-link :to="{name: 'Event'}" v-if="this.isActive" @click="openNav">
              <span class="mob-cap">  {{ $t('nav.events') }}  </span>
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
  
</template>

<script>
// Component that represent the navbar, is responsive for mobile aswell
import { useRouter } from 'vue-router'
import { logout, setNotificationsToSeen, getCart } from '../../serverFetch.js'
import Notifications from './Notifications.vue'
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
      const faqDrop = document.getElementById('faq-content')
      if (faqDrop != null) {
        faqDrop.style.display = 'none'
        const faqLogo = document.getElementById('faq-logo')
        faqLogo.classList.remove('active-dropdown')
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

    highlightFAQLogo () {
      if (!this.isActive) {
        const logo = document.getElementById('faq-logo')
        logo.classList.add('active-dropdown')
      }
    },
    displayFAQDropdown () {
      if (!this.dropdownActive && !this.isActive) {
        const content = document.getElementById('faq-content')
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
