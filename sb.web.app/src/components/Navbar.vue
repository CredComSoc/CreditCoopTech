<template>
  <div id="header-box" class="header-container">
    <header>
      <nav>
        <div class="left-logos" v-if="this.desc">
            <div class="navlogo">
              <a href="http://localhost:8080/shop">
                <figure class="logo-click">
                  <img src="../assets/navbar_logos/shop.png" alt="shop knapp"/>
                  <figcaption class="l-text">Shop</figcaption>
                </figure>
              </a>
              <a href="#" v-if="this.isActive">
                <span class="mob-cap"> Shop </span>
              </a>
            </div>
          <div class="navlogo">
            <a href="http://localhost:8080/events">
              <figure class="logo-click">
                  <img src="../assets/navbar_logos/events.png" alt="shop knapp" id="event-logo"/>
                  <figcaption class="l-text"> Events </figcaption>
              </figure>
            </a>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Events </span>
            </a>
          </div>
          <div class="navlogo" v-if="!this.isActive">
            <div class="dropdown">
              <a href="#">
                <figure id="add-logo" :class="[`logo-click`,`add`]">
                    <img class="add" src="../assets/navbar_logos/add.png" alt="shop knapp"/>
                    <figcaption :class="[`l-text`,`add`]"> Lägg upp </figcaption>
                </figure>
              </a>
              <div id="upload-dropdown" class="dropdown-content">
                  <a href="#">Ny artikel </a>
                  <a href="#">Nytt event </a>
              </div>
            </div>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Lägg upp </span>
            </a>
          </div>
          <div class="navlogo">
            <a href="http://localhost:8080/members">
              <figure class="logo-click">              
                  <img src="../assets/navbar_logos/members.png" alt="shop knapp"/>
                  <figcaption class="l-text"> Medlemmar </figcaption>
              </figure>
            </a>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Medlemmar </span>
            </a>
          </div>
        </div>
        <div class="middle-logo">
          <div class="navlogo">
            <figure>
              <a href="/">
                <img src="../assets/navbar_logos/sb.png" alt="shop knapp"/>
              </a>
            </figure>
          </div>
        </div>
        <div class="right-logos" v-if="this.desc">
          <div class="navlogo" v-if="!this.isActive">
            <div id="click-dropdown" class="dropdown">
              <a href="#">
                <figure id="bell-logo" :class="[`logo-click`,`notice`]">
                    <img id="notice" class="notice" src="../assets/navbar_logos/notice.png"/>
                    <img id="bell" class="notice" src="../assets/navbar_logos/bell.png" alt="shop knapp"/>
                    <figcaption class="l-text"> Notiser </figcaption>
                </figure>
              </a>
              <div id="bell-dropdown" class="dropdown-content">
                <div id="new-notice-list">
                  <a href="#">
                    <div>
                      <p class="notice-title">Nya</p>
                      <img class="notice-img" src="../assets/navbar_logos/notice.png" alt="ny notis"/>
                      <p class="notice-desc">Du har fått en ny köpförfrågan. Gå till <u>Min sida</u> för att godkänna eller ej.</p>
                    </div>
                  </a>
                  <!-- <a href="#">
                    <div>
                      <img class="notice-img" src="../assets/navbar_logos/notice.png" alt="ny notis"/>
                      <p class="notice-desc">Notiser</p>
                    </div>
                  </a> -->
                </div>
                <div id="previous-notice-list">
                  <a href="#">
                    <div>
                      <p class="notice-title">Tidigare</p>
                      <p class="notice-desc"><u>Språkcaféet</u> har taggat dig i ett nytt <u>event</u>.</p>
                    </div>
                  </a>
                </div>
              </div>
            </div>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Notiser </span>
            </a>
          </div>
          <div class="navlogo">
            <a href="#">
              <figure class="logo-click">
                  <img src="../assets/navbar_logos/chat.png" alt="shop knapp"/>
                  <figcaption class="l-text"> Meddelanden </figcaption>
              </figure>
            </a>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Meddelanden </span>
            </a>
          </div>
          <div class="navlogo">
            <a href="#">
              <figure class="logo-click">
                  <img src="../assets/navbar_logos/cart.png" alt="shop knapp"/>
                  <figcaption class="l-text"> Varukorg </figcaption>
              </figure>
            </a>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Varukorg </span>
            </a>
          </div>
          <div @mouseover="displayDropdown" class="navlogo">
              <div id="profile-dropdown" class="dropdown">
                <a href="http://localhost:8080/profile">
                  <figure id="profile-logo" @mouseover="highlightLogo" class="logo-click">
                    <img src="../assets/navbar_logos/profile.png" alt="shop knapp"/>
                    <figcaption class="l-text"> Min sida </figcaption>
                  </figure>
                </a>
                <div id="profile-content" @mouseover="highlightLogo" class="dropdown-content">
                  <a href="http://localhost:8080/profile/#profile">Min profil </a>
                  <a href="http://localhost:8080/profile/#purchases">Mina köp </a>
                  <a href="http://localhost:8080/profile/#products">Mina artiklar </a>
                  <a href="http://localhost:8080/profile/#statistics">Min statistik </a>
                  <a href="http://localhost:8080/profile/#requests">Mina köpförfrågningar </a>
                  <a href="http://localhost:8080/profile/#settings">Inställningar </a>
                </div>
              </div>
              <a href="#" v-if="this.isActive">
                <span class="mob-cap"> Min sida </span>
              </a>
          </div>
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
      profileDrop.style.display = 'none'
      const profileLogo = document.getElementById('profile-logo')
      profileLogo.classList.remove('active-dropdown')

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
  font-size: 10px;
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

.dropdown-content a {
  color:black;
  text-decoration: none;
  font-family: Roboto;
  font-weight: 300;
  font-size: 10px;
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
  font-size: 14px;
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
