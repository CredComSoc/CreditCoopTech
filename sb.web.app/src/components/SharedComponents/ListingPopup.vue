<template>
<div>
  <div @click="$emit('closePopup')" class="popup">
    
  </div>
  <div class="popup-inner">
    <splide :options="options">
      <splide-slide>
        <img :src='getImgURL(this.listingObj.coverImg)' style="object-fit:contain;max-width:280px;max-height:280px;">
      </splide-slide>
      <splide-slide v-if="this.listingObj.img.length >= 1">
        <img :src='getImgURL(this.listingObj.img[0])' style="object-fit:contain;max-width:280px;max-height:280px;">
      </splide-slide>
      <splide-slide  v-if="this.listingObj.img.length >= 2">
        <img :src='getImgURL(this.listingObj.img[1])' style="object-fit:contain;max-width:280px;max-height:280px;">
      </splide-slide>
      <splide-slide  v-if="this.listingObj.img.length >= 3">
        <img :src='getImgURL(this.listingObj.img[2])' style="object-fit:contain;max-width:280px;max-height:280px;">
      </splide-slide>
      <splide-slide  v-if="this.listingObj.img.length >= 4">
        <img :src='getImgURL(this.listingObj.img[3])' style="object-fit:contain;max-width:280px;max-height:280px;">
      </splide-slide>
    </splide>
    
      <div class="textContent">
        <h5>{{listingObj.title}}</h5>
        <div class="article-info">
          <h5 v-if="listingObj.status === 'selling'">{{ $t('Selling') }}</h5>
          <h5 v-if="listingObj.status === 'buying'">{{ $t('Buying') }}</h5>    
          <p>{{listingObj.userUploader}}</p>

          <h5>{{ $t('location') }}</h5>  
          <p>{{listingObj.destination}}</p>

          <h5>{{ $t('type') }}</h5> 
          <p v-if="listingObj.article === 'product'">{{ $t('product') }}</p>
          <p v-if="listingObj.article === 'service'">{{ $t('service') }}</p>

          <h5>{{$t('category')}}</h5> 
          <p>{{listingObj.category}}</p>

          <h5>{{ $t('user.description') }}</h5> 
          <p>{{listingObj.longDesc}}</p>
          
          <h5>{{ $t('list_price') }}</h5> 
          <p>{{listingObj.price}} {{ $t('org.token') }}</p>

          <div v-if="this.$store.state.user.profile.accountName.toLowerCase() !== listingObj.userUploader.toLowerCase() && listingObj.status === 'selling'" >
            <h5>{{ $t('quantity') }}</h5> 
            <div class="quant">
              <div @click="decreaseAmount">
                <img src="../../assets/cart_images/sub.png" >
              </div>
              <p class="amountText"> {{amount}} </p>
              <div @click="increaseAmount">
                <img src="../../assets/cart_images/add.png" >
              </div>
            </div>

            <h5>{{ $t('total_price') }}</h5> 
            <p>{{amount * listingObj.price}} {{ $t('org.token') }}</p>          
          </div>
        </div>

        <div class="spacing"></div>

      <button class="closeBtn" @click="$emit('closePopup')">{{ $t('Close') }}</button>
      <div class="interactContent" v-if="this.$store.state.user.profile.accountName.toLowerCase() !== listingObj.userUploader.toLowerCase() && listingObj.status === 'selling'">
        <div>
          <button class="cartBtn" @click="placeInCart(); $emit('closePopup');">{{ $t('add_to_cart') }}</button>
        </div>
      </div>
      <div class="interactContent" v-else>
        <div>
          <button class="cartBtn" @click="editItem(); $emit('closePopup');">{{ $t('edit_item') }}</button>
        </div>
      </div>
      <div class="interactContent" v-if="this.$store.state.user.profile.accountName.toLowerCase()!== listingObj.userUploader.toLowerCase() && listingObj.status === 'buying'">
        <div>
          <button class="chattBtn" @click="goToChat">{{ $t('chat.start') }}</button>
        </div>
      </div>
    </div>
  </div>
