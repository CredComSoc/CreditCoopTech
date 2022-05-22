<template>
  <div>
    <div className="flexbox-container2 flexbox-item">
      
      <div className="image container-item">
        <img id="profile-img" :src="this.logoURL" alt="Profile Logo" style="object-fit:contain;max-width:240px;max-height:240px;">
        <button v-if="show_optional && profileData.name !== currentUser" id="chat-btn" @click="goToChat" > Starta chatt </button>
      </div>
      <div className="right container-item">
        <h1> Företagsnamn </h1>
        <p> {{profileData.name}} </p>

        <h1> Beskrivning </h1>
        <p> {{profileData.description}} </p>

        <h1> Adress </h1>
        <p> {{profileData.adress}} </p>

        <h1> Stad/ort </h1>
        <p> {{profileData.city}} </p>

        <h1> Faktureringsuppgifter </h1>
        <p> {{profileData.billingName}}<br/>{{profileData.billingBox}}<br/>{{profileData.billingAdress}}<br/> {{profileData.orgNumber}} </p>
      </div>
      <div className="right container-item">
        <div>
          <h1> Kontaktuppgifter </h1>
          <p :key="profileData"> {{"Email: " + profileData.email}}<br/><br/> {{"Tel: " + profileData.phone}} </p>
        </div> 
      </div>
    </div>
    <div class="sendmoney-box" v-if="show_optional && profileData.name !== currentUser">
      
      <form @submit.prevent="sendBkr" v-on:keyup.enter="sendBkr">
        <h1 class="box-text">Skicka Barterkronor</h1>
        <div>
          <label class="box-label">Antal</label>
          <input class="box-input" type="text" v-model="bkr" name="" placeholder="Antal" id="bkr-input" pattern="\d*" required>
        </div>
        <div>
          <label class="box-label">Kommentar</label>
          <textarea class="box-textarea" type="password" v-model="comment" name="" placeholder="Text" id="comment-input"> </textarea>
        </div>
        <button id="login-button">Skicka</button>
      </form>
      <div class="box-error" v-if="error">
        Fel epost eller lösenord ({{ loginCount }})
      </div>
    </div>
    <PopupCard v-if="this.bkrSentMsg" @closePopup="this.closePopup" title="Förfrågan skickad" btnLink="" btnText="Ok" :cardText="`Din förfrågan att överföra ` + this.bkr + ` barterkronor till ` + profileData.name + ' har mottagits.'" />
    <PopupCard v-if="this.notEnoughBkrMsg" @closePopup="this.closePopup" title="Överföringen kunde inte genomföras" btnText="Ok" :cardText="`Du har inte tillräckligt med barterkronor för att genomföra överföringen.`" />
    <PopupCard v-if="this.tooMuchBkrMsg" @closePopup="this.closePopup" title="Överföringen kunde inte genomföras" btnText="Ok" :cardText="profileData.name + ` kan inte ta emot ` + this.bkr + ' bkr.'" />
    <PopupCard v-if="this.chatError" title="Anslutningsproblem" cardText="Något gick fel vid anslutning till chatt med denna användare. Försök igen senare." btnLink="#" btnText="Ok" />
  </div>
</template>

<script>
import { EXPRESS_URL, getMember, profile, getAvailableBalance, sendMoney, postNotification, getUserAvailableBalance, getUserLimits } from './../../serverFetch'
import PopupCard from '../CreateArticle/PopupCard.vue'

