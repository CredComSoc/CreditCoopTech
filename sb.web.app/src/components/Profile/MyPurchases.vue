<template>
  <div>
    <h1><b> Köpförfrågningar </b></h1>
      <div style="max-height: 50em; overflow: scroll; overflow-x: hidden; padding-top: 20px; padding-bottom: 20px;">
        <table v-if="requests">
          <tr>
            <th>Företag</th>
            <th>Artikel</th>
            <th>Antal</th>
            <th>Pris</th>
            <th>Summa</th>
            <th>Tidstämpel</th>
            <th>Status</th>
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
              <button @click="cancel(item.uuid, item.entries[0].payer, index)" style="background-color: red;"> Avbryt </button>
              <button @click="accept(item.uuid, item.entries[0].payer, index, item.entries[0].quant)" style="background-color: green;"> Godkänn </button>
            </td>

          </tr>
          <tr v-for="(item) in this.$store.state.requests.filter(request => request.state==='completed')" :key="item">
            <td>{{item.entries[0].payer}}</td>
            <td v-if="item.entries[0].metadata.id !== '0'"><Listing :listingObj="getListing(item.entries[0])" /></td>
            <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="'0'" :comment="item.entries[0].description"/></td>
            <td>{{item.entries[0].metadata.quantity}}</td>
            <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
            <td>{{item.entries[0].quant}}</td>
            <th>{{item.written}}</th>
            <td style="color: green;">GODKÄND</td>
          </tr>
        </table>
        <div v-if="!requests">
          <h4> Du har inte fått några köpförfrågningar än. </h4>
        </div>
        <PopupCard v-if="this.payeeTooMuchBkr" @closePopup="this.closePopup" title="Förbjuden förfrågan" btnLink="" btnText="Ok" :cardText="`Köpförfrågan kan inte godkännas, din övre gräns är ` + this.max_limit + ' bKr.'" />
        <PopupCard v-if="this.payerNotEnoughBkr" @closePopup="this.closePopup" title="Förbjuden förfrågan" btnLink="" btnText="Ok" :cardText="`Köpförfrågan kan inte godkännas, köparen har inte tillräckligt med bKr.`" />
      </div>
    </div>
    <h1><b> Väntande köp </b></h1>
      <div>
        <p v-if="pendingPurchases.length > 0"> Du har väntande köp som ska godkännas av köparen innan köpet genomförs. Du kommer få en notis när köparen godkänt köpet. </p>
      </div>
      <div style="max-height: 50em; overflow: scroll; overflow-x: hidden;">
        <table>
          <tr>
            <th>Företag</th>
            <th>Artikel</th>
            <th>Antal</th>
            <th>Pris</th>
            <th>Summa</th>
            <th>Tidstämpel</th>
            <th>Status</th>
          </tr>
          <tr v-for="(item, index) in this.$store.state.pendingPurchases" :key="item" ref="reqRefs">
            <td>{{item.entries[0].payee}}</td>
            <td v-if="item.entries[0].metadata.id !== '0'"><Listing :listingObj="getListing(item.entries[0])" /></td>
            <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="'0'" :comment="item.entries[0].description"/></td>
            <td>{{item.entries[0].metadata.quantity}}</td>
            <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
            <td>{{item.entries[0].quant}}</td>
            <th>{{item.written}}</th>
            <td id="buttons">
              <button @click="cancel(item.uuid, index)" style="background-color: red;"> Avbryt </button>
            </td>
          </tr>
        </table>
      </div>
      <h1><b> Köphistorik </b></h1>
      
    <div>
      <div className='filter flexbox-item' style ="padding-top: 20px;padding-bottom: 0px; margin-left: 70px;">
        <p2>Filter:
        <a href='#' @click="this.tab='profile'" :class="{ active: this.tab!='purchases' && this.tab!='articles' && this.tab!='requests' && this.tab!='economy' }" id='profile'>Senaste Först</a>
        <a href='#' @click="this.tab='purchases'" :class="{ active: this.tab==='purchases' }" id='purchases'>Senaste Sist</a>
        <a href='#' @click="this.tab='articles'" :class="{ active: this.tab==='articles' }" id='articles'>Företag</a>
        <a href='#' @click="this.tab='requests'" :class="{ active: this.tab==='requests' }" id='requests'>Pris</a>
        <a href='#' @click="this.tab='economy'" :class="{ active: this.tab==='economy' }" id='economy'>Summa</a>
        </p2>
    </div>
      
      <div style="max-height: 50em; overflow: scroll; overflow-x: hidden;">
      <table>
        <tr>
          <th>Företag</th>
          <th>Artikel</th>
          <th>Antal</th>
          <th>Pris</th>
          <th>Summa</th>
          <th>Tidstämpel</th>
          <th>Faktura</th>      
        </tr>
        <tr v-for="(item) in this.$store.state.completedPurchases" :key="item">
          <td>{{item.entries[0].payee}}</td>
          <td v-if="item.entries[0].metadata.id !== '0'"><Listing :listingObj="getListing(item.entries[0])" /></td>
          <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="'0'" :comment="item.entries[0].description"/></td>
          <td>{{item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant}}</td>
          <th>{{item.written}}</th>
          <td><button className="red" @click="invoice('test.txt', item)">Ladda ner faktura</button></td>
        </tr>
      </table>
      </div>
    </div>
