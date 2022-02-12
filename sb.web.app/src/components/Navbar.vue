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
          <div class="dropdown">
            <figure class="logo-click">
              <a href="#">
                <img id="notice" src="../assets/navbar_logos/notice.png"/>
                <img id="bell" src="../assets/navbar_logos/bell.png" alt="shop knapp"/>
                <figcaption class="l-text"> Notiser </figcaption>
              </a>
            </figure>
            <div class="dropdown-content">
                  <a href="#">
                    <div>
                      <h4>Nya</h4>
                      <p>Du har fått en ny köpförfrågan. Gå till Mina sidor för att godkänna eller ej</p>
                    </div>
                  </a>
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
  margin-left: 50px;
  margin-right: 50px;
  margin-bottom: 7px;
}

#notice {
  position: absolute;
  height: 11px;
  width: 11px;
  margin-top: 4px;
}

#profile:hover {
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
  float: right;
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

</style>
