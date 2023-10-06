<template>
  <div>
    <div className="flexbox-container2 flexbox-item">
      
      <div className="image container-item">
        <img id="profile-img" v-if="profileData.logo !== ''" :src="this.logoURL" alt="Profile Logo" style="object-fit:contain;max-width:240px;max-height:240px;">
        <img id="profile-img" v-if="profileData.logo === ''" src="../../assets/list_images/user.png" alt="Profile Logo2" style="object-fit:contain;max-width:240px;max-height:240px;">
        <h5 >{{ $t('user.last_online')}}:</h5>
        <h5 >{{ getOnlineStatus() }}</h5>
        <button v-if="show_optional" id="chat-btn" @click="goToChat" > {{ $t('chat.start') }} </button>
        
      </div>
      <div className="right container-item">
        <h1> {{ $t('user.business') }} </h1>
        <p> {{profileData.accountName}} </p>

        <h1> {{ $t('user.description') }} </h1>
        <p> {{profileData.description}} </p>

        <h1> {{ $t('user.street_address') }} </h1>
        <p> {{profileData.address}} </p>

        <h1> {{ $t('user.town') }} </h1>
        <p> {{profileData.city}} </p>

        <h1> {{ $t('user.billing') }} </h1>
        <p> {{profileData.billingName}}<br/>{{profileData.billingBox}}<br/>{{profileData.billingAddress}}<br/> {{profileData.orgNumber}} </p>
      </div>
      <div className="right container-item">
        <div>
          <h1> {{ $t('user.contact') }} </h1>
          <p :key="profileData"> {{"Email: " + profileData.email}}<br/><br/> {{"Tel: " + profileData.phone}} </p>
        </div> 
      </div>
    </div>
    <div class="sendmoney-box" v-if="show_optional">
      
      <form @submit.prevent="sendBkr" v-on:keyup.enter="sendBkr">
        <h1 class="box-text">{{ $t('user.send_token', {token: $t('org.token')} ) }}</h1>
        <div>
          <label class="box-label">{{ $t('quantity') }}</label>
          <TextBox class="box-input" placeholder="0" ref="tknInput" id="tkn-input" pattern="\d*" disabled="true" required/>
        </div>
        <div>
          <label class="box-label">{{ $t('user.commentLabel') }}</label>
          <TextArea class="box-textarea" ref="commentInput" length="200" placeholder="Text" />
        </div>
        <button id="send-btn">{{ $t('send') }}</button>
      </form>
    </div>

    <!-- members wants and offers -->

    <div class="listings">
        <div v-if="this.items.offers.length !== 0">
          <h3 >{{ $t('Offers') }}</h3>
          <Alllistings @togglePopupEvent="openPopUp" :key=this.items.offers :search-data=this.items.offers />
        </div>
        <div v-if="this.items.wants.length !== 0">
          <h3>{{ $t('Wants') }}</h3>
          <Alllistings @togglePopupEvent="openPopUp" :key=this.items.wants :search-data=this.items.wants />
        </div>
        <h3 v-if="this.items.offers.length === 0 && this.items.wants.length === 0">{{ $t('shop.no_product_found', {searchWord: this.username}) }}</h3>
        <ListingPopup @closePopup="closePopup" @placeInCart="this.placeInCart" v-if="popupActive" :key="popupActive" :listing-obj=listingObjPopup :username="this.username" />
      </div>


    <PopupCard v-if="this.isBalanceSent" @closePopup="this.closePopup" :title="$t('user.sentMessagePopupTitle')" btnLink="" btnText="Ok" :cardText="$t('user.tknSentMessageCardText', {'username': profileData.accountName})" />
    <PopupCard v-if="this.notEnoughBkrMsg" @closePopup="this.closePopup" :title="$t('cart.insufficient_credit')" btnText="Ok" :cardText="$t('user.insufficient_sender_balance', {amount: this.tkn, 'credit_unit': this.$t('org.token'), 'credit': this.available_credit})" />
    <PopupCard v-if="this.tooMuchBkrMsg" @closePopup="this.closePopup" :title="$t('user.recipient_balance_too_high')" btnText="Ok" :cardText="$t('user.recipient_balance_too_high_body', {username: profileData.accountName})" />
    <PopupCard v-if="this.chatError" :title="$t('user.connectionProblemsCardText')" cardText="Något gick fel vid anslutning till chatt med denna {{ $t('user.member_label') }}. Försök igen senare." btnLink="#" btnText="Ok" />
    <PopupCard v-if="this.invalidNumberOfBkr" :title="$t('user.failed_transaction_invalid_numberMessagePopupTitle')" btnLink="#" btnText="Ok" :cardText="$t('user.tknFailedTransactionInvalidNumberCardText', {tkn: this.tkn, accountName: profileData.accountName})"  />
    <PopupCard v-if="this.pendingBalanceLimitExceeded" :title="$t('cart.insufficient_credit')" btnLink="" btnText="Ok" :cardText="$t('cart.pending_transaction_limit_exceeded', {'total_price': this.tkn, 'credit_unit': this.$t('org.token'), 'available_credit': this.actualAvailableCreditWithPending})" />
    <PopupCard v-if="this.pendingSellerBalanceLimitExceeded" :title="$t('shop.seller_balance_too_high')" btnLink="" btnText="Ok" :cardText="$t('user.receiver_pending_balance_exceeded', {'seller': profileData.accountName})" />

  </div>
  <LoadingComponent ref="loadingComponent" />
