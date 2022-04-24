<template>
  <div>
    <div id="header-box" class="header-container">
      <header>
        <nav>
          <div class="navlogo">
            <router-link :to="{name:''}" @click="logOut">
              <figure class="logo-click">
                  <img src="../../assets/link_arrow/popup_close.png" alt="logut knapp"/>
                  <figcaption class="l-text"> Logout </figcaption>
              </figure>
            </router-link>
          </div>
        </nav>
      </header>
    </div>
    <div id="space">
    </div>
  </div>
</template>

<script>
// Component that represent the navbar, is responsive for mobile aswell
import { useRouter } from 'vue-router'
import { logout } from '../../serverFetch.js'
const router = useRouter()

export default {
  data () {
    return {
      desc: true, // is in desktop mode of navbar
      isActive: false, // if mobile version has its button pressed
      dropdownActive: false // if a dropdown menu is active
    }
  },
  name: 'Navbar',
  props: ['screenWidth'],
  watch: {
    // When screen resize, make navbar responsive
    screenWidth: {
      handler: function (scrWidth) {
        if (scrWidth < 861 && !this.isActive) {
          this.desc = false
        } else {
          this.desc = true
          if (scrWidth > 861) {
            this.isActive = false
            const box = document.getElementById('header-box')
            box.style.height = 'fit-content'
            box.style.overflow = 'inherit'
          }
        }
      }
    }
  },
  mounted () {
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
        dropdown.style.display = 'none'
        dropdown = document.getElementById('bell-dropdown')
        dropdown.style.display = 'none'
        this.dropdownActive = false
        let logo = document.getElementById('add-logo')
        logo.classList.remove('active-dropdown')
        logo = document.getElementById('bell-logo')
        logo.classList.remove('active-dropdown')
      } else {
        if ([...e.target.classList].includes('add')) {
          const dropdown = document.getElementById('upload-dropdown')
          dropdown.style.display = 'block'
          this.dropdownActive = true
          const logo = document.getElementById('add-logo')
          logo.classList.add('active-dropdown')
        } else if ([...e.target.classList].includes('notice')) {
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
    // make height for mobile navbar responsive and scrollable
    resizeNav () {
      if (this.isActive) {
        const box = document.getElementById('header-box')
        const height = window.innerHeight

        if (height < 550) {
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
      this.$router.push({ name: 'Login' })
      logout().then(() => {
        window.location.reload()
      })
    }
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
  height:75px;
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
  gap: 40px;
}

.middle-logo {
  flex-shrink: 0;
  margin-left: 30px;
  margin-right: 30px;
  margin-bottom: 3px;
  margin-top: 3px;
  height: 100%;
}

#notice {
  position: absolute;
  height: 11px;
  width: 11px;
  margin-top: 4px;
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

.notice-desc, .notice-title {
  font-family: Ubuntu;
}

.notice-desc {
  font-weight: 300;
  font-size: 10px;
}

.notice-title {
  font-weight: 500;
  font-size: 16px;
  margin-bottom: 5px;
}

.notice-img {
  float: right;
  top: 50%;
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

@media (max-width: 860px) {

 .header-container {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
 }

 #space {
   height: 69px;
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
    left: 0;
    top: 0;
    margin:0;
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
