<template>
    <div id="click-dropdown" class="dropdown">
        <a href="#">
        <figure id="bell-logo" :class="[`logo-click`,`notice`]" @click="setNotificationsToSeen">
            <img id="notice" class="notice" src="../../assets/navbar_logos/notice.png" v-if="this.$store.state.newNotifications.length > 0"/>
            <img id="bell" class="notice" src="../../assets/navbar_logos/bell.png" :alt="$t('shop.go_to_shop')"/>
            <figcaption :class=" [`l-text`,`notice`]"> {{ $t('notifications.notifications_header_label') }} </figcaption>
        </figure>
        </a>
        <div id="bell-dropdown" class="dropdown-content">
            <div id="new-notice-list" v-if="this.$store.state.newNotifications.length > 0">
                <p class="notice-title">{{ $t('new') }}</p>
                <div v-for="item in this.$store.state.newNotifications" :key="item">
                    <div v-if="item.type == 'saleRequest'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" @click.prevent="">
                        <div id="new-list-content">
                        <img class="notice-img" src="../../assets/navbar_logos/notice.png" alt="{{ $t('notifications.new_notification') }}"/>
                        <p class="notice-desc">{{ $t('notifications.purchase_request') }} {{ item.fromUser }}{{ $t('notifications.go_to') }} <u> {{ $t('nav.my_account') }} </u>{{ $t('notifications.approve_or_decline') }}.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'sendRequest'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" @click.prevent="">
                        <div id="new-list-content">
                        <img class="notice-img" src="../../assets/navbar_logos/notice.png" :alt="$t('notifications.new_notification')"/>
                        <p class="notice-desc">{{ item.fromUser }} {{ $t ('notifications.have_sent') }}{{ item.amount }} {{ $t('org.token') }}{{ $t ('notifications.go_to') }} <u> {{ $t('nav.my_account') }} </u>{{ $t ('notifications.approve_or_decline') }}.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'saleRequestAccepted'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" @click.prevent="">
                        <div id="new-list-content">
                        <img class="notice-img" src="../../assets/navbar_logos/notice.png" alt="{{ $t('notifications.new_notification') }}"/>
                        <p class="notice-desc">{{ item.fromUser }} {{ $t('notifications.approved_purchase')}} {{ $t('notifications.go_to') }} <u> {{ $t('nav.my_account') }} </u> {{ $t('notifications.download_invoice') }}</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'saleRequestDenied'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" @click.prevent="">
                        <div id="new-list-content">
                        <img class="notice-img" src="../../assets/navbar_logos/notice.png" alt="{{ $t('notifications.new_notification') }}"/>
                        <p class="notice-desc">{{ item.fromUser }} {{ $t('notifications.denied_purchase') }}</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'chatMessage'">
                    <router-link :to="{name:'Chat', params:{chatID: item.chatID}}" @click.prevent="">
                        <div id="new-list-content">
                        <img class="notice-img" src="../../assets/navbar_logos/notice.png" alt="{{ $t('notifications.new_notification') }}"/>
                        <p class="notice-desc">{{ item.fromUser }} {{ $t('notifications.have_sent') }} {{ $t('notifications.chat_message') }}.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                </div>
            </div>
            <div id="previous-notice-list" v-if="this.$store.state.oldNotifications.length > 0">
                <p class="notice-title">{{ $t('notifications.older') }}</p>
                <div v-for="item in this.$store.state.oldNotifications" :key="item">
                    <div v-if="item.type == 'saleRequest'">
                    <router-link :to="{name:'Profile', params:{tab: 'requests'}}" >
                        <div id="new-list-content">
                        <p class="notice-desc">{{ $t('notifications.purchase_request') }} {{ item.fromUser }}{{ $t('notifications.go_to') }} <u> {{ $t('nav.my_account') }} </u>{{ $t('notifications.approve_or_decline') }}.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'sendRequest'">
                    <router-link :to="{name:'Profile', params:{tab: 'requests'}}" @click.prevent="">
                        <div id="new-list-content">
                        <p class="notice-desc">{{ item.fromUser }} {{ $t('notifications.have_sent') }}{{ item.amount }} {{ $t('org.token') }}{{ $t('notifications.go_to') }} <u> {{ $t('nav.my_account') }} </u>{{ $t('notifications.approve_or_decline') }}.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'saleRequestAccepted'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" >
                        <div id="new-list-content">
                        <p class="notice-desc">{{ item.fromUser }} {{$t('notifications.approved_purchase')}}{{ $t('notifications.go_to') }} <u> {{ $t('nav.my_account') }} </u> {{ $t('notifications.download_invoice') }}</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'saleRequestDenied'">
                    <router-link :to="{name:'Profile', params:{tab: 'purchases'}}" >
                        <div id="new-list-content">
                        <p class="notice-desc">{{ item.fromUser }} {{ $t('notifications.denied_purchase') }}</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                    <div v-if="item.type == 'chatMessage'">
                    <router-link :to="{name:'Chat', params:{chatID: item.chatID}}" >
                        <div id="new-list-content">
                        <p class="notice-desc">{{ item.fromUser }} {{ $t('notifications.have_sent') }} {{ $t('notifications.chat_message') }}.</p>
                        <p class="notice-date"> {{ item.date.split('T')[0] }}</p>
                        </div>
                    </router-link> 
                    </div>
                </div>
            </div>
            <div id="previous-notice-list" v-if="this.$store.state.oldNotifications.length === 0 && this.$store.state.newNotifications.length === 0">
                <p class="notice-title">{{ $t('notifications.no_notifications') }}</p>
            </div> 
        </div>
    </div>
</template>

<script>

</script>

<style scoped>










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