</template>

<script>
import { EXPRESS_URL, getAvailableBalance, sendMoney, postNotification, getUserAvailableBalance, getUserLimits, setArticles, setCartData } from './../../serverFetch'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'
import TextBox from '@/components/SharedComponents/TextBox.vue'
import TextArea from '@/components/SharedComponents/TextArea.vue'
import Alllistings from '@/components/Shop/all_listings.vue'
import ListingPopup from '@/components/SharedComponents/ListingPopup.vue'
import LoadingComponent from '../SharedComponents/LoadingComponent.vue'

export default {
  components: {
    PopupCard,
    TextBox,
    TextArea,
    Alllistings,
    ListingPopup,
    LoadingComponent
  },
  data () {
    return {
      logoURL: '',
      profileData: [],
      tkn: 0,
      comment: '',
      isBalanceSent: false,
      notEnoughBkrMsg: false,
      tooMuchBkrMsg: false,
      chatError: false,
      show_optional: false,
      invalidNumberOfBkr: false,
      items: {
        wants: [],
        offers: []
      },
      popupActive: false,
      listingObjPopup: Object,
      username: '',
      putInCart: false,
      pendingBalanceLimitExceeded: false,
      actualAvailableCreditWithPending: 0,
      pendingSellerBalanceLimitExceeded: false,
      available_credit: 0

    }
  },
  methods: {
    getImgURL () {
      if (this.profileData.logo !== '') {
        this.logoURL = EXPRESS_URL + '/image/' + this.profileData.logo
      } else {
        this.logoURL = ''
      }
    },
    async sendBkr () {
      this.$refs.loadingComponent.showLoading()
      this.tkn = this.$refs.tknInput.getInput()
      this.comment = this.$refs.commentInput.getInput()
      if (this.tkn && Number.isInteger(Number(this.tkn)) && Number(this.tkn) > 0) {
        const balance = await getAvailableBalance()
        this.available_credit = balance.totalAvailableBalance
        if (balance.totalAvailableBalance < this.tkn) {
          this.$refs.loadingComponent.hideLoading()
          // balance too low
          this.notEnoughBkrMsg = true
        } else {
          // very tricky logic. More knowledge of /saldo endpoint to understand
          // min limit violation
          if ((-balance.pendingBalance) + Number(this.tkn) > (-this.$store.state.user.min_limit)) {
            this.$refs.loadingComponent.hideLoading()
            this.actualAvailableCreditWithPending = Math.abs(this.$store.state.user.min_limit - balance.pendingBalance)
            this.pendingBalanceLimitExceeded = true
          } else {
            const userSaldo = await getUserAvailableBalance(this.profileData.accountName)
            const userLimits = await getUserLimits(this.profileData.accountName)
            
            if (userSaldo.pendingBalance + Number(this.tkn) > userLimits.max) {
              this.$refs.loadingComponent.hideLoading()
              this.pendingSellerBalanceLimitExceeded = true
            } else if (userSaldo.totalAvailableBalance + userLimits.min + Number(this.tkn) > userLimits.max) {
              await postNotification('sendBalanceSellerBalanceTooHigh', this.profileData.accountName, Number(this.tkn))
              this.$refs.loadingComponent.hideLoading()
              // receiver balance too high
              this.tooMuchBkrMsg = true
            } else {
              await sendMoney(this.tkn, this.comment, this.profileData.accountName)
              await postNotification('sendRequest', this.profileData.accountName, this.tkn)
              // await setUserBalance()   set user balance call the api here
              this.$refs.loadingComponent.hideLoading()
              this.isBalanceSent = true
            }
          }
        }
      } else {
        this.$refs.loadingComponent.hideLoading()
        this.invalidNumberOfBkr = true
      }
    },
    openPopUp (listingObj) {
      this.popupActive = true
      console.log(listingObj)
      this.listingObjPopup = listingObj
    },
    closePopup () {
      this.tknSentMsh = false
      this.notEnoughBkrMsg = false
      this.tooMuchBkrMsg = false
      this.tkn = 0
      this.comment = ''
      this.popupActive = false
    },
    goToChat () {
      fetch(EXPRESS_URL + '/chat/' + this.profileData.accountName, {
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
            //console.log('chat error!!')
            this.chatError = true
          }
        }).catch(err => console.log(err))
    },
    getOnlineStatus () {
      if (this.profileData.last_online) {
        const lastOnline = new Date(this.profileData.last_online)
        if (Date.now() - lastOnline < 1000 * 60 * 3) { // 3 min
          return this.$i18n.t('time.now')
        } else if (Date.now() - lastOnline < 1000 * 60 * 60 * new Date().getHours() + 1) { // today
          return this.$i18n.t('time.today')
        } else if (Date.now() - lastOnline < 1000 * 60 * 60 * (new Date().getHours() + 25)) { // yday
          return this.$i18n.t('time.yesterday')
        } else {
          let days = ((Date.now() - lastOnline) / (1000 * 60 * 60 * 24)) + 1
          days = Math.floor(days * 10) / 10
          return days + ' ' + this.$i18n.t('time.days_ago')
        }
      } else {
        return this.$i18n.t('time.never')
      } 
    },
    placeInCart (amount, listingObj) {
      const JSONdata = new FormData()
      const cartItem = {
        title: listingObj.title,
        coverImg: listingObj.coverImg,
        price: listingObj.price,
        quantity: amount, // number of items
        article: listingObj.article, // product or service
        id: listingObj.id, // Id for the article
        status: listingObj.status, // is for sale
        userUploader: listingObj.userUploader, // user who uploaded the article, use to see if article is still for sale
        'end-date': listingObj['end-date'] // end date for the article
      }
      JSONdata.append('cartItem', JSON.stringify(cartItem))

      this.popupActive = false
      this.putInCart = true

      fetch(EXPRESS_URL + '/cart', { // POST endpoint
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include',
        body: JSON.stringify(cartItem) // This is your file object
      }).then(
        response => response,
        // TODO: get the cart data endpoint only and replace it with the whole data endpoint
        setTimeout(() => {
          setCartData()
        })
      ).then(
        success => {
          //console.log(success)
        } // Handle the success response object
      ).catch(
        error => console.log(error) // Handle the error response object
      )
    }
  },
  created: async function () {
    for (const member of this.$store.state.allMembers) {
      if (member.accountName === this.$route.params.userprofile) {
        this.profileData = member
      }
    }
    this.getImgURL()
    if (this.$route.params.userprofile !== this.$store.state.user.profile.accountName) {
      this.show_optional = true
    }
  },
  async mounted () {
    this.username = this.$route.params.userprofile
    await setArticles()
    this.items.offers = this.$store.state.allArticles.filter(article => (article.status === 'selling' || article.status === 'offer') && article.userUploader === this.$route.params.userprofile)
    this.items.wants = this.$store.state.allArticles.filter(article => (article.status === 'buying' || article.status === 'want') && article.userUploader === this.$route.params.userprofile)
    console.log(this.items)
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
  margin-bottom: 15px;
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
  text-align: center;
}

#chat-btn {
  width: 135px;
  height: 45px;
  margin-left: 30%;
  margin-bottom: 50px;
  display: block;
  margin-left: auto;
  margin-right: auto;
  margin-top: 20px;
}

#send-btn {
  width: 90px;
  height: 45px;  
}

#profile-img {
  text-align: center;
}
.listings {
  flex-basis: 100%;
  width: auto;
  margin-top: 1rem;
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
  margin-top:40px;
  margin-right: 10px;
  border-radius: 5px;
  font-size: 1.2rem;
  padding: 2px 12px 2px 12px;
  background: #4690CD;
  border: 1px solid #4690CD;
  color: #FFF;
  font-size: 17px;
  line-height: 23px;
  letter-spacing: 0.06em;
  border-radius: 10px;
}

button:hover {
  background: #457EAD; 
}

.box-text {
  padding-top: 20px;
}

.box-label {
  padding-top: 20px;
}

.box-input {
  margin-top: -30px;
  border: 0px;
  width: 340px;
  height: 34px;
  margin-bottom: 40px;
}

.box-textarea{
  margin-top: -30px;
  border: 0px;
  width: 340px;
  height: 102px;
  margin-bottom: 36px;
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