</template>

<script>
import { getPurchases, cancelRequest } from '../../serverFetch'
import Listing from '@/components/SharedComponents/Listing.vue'

export default {

  data () {
    return {
      completedPurchases: [],
      pendingPurchases: [],
      componentKey: 0
    }
  },
  components: {
    Listing
  },
  methods: {
    
    invoice (filename, item) {
      console.log(item.entries[0])
      const pom = document.createElement('a')
      const text = 'hello'
      pom.setAttribute('href', 'data:text/plain;charset=utf-8,' + encodeURIComponent(text))
      pom.setAttribute('download', filename)

      if (document.createEvent) {
        const event = document.createEvent('MouseEvents')
        event.initEvent('click', true, true)
        pom.dispatchEvent(event)
      } else {
        pom.click()
      }
    },
    cancel (id, index) {
      console.log('Canceling order: ' + id)
      this.statusSwap(index)
      cancelRequest(id)
    },
    statusSwap (index) {
      const tag = document.createElement('p')
      const text = document.createTextNode('AVBRUTEN')
      tag.style.color = 'red'
      tag.appendChild(text)
      const element = this.$refs.reqRefs[index]
      const child = element.lastElementChild
      let grandChild = child.lastElementChild
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

h1 {
  padding: 10px 0px 10px 0px;
  font-size: 1.6rem;
}

p {
  padding: 10px 0px 10px 0px;
  font-size: 1.2rem;
}

p2 {
  padding: 10px 0px 0px 0px;
  font-size: 1.2rem;
}
.green {
  color:green;
}

.red {
  color: red;
}

.article {
  align-content: center;
  display: flex;
  justify-content: center;
}

button {
  color: white;
  margin-right: 10px;
  border-radius: 5px;
  font-size: 1.2rem;
  padding: 2px 15px 2px 15px;
}

.filter a {
  text-align: center;
  padding: 8px 8px 8px 8px;
  text-decoration: none;
  font-size: 12px;
  color: steelblue;
  border-style: solid;
  border-color: gainsboro;
  border-radius: 5px;
  margin-top: -1px;
  margin-bottom: 2px;
  margin-left: 1px;
  margin-right: -1px;
  border-width: 2px;
}

.filter a:hover {
      box-shadow: 2px 2px 2px rgba(0, 0, 0, 0.25);  
}

.filter a.active {
  color: dimgrey;
  font-weight: bold;
  background-color: brown;
}

</style>