</div>
</template>
<script>
import { EXPRESS_URL, profile } from '../../serverFetch'
import '@splidejs/splide/dist/css/themes/splide-default.min.css'
export default {

  props: {
    listingObj: Object,
    username: String  
  },

  data () {
    return {
      amount: 1,
      options: {
        type: 'loop',
        width: '600px',
        gap: '1rem',
        arrows: this.listingObj.img.length > 0,
        pagination: this.listingObj.img.length > 0
      }
    }
  },

  methods: {
    decreaseAmount () {
      if (this.amount > 1) {
        this.amount--
      }
    },
    increaseAmount () {
      if (this.amount < 99) {
        this.amount++
      }
    },
    getImgURL (img) {
      return EXPRESS_URL + '/image/' + img
    },
    placeInCart () {
      this.$emit('placeInCart', this.amount, this.listingObj)

      profile().then(res => {
        // if (res.name !== this.listingObj.userUploader) {
        // }
      })
    },
    goToChat () {
      fetch(EXPRESS_URL + '/chat/' + this.listingObj.userUploader, {
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
    editItem () {
      // TODO: push to item editing page
      this.$router.push({ name: 'Edit_Article', params: { artID: this.listingObj.id } })
    }
  },
  created: function () {
    // this.getSmallerImages(this.listingObj.img)
  }
}
</script>

<style scoped>

.spacing {
  height: 50px;
}
.quant {
  display: flex;
  flex-direction: row;
  white-space: nowrap; 
}

.quant div {
  width: 18px;
  height: 18px;
  border: 1px solid black;
  border-radius: 4px;
  display: flex;
  justify-content: center;
  align-items: center;  
}
.quant div:hover {
  transform: scale(1.10);
}

.amountText {
  bottom: 50px;
  right: 100px;
  font-size: 22px;
  text-align: center;
  width: 28px;

}

.article-info {
  text-align: left;
  margin-top: 10px;
}

.article-info h5 {
  font-size: 13px;
  font-weight: bold;
}

.article-info p {
  font-size: 11px;
  font-style: italic;
}

.popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 99;
  background-color: rgba(0, 0, 0, 0.05);
  display: flex;
  z-index: 2
}

.popup-inner {
  position: fixed; 
  background: #FFFFFF;
  border: 4px solid #C4C4C4;
  box-sizing: border-box;
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  flex-direction: column;
  width: 520px;
  font-size-adjust: 0.58;
  top: 50%;
  left: 50%;
  transform: translate(-50%, -50%);
  padding:0;
  z-index: 5;
  font-size: 1.4rem;
  text-align: center;
  max-height: 100%;
  overflow: scroll;
}

h5 {
  font-size: 1.6rem;
}

.textContent {
  padding: 1rem;
}

.content-right {
  width:100%;
  display: flex;
  flex-direction: column;
  background-color: white;

}

.flex-center-bottom {
  align-items: center;
  display: flex;
  justify-content: flex-end;
  padding-right: 1rem;
  
}

.interactContent {
  /* position:absolute; */
  /* text-align: right; */
  bottom: 0;
  right: 1rem;
  /* width: 33%; */
}

.interactContent > * {
  width: 100%;
}

.cartBtn, .chattBtn, .closeBtn{
    background-color:#4690CD;
    color: white;
    border-radius: 10px;
    border: none;
    white-space: nowrap;   
}

.cartBtn, .chattBtn {
    /* position:absolute; */
    padding: 5px 15px 5px 15px;
    right: 0;
    bottom:0;
    margin-bottom: 0.5rem; 
    display: inline-block;    
}

.closeBtn {
    /* position:absolute; */
    float: right;
    padding: 5px 15px 5px 15px;
    left: 1rem;
    bottom:0;
    margin-bottom: 0.5rem; 
    display: inline-block;
    background-color: #CD9046;  
}

.closeBtn:hover {
  background-color: #AD7E45;;
}

.cartBtn:hover, .chattBtn:hover {
  background: #457EAD;
}

@media screen and (max-width: 860px) {
  .popup-inner {
    width: 80%;
    max-height: 70%;
    overflow: scroll;
    font-size-adjust: 0.4;
  }
  .content-right {
    width: 100%;
  }

  img {
    width: 55%;
    height: 55%;
  }

}

</style>
