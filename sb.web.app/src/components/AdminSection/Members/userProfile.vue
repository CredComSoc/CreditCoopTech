<template>
  <div>
    <div className="flexbox-container2 flexbox-item" v-if="!edit">
      <div className="image container-item">
        <img id="profile-img" v-if="profileData.logo" :src="this.logoURL" alt="$t('user.alt_profile_logo')"
          style="object-fit:contain;max-width:240px;max-height:240px;">
        <img id="profile-img" v-if="!profileData.logo" src="@/assets/list_images/user.png"
          alt="$t('user.alt_profile_logo')" style="object-fit:contain;max-width:240px;max-height:240px;">
        <h5>{{ $t('user.last_online')}}:</h5>
        <h5>{{ getOnlineStatus() }}</h5>
        <button v-if="show_optional" id="chat-btn" @click="goToChat"> {{ $t('chat.start') }} </button>
        <button @click="edit = !edit" id="edit-btn">
          <span v-html="$t('user.edit_user')"></span>
        </button>
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
        <p> {{profileData.billing.name}}<br />{{profileData.billing.box}}<br />{{profileData.billing.address}}<br />
          {{profileData.billing.orgNumber}} </p>
      </div>
      <div className="right container-item">
        <div>
          <h1> {{ $t('user.contact') }} </h1>
          <p :key="profileData"> {{"Email: " + profileData.email}}<br /><br /> {{"Tel: " + profileData.phone}} </p>

          <h1>{{ $t('user.active') }}</h1>
          <p><input type="checkbox" id="checkbox" v-model="profileData.is_active" disabled /></p>
        </div>
      </div>
    </div>
    <div class="sendmoney-box" v-if="show_optional && !edit">
      <form @submit.prevent="sendBkr" v-on:keyup.enter="sendBkr">
        <h1 class="box-text">{{ $t('send') }} {{ $t('org.token') }}</h1>
        <div>
          <label class="box-label">{{ $t('transfer_amount') }}</label>
          <TextBox class="box-input" placeholder="0" ref="tknInput" id="tkn-input" pattern="\d*" disabled="true"
            required />
        </div>
        <div>
          <label class="box-label">{{ $t('user.commentLabel') }}</label>
          <TextArea class="box-textarea" ref="commentInput" length="200" placeholder="Text" />
        </div>
        <button id="send-btn">{{ $t('send') }}</button>
      </form>
    </div>
    <PopupCard v-if="this.tknSentMsg" @closePopup="this.closePopup" :title="$('user.sentMessagePopupTitle')" btnLink=""
      btnText="Ok"
      :cardText="$t('user.tknSentMessageCardText', {amt: this.tkn, token: $t('org.token'), accountName: profileData.accountName})" />
    <PopupCard v-if="this.notEnoughBkrMsg" @closePopup="this.closePopup"
      :title="$('user.failed_transaction_underMessagePopupTitle')" btnText="Ok"
      :cardText="$t('user.tknFailedTransactionUnderCardText')" />
    <PopupCard v-if="this.tooMuchBkrMsg" @closePopup="this.closePopup"
      :title="$('user.failed_transaction_overMessagePopupTitle')" btnText="Ok"
      :cardText="$t('user.tknFailedTransactionOverCardText', {accountName: profileData.accountName})" />
    <PopupCard v-if="this.chatError" :title="$('user.failed_chat_PopupTitle')"
      :cardText="$t('user.failed_chat_CardText', {accountName: user.member_label})" btnLink="#" btnText="Ok" />
    <PopupCard v-if="this.invalidNumberOfBkr" :title="$('user.failed_transaction_invalid_numberMessagePopupTitle')"
      btnLink="#" btnText="Ok" :cardText="$t('user.tknFailedTransactionInvalidNumberCardText')" />
    <div v-if="edit">
      <form className="flexbox-container2" @submit.prevent="">
        <div className="container-item">
          <h1>{{ $t('user.general_information') }}</h1>
          <label for="logo">{{ $t('user.profile_picture') }}</label><br />
          <div class="image">
            <img v-if="profileData.logo !== ''" :src="this.logoURL" alt="$t('user.alt_profile_logo')"
              style="object-fit:contain;max-width:120px;max-height:120px;">
            <img v-if="profileData.logo === ''" src="@/assets/list_images/user.png" alt="$t(user.alt_profile_logo)"
              style="object-fit:contain;max-width:120px;max-height:120px;">
          </div>
          <input type="file" name="logo" @change="addLogo"><br />
          <label for="name">{{ $t('user.business') }}:</label><br />
          <input type="text" id="name" v-model="profileData.accountName" required><br />
          <label for="description">{{ $t('user.description') }}:</label><br />
          <textarea name="description" rows="5" cols="30" v-model="profileData.description" required></textarea><br />
          <label for="address">{{ $t('user.street_address') }}:</label><br />
          <input type="text" id="address" v-model="profileData.address" required><br />
          <label for="location">{{ $t('user.town') }}:</label><br />
          <input type="text" id="location" v-model="profileData.city" required><br />
        </div>
        <div className="container-item">
          <h1>{{ $t('user.billing') }}</h1>
          <label for="billingName">{{ $t('user.billingnamelabel') }}:</label><br />
          <input name="billingName" v-model="profileData.billing.name" required><br />
          <label for="billingBox">{{ $t('user.box') }}:</label><br />
          <input name="billingBox" v-model="profileData.billing.box" required><br />
          <label for="billingAddress">{{ $t('user.street_address') }}:</label><br />
          <input name="billingAddress" v-model="profileData.billing.address" required><br />
          <label for="orgNumber">{{ $t('user.orgnumberlabel') }}:</label><br />
          <input name="orgNumber" v-model="profileData.billing.orgNumber" required><br /><br />
          <h1>{{ $t('user.contact') }}</h1>
          <label for="email">{{ $t('user.emailcontactlabel') }}:</label><br />
          <input type="email" id="email" v-model="profileData.email" required><br />
          <label for="phone">{{ $t('user.telephonecontactlabel') }}:</label><br />
          <input type="tel" id="phone" v-model="profileData.phone" required><br /><br />
          <h1>{{ $t('user.active') }}</h1>
          <p><input type="checkbox" id="checkbox" v-model="profileData.is_active" required/></p>
          <button @click="submit" class="buttonflex">
            <p style="padding-right:7px"> {{ $t('user.saveLabel') }}: </p>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-device-floppy" width="30"
              height="30" viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round"
              stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <path d="M6 4h10l4 4v10a2 2 0 0 1 -2 2h-12a2 2 0 0 1 -2 -2v-12a2 2 0 0 1 2 -2" />
              <circle cx="12" cy="14" r="2" />
              <polyline points="14 4 14 8 8 8 8 4" />
            </svg>
          </button>
          <button @click="edit = !edit" class="buttonflex">
            <p style="padding-right:0px"> {{ $t('user.cancelLabel') }} </p>
            <svg xmlns="http://www.w3.org/2000/svg" class="icon icon-tabler icon-tabler-x" width="30" height="30"
              viewBox="0 0 24 24" stroke-width="1.5" stroke="#2c3e50" fill="none" stroke-linecap="round"
              stroke-linejoin="round">
              <path stroke="none" d="M0 0h24v24H0z" fill="none" />
              <line x1="18" y1="6" x2="6" y2="18" />
              <line x1="6" y1="6" x2="18" y2="18" />
            </svg>
          </button>
        </div>
      </form>
    </div>
  </div>
