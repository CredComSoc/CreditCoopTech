<template>
  <div id="cart-container">
    <h1 class="title"> {{ $t('cart.cart') }} </h1>
    <EmptyCart v-if="this.gotCartRes && this.cart.length === 0" />
    <FilledCart v-if="this.gotCartRes && this.cart.length > 0" :total="this.total" :cart="this.cart" @remove-row="this.removeRow"  @add-item="this.addItem" @min-item="this.minItem" @complete-purchase="this.completePurchase"/>
      <PopupCard v-if="this.confirmPress" :title="$t('cart.purchase_thanks_header')" btnLink="/" btnText="Ok" :cardText="$t('cart.purchase_thanks_notified')" />
    <PopupCard v-if="this.insufficientBalance" :title="$t('cart.insufficient_credit')" btnLink="/cart" btnText="Ok" :cardText="this.insufficientBalanceMessage"/>
    <PopupCard v-if="this.sellerLimitError" :title="$t('shop.seller_balance_too_high')" btnLink="/cart" btnText="Ok" :cardText="$t('shop.seller_has_reached_limit', {'seller': this.seller})" />
    <PopupCard v-if="this.transactionFailed" :title="$t('cart.transaction_failed')" btnLink="/cart" btnText="Ok" :cardText="$t('cart.transactions_failed_for_items')" />
    <LoadingComponent ref="loadingComponent" />
  </div>
</template>

<script>
import EmptyCart from './EmptyCart.vue'
import FilledCart from './FilledCart.vue'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'
import { EXPRESS_URL, createTransactions, getAvailableBalance, getUserAvailableBalance, getUserLimits, setStoreData, setCartData, postNotification } from '../../serverFetch'
import LoadingComponent from '../SharedComponents/LoadingComponent.vue'
export default {
  name: 'ShoppingCart',
  props: [],
  components: {
    EmptyCart,
    FilledCart,
    PopupCard,
    LoadingComponent
  },
  async mounted () {
    await setCartData()
    if (this.$store.state.myCart) {
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
      seller: '',
      transactionFailed: false,
      insufficientBalanceMessage: '',
      availableBalance: 0,
      failedTransactionsMessage: ''
    }
  },
  methods: {
    async removeRow (ind) {
      await fetch(EXPRESS_URL + '/cart/remove/item/' + this.cart[ind - 1].id, {
        method: 'POST',
        credentials: 'include'
      }).then(
      ).catch(
        error => console.log(error)
      )
      setTimeout(async () => {
        await setCartData()
        this.calcTotal()
      })
    },
    async addItem (ind) {
      const quant = this.cart[ind - 1].quantity
      await fetch(EXPRESS_URL + '/cart/set/item/' + this.cart[ind - 1].id, {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ quantity: quant + 1 }),
        credentials: 'include'
      }).then(
      ).catch(
        error => console.log(error)
      )
      setTimeout(async () => {
        await setCartData()
        this.calcTotal()
      })
    },
    async minItem (ind) {
      if (this.cart[ind - 1].quantity > 1) {
        const quant = this.cart[ind - 1].quantity
        await fetch(EXPRESS_URL + '/cart/set/item/' + this.cart[ind - 1].id, {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({ quantity: quant - 1 }),
          credentials: 'include'
        }).then(
        ).catch(
          error => console.log(error)
        )
    
        setTimeout(async () => {
          await setCartData()
          this.calcTotal()
        })
      }
    },
    calcTotal () {
      this.cart = this.$store.state.myCart
      let total = 0
      for (let i = 0; i < this.cart.length; i++) {
        total += this.cart[i].price * this.cart[i].quantity
      }
      this.total = total
    },
    async completePurchase () {
      this.$refs.loadingComponent.showLoading()
      getAvailableBalance().then(async (res) => { //saldo(cc-node) + creditline (min_limit in database)
        console.log(this.total)
        this.availableBalance = res
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
              await postNotification('sellerLimitExceeded', key, value)
              if (this.seller === '') {
                this.seller = key
              } else {
                this.seller = this.seller + ', ' + key
              }    
            }
          }
          if (this.seller) {
            this.sellerLimitError = true
            this.$refs.loadingComponent.hideLoading()
            return
          }

          const transactionValue = await createTransactions(this.cart)

          if (transactionValue.successResults.length === this.cart.length) { // if success transactions is equal to the cart means all carts transactions were a success so continue with the normal flow
            this.confirmPress = true

            // this.confirmPress = false
            this.$store.commit('setMyCart', [])
            this.cart = []
            // remove all items from cart
            await fetch(EXPRESS_URL + '/cart/remove', {
              method: 'POST',
              credentials: 'include'
            }).then(
            ).catch(
              error => console.log(error)
            )
            setTimeout(async () => {
              await setCartData()
              this.calcTotal()
            })
            this.$refs.loadingComponent.hideLoading()
          } else {
            const failedResults = this.cart.filter(item => !transactionValue.failedResults.some(innerItem => innerItem.id === item.id))
            transactionValue.successResults.forEach(async (e) => {
              await fetch(EXPRESS_URL + '/cart/remove/item/' + e.id, {
                method: 'POST',
                credentials: 'include'
              }).then(
              ).catch(
                error => console.log(error)
              )
            })
            this.cart = failedResults
            setTimeout(() => {
              setCartData()
            })
            this.calcTotal()
            this.$refs.loadingComponent.hideLoading()
            this.transactionFailed = true
          }
        } else {
          // display insufficient balance msg
          this.insufficientBalanceMessage = `${this.$t('cart.not_enough_credit')} ${this.total} ${this.$t('cart.available_credit')} ${this.availableBalance} ${this.$t('org.token')}`
          this.insufficientBalance = true
          this.$refs.loadingComponent.hideLoading()
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

 .title {
   text-transform: uppercase
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
