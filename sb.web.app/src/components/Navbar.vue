<template>
  <header>
    <nav>
      <div class="left-logos">
          <div class="navlogo">
            <figure class="logo-click">
              <a href="#">
                <img src="../assets/navbar_logos/shop.png" alt="shop knapp"/>
                <figcaption class="l-text">Shop</figcaption>
              </a>
            </figure>
          </div>
        <div class="navlogo">
          <figure class="logo-click">
            <a href="#">
              <img src="../assets/navbar_logos/events.png" alt="shop knapp" id="event-logo"/>
              <figcaption class="l-text"> Events </figcaption>
            </a>
          </figure>
        </div>
        <div class="navlogo">
          <figure class="logo-click">
            <a href="#">
              <img src="../assets/navbar_logos/add.png" alt="shop knapp"/>
              <figcaption class="l-text"> Lägg upp </figcaption>
            </a>
          </figure>
        </div>
        <div class="navlogo">
          <figure class="logo-click">
            <a href="#">
              <img src="../assets/navbar_logos/members.png" alt="shop knapp"/>
              <figcaption class="l-text"> Medlemmar </figcaption>
            </a>
          </figure>
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
      <div class="right-logos">
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
                    <p class="notice-desc">Hello</p>
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
        </div>
        <div class="navlogo">
          <figure class="logo-click">
            <a href="#">
              <img src="../assets/navbar_logos/chat.png" alt="shop knapp"/>
              <figcaption class="l-text"> Meddelanden </figcaption>
            </a>
          </figure>
        </div>
        <div class="navlogo">
          <figure class="logo-click">
            <a href="#">
              <img src="../assets/navbar_logos/cart.png" alt="shop knapp"/>
              <figcaption class="l-text"> Varukorg </figcaption>
            </a>
          </figure>
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
        </div>
      </div>
    </nav>
  </header>
</template>
<script>
export default {
  name: 'Navbar'
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
/* Add a black background color to the top navigation */

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

 /* Style the links inside the navigation bar  */

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

.logo-click:hover {
  border-bottom: 2px solid black;
  transform: scale(1.05);
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
  margin-top: 3px;
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

.dropdown-content a:hover {
  display: block;
  background-color: #E5F0FD;
}

.dropdown:hover .dropdown-content {
  display: block;
}

#click-

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

</style>
