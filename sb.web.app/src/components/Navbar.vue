<template>
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
      <a href="#" class="icon" @click="openNav">
        <i class="fa fa-bars"></i>
      </a>
    </nav>
  </header>
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

  mounted () {
    this.onResize()
    window.addEventListener('resize', this.onResize)
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
    },
    onResize () {
      if (window.innerWidth < 861 && !this.isActive) {
        this.desc = false
      } else {
        this.desc = true
        if (window.innerWidth > 861) {
          this.isActive = false
        }
      }
    }
  }
}
</script>

<style scoped>
* {
  margin-bottom: 0;
}

header nav {
  background-color: white;
  overflow:visible;
  display: flex;
  flex-direction: row;
  justify-content: space-evenly;
  margin-top: 20px;
  border-bottom: 1px solid black;
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

.left-logos {
  margin-left: 250px;
}

.right-logos {
  margin-right: 250px;
}

.middle-logo {
  margin-left: 60px;
  margin-right: 60px;
  margin-bottom: 7px;
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

  header nav {
    overflow: hidden;
    background-color: #fff;
    position: relative;
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
  }

  nav a:hover {
    color: grey;
  }
}

</style>