</template>

<script>
import { EXPRESS_URL, getAvailableBalance, sendMoney, postNotification, getUserAvailableBalance, getUserLimits, profile, updateuserProfile, getAvailableBalancesAndLimits } from '@/serverFetch'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'
import TextBox from '@/components/SharedComponents/TextBox.vue'
import TextArea from '@/components/SharedComponents/TextArea.vue'

export default {
  components: {
    PopupCard,
    TextBox,
    TextArea
  },
  props: ['userprofile'],
  data () {
    return {
      edit: false,
      logoURL: '',
      profileData: [],
      updateuserProfile,
      tkn: 0,
      comment: '',
      tknSentMsg: false,
      notEnoughBkrMsg: false,
      tooMuchBkrMsg: false,
      chatError: false,
      show_optional: false,
      invalidNumberOfBkr: false
    }
  },
  mounted () {
  },
  methods: {
    addLogo (e) {
      this.profileData.logo = e.target.files[0]
      //console.log(this.profileData.logo)
      this.localURL = URL.createObjectURL(this.profileData.logo)
    },
    submit () {
      this.updateuserProfile(
        this.profileData.previousname, 
        this.profileData.accountName, 
        this.profileData.description, 
        this.profileData.address,
        this.profileData.city, 
        this.profileData.billing.name, 
        this.profileData.billing.box, 
        this.profileData.billing.address,
        this.profileData.billing.orgNumber, 
        this.profileData.email, 
        this.profileData.phone,
        this.profileData.logo,
        this.profileData.is_active
      )
      if (this.localUrl) {
        this.logoURL = this.localURL
      }
      this.edit = !this.edit
    },
    getImgURL () {
      if (this.profileData.logo !== '') {
        this.logoURL = EXPRESS_URL + '/image/' + this.profileData.logo
      } else {
        this.logoURL = ''
      }
    },
    async sendBkr () {
      // TODO: get these requests to backend to occur simultaneously
      // TODO:: Combine the two requests into one for min and saldo
      this.$refs.loadingComponent.showLoading()
      this.tkn = this.$refs.tknInput.getInput()
      this.comment = this.$refs.commentInput.getInput()
      if (this.tkn && Number.isInteger(Number(this.tkn)) && Number(this.tkn) > 0) {
        const result = await getAvailableBalancesAndLimits(this.profileData.accountName)
        const mySaldo = result.requester.saldo
        const limits = result.requester
        const totalAvailableBalance = mySaldo.completed.balance - limits.min_limit
        if (totalAvailableBalance < this.tkn) {
          this.$refs.loadingComponent.hideLoading()
          this.notEnoughBkrMsg = true
        } else {
          if ((-mySaldo.pending.balance) + Number(this.tkn) > (-limits.min_limit)) {
            this.$refs.loadingComponent.hideLoading()
            this.actualAvailableCreditWithPending = Math.abs(limits.min_limit + mySaldo.pending.balance)
            this.pendingBalanceLimitExceeded = true
          } else {
            const userSaldo = result.requestee.saldo
            const userLimits = result.requestee
            if (userSaldo.completed.balance + Number(this.tkn) > userLimits.max_limit) {
              await postNotification('sendBalanceSellerBalanceTooHigh', this.profileData.accountName, Number(this.tkn))
              this.$refs.loadingComponent.hideLoading()
              this.tooMuchBkrMsg = true
            } else if (userSaldo.pending.balance + Number(this.tkn) > userLimits.max_limit) {
              await postNotification('sendBalanceSellerPendingLimitExceeded', this.profileData.accountName, Number(this.tkn))
              this.$refs.loadingComponent.hideLoading()
              this.tooMuchBkrMsg = true
            } else {
              await sendMoney(this.tkn, this.comment, this.profileData.accountName)
              await postNotification('sendRequest', this.profileData.accountName, this.tkn)
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
    closePopup () {
      this.tknSentMsh = false
      this.notEnoughBkrMsg = false
      this.tooMuchBkrMsg = false
      this.tkn = 0
      this.comment = ''
    },
    goToChat () {
      fetch(EXPRESS_URL + '/adminchat/' + this.profileData.accountName, {
        method: 'GET',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include'
      }).then(res => res.json())
        .then(data => {
          if (data !== false) {
            this.$router.push({ name: 'AdminChat', params: { chatID: data } })
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
    }
  },
  created: function () {
    for (const member of this.$store.state.allMembers) {
      if (member.accountName === this.userprofile) {
        this.profileData = member
        this.profileData.previousname = this.profileData.accountName
        //console.log(this.profileData.previousname)
        //console.log(this.profileData.accountName)
      }
    }
    this.getImgURL()
    if (this.userprofile !== this.$store.state.user.profile.accountName) {
      this.show_optional = true
    }
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
  font-size: 1.5rem;
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
  margin-bottom: 0px;
  display: block;
  margin-left: auto;
  margin-right: auto;
  margin-top: 20px;
}

#edit-btn {
  width: 135px;
  height: 60px;
  margin-left: 30%;
  margin-bottom: 0px;
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
  margin-right:50px;
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
  .image {
    width: 50%;
  }
  .sendmoney-box {
    font-size: 14px;
    height: fit-content;
  }
}

</style>
