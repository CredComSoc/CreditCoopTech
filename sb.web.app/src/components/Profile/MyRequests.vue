<template>
  <div style="max-height: 50em; overflow: scroll; ">
    <table v-if="requests">
      <tr>
        <th>{{ $t('member') }}</th>
        <th>{{ $t('article')}}</th>
        <th>{{ $t('quantity') }}</th>
        <th>{{ $t('price') }}</th>
        <th>{{ $t('amount') }}</th>
        <th>{{ $t('timestamp') }}</th>
        <th>{{ $t('status') }}</th>
      </tr>
      <tr v-for="(item, index) in this.$store.state.requests.filter(request => request.state==='pending')" :key="item" ref="reqRefs">
        <td>{{item.entries[0].payer}}</td>
        <td v-if="item.entries[0].metadata.id !== '0'"><Listing :listingObj="getListing(item.entries[0])" /></td>
        <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="'0'" :comment="item.entries[0].description"/></td>
        <td>{{item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant}}</td>
        <th>{{item.written}}</th>
        <td id="buttons">
          <button @click="cancel(item.uuid, item, index)" style="background-color: red;"> {{ $t('user.cancelLabel') }} </button>
          <button @click="accept(item.uuid, item.entries[0].payer, index, item.entries[0].quant)" style="background-color: green;"> {{ $t('user.approveLabel') }} </button>
        </td>

      </tr>
      <!--<tr v-for="(item) in this.$store.state.requests.filter(request => request.state==='completed')" :key="item">
        <td>{{item.entries[0].payer}}</td>
        <td v-if="item.entries[0].metadata.id !== '0'"><Listing :listingObj="getListing(item.entries[0])" /></td>
        <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="'0'" :comment="item.entries[0].description"/></td>
        <td>{{item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant}}</td>
        <th>{{item.written}}</th>
        <td style="color: green;">{{ $t('approving') }}</td>
      </tr>
    -->
    </table>
    <div v-if="!requests">
      <h4> {{ $t('youHaventReceivedAnyPurchaseRequestsYet')}} </h4>
    </div>
    <PopupCard v-if="this.payeeTooMuchBkr" @closePopup="this.closePopup" :title="$t('user.prohibitedRequest')" btnLink="" btnText="Ok" :cardText="$t('shop.purchaseRequestCannotBeApproved') + ', ' + $t('upper_limit_is') + this.max_limit + ' bKr.'" />
    <PopupCard v-if="this.payerNotEnoughBkr" @closePopup="this.closePopup" :title="$t('user.prohibitedRequest')" btnLink="" btnText="Ok" :cardText="$t('shop.purchaseRequestCannotBeApproved') + ', ', + $t('not_enough_credit_unit')" />
  </div>
</template>

<script>
import { cancelRequest, acceptRequest, postNotification, getAvailableBalance, getUserAvailableBalance, getLimits } from '../../serverFetch'
import Listing from '@/components/SharedComponents/Listing.vue'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'

export default {
  components: {
    Listing,
    PopupCard
  },
  data () {  
    return {
      requests: [],
      payerNotEnoughBkr: false,
      payeeTooMuchBkr: false,
      max_limit: 0
    }
  },
  methods: {
    cancel (id, item, index) {
      this.statusSwap(index, this.$i18n.t('declining'), 'red')
      cancelRequest(id)
      const payer = item.entries[0].payer
      const item_count = item.entries[0].metadata.quantity
      const amount = item.entries[0].quant
      const item_data = getListing(item)
      const item_name = item_data.title
      postNotification('saleRequestDenied', payer, amount, item_name, item_count)
    },

    accept (id, payer, index, cost) {
      getAvailableBalance().then((balance) => {
        getLimits().then((limits) => {
          this.max_limit = limits.max
          if (balance.totalAvailableBalance + limits.min + cost > limits.max) {
            this.payeeTooMuchBkr = true
          } else {
            getUserAvailableBalance(payer).then((payerBalance) => {
              if (cost <= payerBalance.totalAvailableBalance) {
                this.statusSwap(index, this.$i18n.t('approving'), 'green')
                acceptRequest(id)
                postNotification('saleRequestAccepted', payer)
              } else {
                this.payerNotEnoughBkr = true
              } 
            })
          }
        })
      })
    },
    closePopup () {
      this.payerNotEnoughBkr = false
      this.payeeTooMuchBkr = false
    },
    statusSwap (index, messageText, color) {
      var tag = document.createElement('p')
      var text = document.createTextNode(messageText)
      tag.style.color = color
      tag.appendChild(text)
      const element = this.$refs.reqRefs[index]
      const child = element.lastElementChild
      var grandChild = child.lastElementChild
      while (grandChild) {
        child.removeChild(grandChild)
        grandChild = child.lastElementChild
      }
      child.appendChild(tag)
    },
    getListing (item) {
      for (const listing of this.$store.state.allArticles) {
        if (listing.id === item.metadata.id) {
          return listing
        }
      }
    }
  }
}

</script>

<style scoped>

table {
  margin-left: auto;
  margin-right: auto;
  border-spacing:50px;
  width: 100%;
  text-align: center;
  font-size: 1.2rem;
}

th {
  padding: 20px;
  font-weight: normal;
}

td {
  padding: 10px 0px 10px 0px;
}

button {
  color: white;
  margin-right: 10px;
  border-radius: 5px;
  font-size: 1.2rem;
  padding: 2px 15px 2px 15px;
}

.article {
  align-content: center;
  display: flex;
  justify-content: center;
}

</style>