export default {
  components: {
    PopupCard
  },
  data () {
    return {
      logoURL: '',
      profileData: [],
      getMember,
      currentUser: '',
      bkr: 0,
      comment: '',
      bkrSentMsg: false,
      notEnoughBkrMsg: false,
      tooMuchBkrMsg: false,
      chatError: false,
      show_optional: false
    }
  },
  methods: {
    getProfile (member) {
      this.getMember(member).then(res => {
        this.profileData = res
        this.getImgURL()
      })
    },
    getImgURL () {
      this.logoURL = EXPRESS_URL + '/image/' + this.profileData.logo
    },
    async sendBkr () {
      if (this.bkr !== 0) {
        const saldo = await getAvailableBalance()
        if (saldo < this.bkr) {
          this.notEnoughBkrMsg = true
        } else {
          const userSaldo = await getUserAvailableBalance(this.profileData.name)
          const userLimits = await getUserLimits(this.profileData.name)
          if (userSaldo + userLimits.min + Number(this.bkr) > userLimits.max) {
            this.tooMuchBkrMsg = true
          } else {
            await sendMoney(this.bkr, this.comment, this.profileData.name)
            postNotification('sendRequest', this.profileData.name, this.bkr)
            this.bkrSentMsg = true
          }
        }
      }
    },
    closePopup () {
      this.bkrSentMsh = false
      this.notEnoughBkrMsg = false
      this.tooMuchBkrMsg = false
      this.bkr = 0
      this.comment = ''
    },
    goToChat () {
      fetch(EXPRESS_URL + '/chat/' + this.profileData.name, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include'
      }).then(res => res.json())
        .then(data => {
          if (data !== false) {
            this.$router.push({ name: 'Chat', params: { chatID: data } })
          } else {
            console.log('chat error!!')
            this.chatError = true
          }
        }).catch(err => console.log(err))
    }
  },
  created: function () {
    this.getProfile(this.$route.params.userprofile)
    profile().then((res) => {
      this.currentUser = res.name
      if (this.profileData.name !== this.currentUser) {
        this.show_optional = true
      }
    })
  }
}

</script>

<style scoped>

.container-item {
  flex: 2;
  padding-left: 30px;
  padding-right: 30px;
}

img {
  display: block;
  margin-left: auto;
  margin-right: auto;
  width: 100%;
}

.right {
  display: flex;
  flex-direction: column;
  justify-content: space-between;
}

.flexbox-item {
  margin: 10px;
  max-width: 1100px;
  width: 100%;
  margin: 0 auto;
}

h1 {
  font-size: 2rem;
}

.image {
  /**margin-left: auto;
  margin-right: auto;*/
  width: 50%;
}

#chat-btn {
  font-size: 17px;
  line-height: 23px;
  letter-spacing: 0.06em;
  color: #FFF;
  border: none;
  width: 150px;
  height:50px;
  border-radius: 10px;
  background: #4690CD;
  border: 1px solid #4690CD;
  margin-left: 30%;
  margin-bottom: 50px;
  display: block;
  margin-left: auto;
  margin-right: auto;
  margin-top: 20px;
}

#profile-img {
  text-align: center;
}

@media screen and (min-width: 860px) {
  .flexbox-container2 {
    padding-top: 50px;
    display: flex;
  }
  .image {
    width: 50%;
  }
}

.sendmoney-box {
    font-family: Ubuntu;
    font-style: Regular;
    font-size:  20px;
    text-align: left;
    letter-spacing: 0.05em;
    padding-left: 19px;
    padding-right: 19px;
    width: 345px;
    height: 570px;
    border-radius: 20px;
    margin: auto;
    position: relative;
}

button {
  margin-right: 10px;
  border-radius: 5px;
  font-size: 1.2rem;
  padding: 2px 12px 2px 12px;
  background-color: #F3F3F3;
}

.box-text {
  padding-top: 20px;
  padding-bottom: 18px;
}

.box-label {
  padding-bottom: 8px;
}

.box-input {
  border: 0px;
  width: 340px;
  height: 34px;
  background-color: #F3F3F3;
  margin-left: 20px;
  margin-bottom: 16px;
  padding-left: 8px;
}

.box-textarea{
  border: 0px;
  width: 340px;
  height: 102px;
  background-color: #F3F3F3;
  margin-left: 20px;
  margin-bottom: 36px;
  padding-left: 8px;
}

input:focus,
select:focus,
textarea:focus,
button:focus {
    outline: none;
}

@media screen and (max-width: 860px) {
  .flexbox-container2 {
    display: flex;
    margin-top: 30px;
    flex-direction: column;
  }
  .right, .container-item {
  }
  .image {
    width: 50%;
  }
  .sendmoney-box {
    font-size: 14px;
    height: fit-content;
  }
}

</style>
