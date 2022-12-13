<template>
  <div id="cart-container">
    <h1> VARUKORG </h1>
    <EmptyCart v-if="this.gotCartRes && this.cart.length === 0" />
    <FilledCart v-if="this.gotCartRes && this.cart.length > 0" :total="this.total" :cart="this.cart" @remove-row="this.removeRow"  @add-item="this.addItem" @min-item="this.minItem" @complete-purchase="this.completePurchase"/>
    <PopupCard v-if="this.confirmPress" title="Tack för ditt köp" btnLink="/" btnText="Ok" :cardText="`Tack för ditt köp! Säljaren har meddelats. Du kommer få en\nnotis när säljaren bekräftat din köpförfrågan.`" />
    <PopupCard v-if="this.insufficientBalance" title="Köpet kunde inte genomföras" btnLink="/" btnText="Ok" :cardText="`Du har inte tillräckligt med barterkronor för att genomföra köpet.`" />
    <PopupCard v-if="this.sellerLimitError" title="Köpet kunde inte genomföras" btnLink="/" btnText="Ok" :cardText="'Säljaren ' + this.seller + ` har nått sin övre gräns för barterkronor.`" />
  </div>
</template>

<script>
import EmptyCart from './EmptyCart.vue'
import FilledCart from './FilledCart.vue'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'
import { EXPRESS_URL, createTransactions, getAvailableBalance, getUserAvailableBalance, getUserLimits } from '../../serverFetch'
export default {
  name: 'ShoppingCart',
  props: [],
  components: {
    EmptyCart,
    FilledCart,
    PopupCard
  },
  mounted () {
    if (this.$store.state.myCart) {
      this.cart = this.$store.state.myCart
      this.calcTotal()
    }
    this.gotCartRes = true
  },
  data () {
    return {
      cart: [],
      total: 0,
      gotCartRes: false,
      confirmPress: false,
      insufficientBalance: false,
      sellerLimitError: false,
      seller: ''
    }
  },
  methods: {
    removeRow (ind) {
      fetch(EXPRESS_URL + '/cart/remove/item/' + this.cart[ind - 1].id, {
        method: 'POST',
        credentials: 'include'
      }).then(
        this.cart.splice(ind - 1, 1),
        this.calcTotal()
      ).catch(
        error => console.log(error)
      )
    },
    addItem (ind) {
      this.cart[ind - 1].quantity++
      fetch(EXPRESS_URL + '/cart/set/item/' + this.cart[ind - 1].id, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ quantity: this.cart[ind - 1].quantity }),
        credentials: 'include'
      }).then(
        this.calcTotal()
      ).catch(
        error => console.log(error)
      )
    },
    minItem (ind) {
      if (this.cart[ind - 1].quantity > 1) {
        this.cart[ind - 1].quantity--
        fetch(EXPRESS_URL + '/cart/set/item/' + this.cart[ind - 1].id, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ quantity: this.cart[ind - 1].quantity }),
          credentials: 'include'
        }).then(
          this.calcTotal()
        ).catch(
          error => console.log(error)
        )
      }
    },
    calcTotal () {
      let total = 0
      for (let i = 0; i < this.cart.length; i++) {
        total += this.cart[i].price * this.cart[i].quantity
      }
      this.total = total
    },
    completePurchase () {
      getAvailableBalance().then(async (res) => { //saldo(cc-node) + creditline (min_limit in database)
        if (res >= this.total) {
          const totalCosts = {}
          for (let i = 0; i < this.cart.length; i++) {
            if (!(this.cart[i].userUploader in totalCosts)) {
              totalCosts[this.cart[i].userUploader] = 0
            }
            totalCosts[this.cart[i].userUploader] += Number(this.cart[i].price) * this.cart[i].quantity
          }

          for (const [key, value] of Object.entries(totalCosts)) {
            const userSaldo = await getUserAvailableBalance(key)
            const userLimits = await getUserLimits(key)
            if (userSaldo + userLimits.min + value > userLimits.max) {
              this.seller = key
              this.sellerLimitError = true
              return
            }
          }

          this.confirmPress = true
          createTransactions(this.cart)
          this.cart = []
          // remove all items from cart
          fetch(EXPRESS_URL + '/cart/remove', {
            method: 'POST',
            credentials: 'include'
          }).catch(
            error => console.log(error)
          )
        } else {
          // display insufficient balance msg
          this.insufficientBalance = true
        }
      })
    }
  }
}
</script>

<style scoped>

  *{
    font-family: 'Ubuntu' ;
    font-style: normal;
    font-weight: normal;
    letter-spacing: 0.05em;
    padding: 0;
    box-sizing: border-box;
    margin: 0 auto;
  }

  h1 {
    margin-top: 4rem;
    margin-bottom: 4rem;
    font-size: 2.2rem;
    letter-spacing: 0.25em;
  }

  #cart-container{
    margin-top: 75px;
    width: 60%;
    min-height: 200px;
    position: relative;
    text-align: center;
  }

  @media (max-width: 1450px) {
      #cart-container{
          width: 70%;
      }
 }

 @media (max-width: 1300px) {
      #cart-container{
          width: 80%;
      }
 }

 @media (max-width: 1200px) {
      #cart-container{
          width: 90%;
      }
 }

  @media (max-width: 860px) {
      #cart-container{
          width: 70%;
      }
 }

  @media (max-width: 500px) {
      #cart-container{
          width: 80%;
      }
 }

 @media (max-width: 370px) {
      #cart-container{
          width: 95%;
      }
 }

</style>
