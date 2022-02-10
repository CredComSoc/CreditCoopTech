<template>
  <div class="top-nav">
    <div class="left-logos">
      <div class="navlogo">
        <figure>
          <img src="../assets/navbar_logos/shop.png" alt="shop knapp"/>
          <figcaption class="l-text"> Shop </figcaption>
        </figure>
      </div>
      <div class="navlogo">
        <figure>
          <img src="../assets/navbar_logos/events.png" alt="shop knapp" id="event-logo"/>
          <figcaption class="l-text"> Events </figcaption>
        </figure>
      </div>
      <div class="navlogo">
        <figure>
          <img src="../assets/navbar_logos/add.png" alt="shop knapp"/>
          <figcaption class="l-text"> Lägg upp </figcaption>
        </figure>
      </div>
      <div class="navlogo">
        <figure>
          <img src="../assets/navbar_logos/members.png" alt="shop knapp"/>
          <figcaption class="l-text"> Medlemmar </figcaption>
        </figure>
      </div>
    </div>
    <div class="middle-logo">
      <div class="navlogo">
        <figure>
          <img src="../assets/navbar_logos/shop.png" alt="shop knapp"/>
          <figcaption class="l-text"> Shop </figcaption>
        </figure>
      </div>
    </div>
    <div class="right-logos">
      <div class="navlogo">
        <figure>
          <img src="../assets/navbar_logos/shop.png" alt="shop knapp"/>
          <figcaption class="l-text"> Shop </figcaption>
        </figure>
      </div>
    </div>
      <!-- <a href="#home">Shop</a>
      <a href="#news">Events</a>
      <a href="#contact">Lägg upp</a>
      <a href="#about">Medlemmar</a>
      <a href="#about">Notiser</a>
      <a href="#about">Meddelanden</a>
      <a href="#about">Varukorg</a>
      <a href="#about">Min Sida</a> -->
  </div>
</template>

<script>
export default {
  name: 'Navbar'
}
</script>

<!-- Add "scoped" attribute to limit CSS to this component only -->
<style scoped>
/* Add a black background color to the top navigation */
 .top-nav {
  background-color: white;
  overflow: hidden;
  display: flex;
  flex-flow: row wrap;
  justify-content: space-around;
}

 /* Style the links inside the navigation bar  */
.top-nav a {
  float: left;
  color: #f2f2f2;
  text-align: center;
  padding: 14px 16px;
  text-decoration: none;
  font-size: 17px;
}

.l-text {
  font-family: 'Roboto';
  font-weight: 300;
  font-style: normal;
  font-size: 10px;
  text-align: center;
}

.logo-text {
  display: flex;
  flex-flow: row wrap;
  justify-content: space-around;
  margin-bottom: 40px;
}

 /* Change the color of links on hover  */
.top-nav a:hover {
  background-color: #ddd;
  color: black;
}

.left-logos {
  display: flex;
  justify-content: space-around;
}

.right-logos {
  display: flex;
}

.navlogo{
  padding-left: 40px;
  background-color: blue;
}

img{
  padding-left:20%;
  padding-bottom:10%;
  margin-top:30%;
}
</style>
