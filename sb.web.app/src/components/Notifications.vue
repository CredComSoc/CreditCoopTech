<template>
    <div id="click-dropdown" class="dropdown">
        <a href="#">
        <figure id="bell-logo" :class="[`logo-click`,`notice`]" @click="setNotificationsToSeen">
            <img id="notice" class="notice" src="../assets/navbar_logos/notice.png" v-if="newNotifications.length > 0"/>
            <img id="bell" class="notice" src="../assets/navbar_logos/bell.png" alt="shop knapp"/>
            <figcaption :class=" [`l-text`,`notice`]"> Notiser </figcaption>
        </figure>
        </a>
        <div id="bell-dropdown" class="dropdown-content">
            <div id="new-notice-list" v-if="newNotifications.length > 0">
                <p class="notice-title">Nya</p>
                <div v-for="item in newNotifications" :key="item">
                    <div v-if="item.type == 'saleRequest'">
                    <router-link :to="{name:'Profile', params:{tab: 'requests'}}" @click.prevent="moveNotification(item)">
                        <div id="new-list-content">
                        <img class="notice-img" src="../assets/navbar_logos/notice.png" alt="ny notis"/>
                        <p class="notice-desc">Du har fått en köpförfrågan från {{ item.fromUser }}. Gå till <u>Min sida</u> för att godkänna eller ej.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'sendRequest'">
                    <router-link :to="{name:'Profile', params:{tab: 'requests'}}" @click.prevent="moveNotification(item)">
                        <div id="new-list-content">
                        <img class="notice-img" src="../assets/navbar_logos/notice.png" alt="ny notis"/>
                        <p class="notice-desc">{{ item.fromUser }} har skickat {{ item.amount }} barterkronor. Gå till <u>Min sida</u> för att godkänna eller ej.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'saleRequestAccepted'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" @click.prevent="moveNotification(item)">
                        <div id="new-list-content">
                        <img class="notice-img" src="../assets/navbar_logos/notice.png" alt="ny notis"/>
                        <p class="notice-desc">{{ item.fromUser }} har godkänt din köpförfrågan. Gå till <u>Min sida</u> för att ladda ner fakturan.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'saleRequestDenied'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" @click.prevent="moveNotification(item)">
                        <div id="new-list-content">
                        <img class="notice-img" src="../assets/navbar_logos/notice.png" alt="ny notis"/>
                        <p class="notice-desc">{{ item.fromUser }} har nekat din köpförfrågan.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'chatMessage'">
                    <router-link :to="{name:'Chat', params:{chatID: item.chatID}}" @click.prevent="moveNotification(item)">
                        <div id="new-list-content">
                        <img class="notice-img" src="../assets/navbar_logos/notice.png" alt="ny notis"/>
                        <p class="notice-desc">{{ item.fromUser }} har skickat ett chatt-meddelande.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                </div>
            </div>
            <div id="previous-notice-list" v-if="oldNotifications.length > 0">
                <p class="notice-title">Tidigare</p>
                <div v-for="item in oldNotifications" :key="item">
                    <div v-if="item.type == 'saleRequest'">
                    <router-link :to="{name:'Profile', params:{tab: 'requests'}}" >
                        <div id="new-list-content">
                        <p class="notice-desc">Du har fått en köpförfrågan från {{ item.fromUser }}. Gå till <u>Min sida</u> för att godkänna eller ej.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'saleRequestAccepted'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" >
                        <div id="new-list-content">
                        <p class="notice-desc">{{ item.fromUser }} har godkännt din köpförfrågan. Gå till <u>Min sida</u> för att ladda ner fakturan.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'saleRequestDenied'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" >
                        <div id="new-list-content">
                        <p class="notice-desc">{{ item.fromUser }} har nekat din köpförfrågan.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'chatMessage'">
                    <router-link :to="{name:'Chat', params:{chatID: item.chatID}}" >
                        <div id="new-list-content">
                        <p class="notice-desc">{{ item.fromUser }} har skickat ett chatt-meddelande.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                </div>
            </div>
            <div id="previous-notice-list" v-if="oldNotifications.length === 0 && newNotifications.length === 0">
                <p class="notice-title">Inga notiser</p>
            </div> 
        </div>
    </div>
</template>

<script>
import { getNotifications, setNotificationsToSeen } from '../serverFetch.js'

export default {
  data () {
    return {
      oldNotifications: [],
      newNotifications: []
    }
  },
  mounted () {
    getNotifications().then((res) => {
      res.forEach(notification => {
        if (notification.seen) {
          this.oldNotifications.push(notification)
        } else {
          this.newNotifications.push(notification)
        }
      })
    })

    setInterval(() => getNotifications().then((res) => {
      if (res) {
        this.oldNotifications = []
        this.newNotifications = []
        res.forEach(notification => {
          if (notification.seen) {
            this.oldNotifications.push(notification)
          } else {
            this.newNotifications.push(notification)
          }
        })
      }
    }), 10000)
  },
  methods: {
    moveNotification (notification) {
      this.newNotifications.splice(this.newNotifications.indexOf(notification), 1)
      this.oldNotifications.unshift(notification)
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

.notice-date {
  font-size: 7px;

  color: blue;
}

.notice-title {
  font-weight: 500;
  font-size: 16px;
  margin-bottom: 5px;
  text-align: center;
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

@media (min-width: 1025px) {
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
