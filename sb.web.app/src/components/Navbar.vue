<template>
  <div id="header-box" class="header-container">
    <header>
      <nav>
        <div class="left-logos" v-if="this.desc">
            <div class="navlogo">
              <figure class="logo-click">
                <a href="#">
                  <img src="../assets/navbar_logos/shop.png" alt="shop knapp"/>
                  <figcaption class="l-text">Shop</figcaption>
                </a>
              </figure>
              <a href="#" v-if="this.isActive">
                <span class="mob-cap"> Shop </span>
              </a>
            </div>
          <div class="navlogo">
            <figure class="logo-click">
              <a href="#">
                <img src="../assets/navbar_logos/events.png" alt="shop knapp" id="event-logo"/>
                <figcaption class="l-text"> Events </figcaption>
              </a>
            </figure>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Events </span>
            </a>
          </div>
          <div class="navlogo">
            <figure class="logo-click">
              <a href="#">
                <img src="../assets/navbar_logos/add.png" alt="shop knapp"/>
                <figcaption class="l-text"> Lägg upp </figcaption>
              </a>
            </figure>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Lägg upp </span>
            </a>
          </div>
          <div class="navlogo">
            <figure class="logo-click">
              <a href="#">
                <img src="../assets/navbar_logos/members.png" alt="shop knapp"/>
                <figcaption class="l-text"> Medlemmar </figcaption>
              </a>
            </figure>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Medlemmar </span>
            </a>
          </div>
        </div>
        <div class="middle-logo">
          <div class="navlogo">
            <figure>
              <a href="#">
                <img src="../assets/navbar_logos/sb.png" alt="shop knapp"/>
              </a>
            </figure>
          </div>
        </div>
        <div class="right-logos" v-if="this.desc">
          <div class="navlogo">
            <div id="click-dropdown" class="dropdown">
              <figure id="bell-logo" class="logo-click">
                <a href="#">
                  <img id="notice" src="../assets/navbar_logos/notice.png"/>
                  <img id="bell" src="../assets/navbar_logos/bell.png" alt="shop knapp"/>
                  <figcaption class="l-text"> Notiser </figcaption>
                </a>
              </figure>
              <div id="bell-dropdown" class="dropdown-content">
                <div id="new-notice-list">
                  <a href="#">
                    <div>
                      <p class="notice-title">Nya</p>
                      <img class="notice-img" src="../assets/navbar_logos/notice.png" alt="ny notis"/>
                      <p class="notice-desc">Du har fått en ny köpförfrågan. Gå till <u>Min sida</u> för att godkänna eller ej.</p>
                    </div>
                  </a>
                  <a href="#">
                    <div>
                      <img class="notice-img" src="../assets/navbar_logos/notice.png" alt="ny notis"/>
                      <p class="notice-desc">Notiser</p>
                    </div>
                  </a>
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
            <figure class="logo-click">
              <a href="#">
                <img src="../assets/navbar_logos/chat.png" alt="shop knapp"/>
                <figcaption class="l-text"> Meddelanden </figcaption>
              </a>
            </figure>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Meddelanden </span>
            </a>
          </div>
          <div class="navlogo">
            <figure class="logo-click">
              <a href="#">
                <img src="../assets/navbar_logos/cart.png" alt="shop knapp"/>
                <figcaption class="l-text"> Varukorg </figcaption>
              </a>
            </figure>
            <a href="#" v-if="this.isActive">
              <span class="mob-cap"> Varukorg </span>
            </a>
          </div>
          <div class="navlogo">
              <div class="dropdown">
                  <figure class="logo-click">
                    <a href="#">
                      <img src="../assets/navbar_logos/profile.png" alt="shop knapp"/>
                      <figcaption class="l-text"> Min sida </figcaption>
                    </a>
                  </figure>
                <div class="dropdown-content">
                  <a href="#">Min profil </a>
                  <a href="#">Mina artiklar </a>
                  <a href="#">Min statistik </a>
                  <a href="#">Mina köpförfrågningar </a>
                  <a href="#">Inställningar </a>
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
</template>

<script>

export default {
  data () {
    return {
      desc: true,
      isActive: false
    }
  },
  name: 'Navbar',
  props: ['screenWidth'],
  watch: {
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
  },
  methods: {
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
    resizeNav () {
      if (this.isActive) {
        const box = document.getElementById('header-box')
        const height = window.innerHeight

        if (height < 730) {
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

.header-container {
  width: 100%;
  height: 80px;
  top: 0px;
  z-index: 3;
  position: sticky;
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
  margin-bottom: 7px;
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

#bell-dropdown {
  margin-top: 2px;
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

.logo-click:hover {
    border-bottom: 2px solid black;
    transform: scale(1.05);
}

.icon {
    display: none;
}

@media (min-width: 1100px) {
  .dropdown:hover .dropdown-content {
    display: block;
  }
}

@media (max-width: 860px) {

 .header-container {
    -ms-overflow-style: none;  /* IE and Edge */
    scrollbar-width: none;  /* Firefox */
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
    transform: scale(2.0);
    margin-right: 10px;
    margin-top: 20px;
    display: block;
    border: none;
    background: #fff;
  }

  nav a:hover {
    color: grey;
  }
}

</style>
