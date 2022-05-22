<template>
  <div style="max-height: 50em; overflow: scroll;">
    <table v-if="requests">
      <tr>
        <th></th>
        <th>Företag</th>
        <th>Artikel</th>
        <th>Antal</th>
        <th>Pris</th>
        <th>Summa</th>
        <th>Status</th>
      </tr>
      <tr v-for="(item, index) in requests.filter(request => request.state==='pending')" :key="item" ref="reqRefs">
        <td>{{index + 1 + '.'}}</td>
        <td>{{item.entries[0].payer}}</td>
        <td v-if="item.entries[0].metadata.id !== '0'"><Listing :listingId="getListing(item.entries[0])" /></td>
        <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="getListing(item.entries[0])" :comment="item.entries[0].description"/></td>
        <td>{{item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant}}</td>
        <td id="buttons">
          <button @click="cancel(item.uuid, item.entries[0].payer, index)" style="background-color: red;"> Avbryt </button>
          <button @click="accept(item.uuid, item.entries[0].payer, index, item.entries[0].quant)" style="background-color: green;"> Godkänn </button>
        </td>
      </tr>
      <tr v-for="(item, index) in requests.filter(request => request.state==='completed')" :key="item">
        <td>{{index + 1 + '.'}}</td>
        <td>{{item.entries[0].payer}}</td>
        <td v-if="item.entries[0].metadata.id !== '0'"><Listing :listingId="getListing(item.entries[0])" /></td>
        <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="getListing(item.entries[0])" :comment="item.entries[0].description"/></td>
        <td>{{item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant}}</td>
        <td><p style="color: green;">GODKÄND</p></td>
      </tr>
    </table>
    <div v-if="!requests">
      <h4> Du har inte fått några köpförfrågningar än. </h4>
    </div>
    <PopupCard v-if="this.payeeTooMuchBkr" @closePopup="this.closePopup" title="Förbjuden förfrågan ;)" btnLink="" btnText="Ok" :cardText="`Köpförfrågan kan inte godkännas, din övre gräns är ` + this.max_limit + ' bKr.'" />
    <PopupCard v-if="this.payerNotEnoughBkr" @closePopup="this.closePopup" title="Förbjuden förfrågan ;)" btnLink="" btnText="Ok" :cardText="`Köpförfrågan kan inte godkännas, köparen har inte tillräckligt med bKr.`" />
  </div>
</template>

<script>
import { getRequests, cancelRequest, acceptRequest, postNotification, getAvailableBalance, getUserAvailableBalance, getLimits } from '../../serverFetch'
import Listing from '@/components/userstory4/Listing.vue'
import PopupCard from '../CreateArticle/PopupCard.vue'

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
  mounted () {
    getRequests()
      .then(res => {
        console.log(res)
        this.requests = res
      })
  },
  methods: {
    cancel (id, payer, index) {
      //const element = this.$refs.reqRefs[index]
      //element.parentNode.removeChild(element)
      this.statusSwap(index, 'cancel')
      cancelRequest(id)
      postNotification('saleRequestDenied', payer)
    },

    accept (id, payer, index, cost) {
      getAvailableBalance().then((balance) => {
        getLimits().then((limits) => {
          this.max_limit = limits.max
          if (balance + limits.min + cost > limits.max) {
            this.payeeTooMuchBkr = true
          } else {
            getUserAvailableBalance(payer).then((payerBalance) => {
              if (cost <= payerBalance) {
                this.statusSwap(index, 'accept')
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
    statusSwap (index, answer) {
      var tag = document.createElement('p')
      var text
      if (answer === 'cancel') {
        text = document.createTextNode('NEKAD')
        tag.style.color = 'red'
      } else {
        text = document.createTextNode('GODKÄND')
        tag.style.color = 'green'
      }
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
      return item.metadata.id
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
