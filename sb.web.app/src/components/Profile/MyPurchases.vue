<template>
  <div>
    <!--PAYEE AND PAYER NEEDS TO BE ADJUSTED SO ITS RIGHT-->
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
          <tr v-for="(item, index) in this.$store.state.requests" :key="item" ref="reqRefs">
            <td>{{item.payer}}</td>
          <td v-if="item.metadata.id !== '0'"><Listing :listingObj="getListing(item)" /></td>
          <td v-if="item.metadata.id === '0'"><Listing :listingId="'0'" :comment="item.description"/></td>
          <td>{{item.metadata.quantity}}</td>
          <td>{{item.quant / item.metadata.quantity}}</td>
          <td>{{item.quant}}</td>
          <td>{{item.metadata.time.split('T')[0]}}</td>
          <th>{{item.written}}</th>
            <td id="buttons">
              <button @click="cancel(item.uuid, item.payer, index)" style="background-color: red;"> Avbryt </button>
              <button @click="accept(item.uuid, item.payer, index, item.quant)" style="background-color: green;"> Godkänn </button>
            </td>
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
        <p v-if="this.$store.state.pendingPurchases.length > 0"> Du har väntande köp som ska godkännas av köparen innan köpet genomförs. Du kommer få en notis när köparen godkänt köpet. </p>
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
            <td>{{item.payee}}</td>
          <td v-if="item.metadata.id !== '0'"><Listing :listingObj="getListing(item)" /></td>
          <td v-if="item.metadata.id === '0'"><Listing :listingId="'0'" :comment="item.description"/></td>
          <td>{{item.metadata.quantity}}</td>
          <td>{{item.quant / item.metadata.quantity}}</td>
          <td>{{item.quant}}</td>
          <td>{{item.metadata.time.split('T')[0]}}</td>
          <th>{{item.written}}</th>
            <td id="buttons">
              <button @click="cancel(item.uuid, index)" style="background-color: red;"> Avbryt </button>
            </td>
          </tr>
        </table>
      </div>
      <h1><b> Köphistorik </b></h1>
      
    <div>
      <div className='filter flexbox-item' style ="padding-top: 20px;padding-bottom: 0px; margin-left: 15px;">
        <button @click="filterTransactions()">Filtrera</button>
        <DateFilter class= "DateFilter filterObject" ref="startDateInput" name="start-date-filter" :placeholder="`Från och med`" @click="handleDate()"/>
        <DateFilter class= "DateFilter filterObject" ref="endDateInput" name="end-date-filter" :placeholder="`Till och med`" @click="handleDate()"/>
        <input class="box-input filterObject" type="text" v-model="company" ref="companyInput" name="company-filter" placeholder="Företag" id="company-input">
        <input class="box-input filterObject" type="text" v-model="product" ref="productInput" name="product-filter" placeholder="Produkt" id="product-input">
    </div>
      <div style="max-height: 50em; overflow: scroll; overflow-x: hidden;">
      <table v-if="(!this.filterActive)">
        <tr>
          <th>Köpare</th>
          <th>Säljare</th>
          <th>Artikel</th>
          <th>Antal</th>
          <th>Pris</th>
          <th>Summa</th>
          <th>Tidstämpel</th>
          <th>Faktura</th>      
        </tr>
        <tr v-for="(item) in this.$store.state.completedTransactions" :key="item">
          <td>{{item.payer}}</td>
          <td>{{item.payee}}</td>
          <td v-if="item.metadata.id !== '0'">{{getListing_title(item)}}</td>
          <td v-if="item.metadata.id === '0'"><Listing :listingId="'0'" :comment="item.description"/></td>
          <td>{{item.metadata.quantity}}</td>
          <td>{{item.quant / item.metadata.quantity}}</td>
          <td>{{item.quant}}</td>
          <td>{{item.metadata.time.split('T')[0]}}</td>
          <th>{{item.written}}</th>
          <td><button className="red" @click="invoice('test.txt', item)">Ladda ner faktura</button></td>
        </tr>
      </table>
      <table v-if="(this.filterActive)">
        <tr>
          <th>Köpare</th>
          <th>Säljare</th>
          <th>Artikel</th>
          <th>Antal</th>
          <th>Pris</th>
          <th>Summa</th>
          <th>Tidstämpel</th>
          <th>Faktura</th>      
        </tr>
        <tr v-for="(item) in this.filteredTransactions" :key="item">
          <td>{{item.payer}}</td>
          <td>{{item.payee}}</td>
          <td v-if="item.metadata.id !== '0'">{{getListing_title(item)}}</td>
          <td v-if="item.metadata.id === '0'"><Listing :listingId="'0'" :comment="item.description"/></td>
          <td>{{item.metadata.quantity}}</td>
          <td>{{item.quant / item.metadata.quantity}}</td>
          <td>{{item.quant}}</td>
          <td>{{item.metadata.time.split('T')[0]}}</td>
          <th>{{item.written}}</th> <!--might be date from cc-node-->
          <td><button className="red" @click="invoice('test.txt', item)">Ladda ner faktura</button></td>
        </tr>
      </table>
      </div>
    </div>
    
</template>

<script>
import { getPurchases, cancelRequest, acceptRequest, postNotification, getAvailableBalance, getUserAvailableBalance, getLimits } from '../../serverFetch'
import Listing from '@/components/SharedComponents/Listing.vue'
import DateFilter from './DateFilter.vue'
import PopupCard from '@/components/SharedComponents/PopupCard.vue'

export default {

  data () {
    return {
      filterActive: false,
      company: '',
      product: '',
      /* filterCompany: [],*/
      filteredTransactions: [],
      requests: [],
      componentKey: 0,
      payerNotEnoughBkr: false,
      payeeTooMuchBkr: false,
      max_limit: 0
    }
  },
  components: {
    Listing,
    DateFilter,
    PopupCard
  },
  methods: {
    mounted () {
      this.clearFilters()
    },
    clearFilters () {
      this.filterActive = false
    },
    handleDate () {
      const dateFilterEndDate = document.getElementById('end-date-filter' + '-date-filter')
      const dateFilterStartDate = document.getElementById('start-date-filter' + '-date-filter')
      console.log(dateFilterStartDate.value)
      if (dateFilterStartDate.value === '' || this.$refs.startDateInput.getInput() === null) {
        const minLimitDate = new Date()
        minLimitDate.setFullYear(2020)
        dateFilterEndDate.setAttribute('min', minLimitDate.toISOString().split('T')[0])
      } else {
        let startDateValue = new Date(dateFilterStartDate.value)
        startDateValue = startDateValue.setDate(startDateValue.getDate() + 1)
        const minLimitEndDate = new Date(startDateValue)
        dateFilterEndDate.setAttribute('min', minLimitEndDate.toISOString().split('T')[0])
      }
      if (dateFilterEndDate.value === '' || this.$refs.endDateInput.getInput() === null) {
        const maxLimitDate = new Date()
        dateFilterStartDate.setAttribute('max', maxLimitDate.toISOString().split('T')[0])
      } else {
        let endDateValue = new Date(dateFilterEndDate.value)
        endDateValue = endDateValue.setDate(endDateValue.getDate() + 1)
        const maxLimitStartDate = new Date(endDateValue)
        dateFilterStartDate.setAttribute('max', maxLimitStartDate.toISOString().split('T')[0])
      }
    },
    getListing (item) {
      for (const listing of this.$store.state.allArticles) {
        if (listing.id === item.metadata.id) {
          return listing
        }
      }
    },
    getListing_title (item) {
      for (const listing of this.$store.state.allArticles) {
        if (listing.id === item.metadata.id) {
          return listing.title
        }
      }
    },
    
    /*arrayUnique (array) {
      var a = array.concat()
      for (var i = 0; i < a.length; ++i) {
        for (var j = i + 1; j < a.length; ++j) {
          if (a[i] === a[j]) {
            a.splice(j--, 1)
          }
        }
      }

      return a
    },*/
    
    /* filer_helper (item, company, product) {
      if (item.payee.toLowerCase.includes(company.toLowerCase) || item.payer.toLowerCase.includes(company.toLowerCase)) {
        return true
      }
      if (this.getListing_title(item).toLowerCase.includes(product.toLowerCase)) {
        return true
      }
      return false
    },*/
    /*filteredTransactions () {
      const endDate = this.$refs.endDateInput.getInput()
      const filterCompany = this.$store.state.completedTransactions.filter((item) => item.toLowerCase().includes(this.company.value.toLowerCase()))
      const filterProduct = this.$store.state.completedTransactions.filter((item) => item.toLowerCase().includes(this.product.value.toLowerCase()))
      return this.arrayUnique(filterCompany.concat(filterProduct))
    },*/
    filterTransactions () {
      this.filteredTransactions = []
      const dateFilterEndDate = document.getElementById('end-date-filter' + '-date-filter')
      const dateFilterStartDate = document.getElementById('start-date-filter' + '-date-filter')
      let startDateValue = new Date(dateFilterStartDate.value)
      startDateValue = new Date(startDateValue.setDate(startDateValue.getDate()))
      startDateValue = startDateValue.setHours(0, 0, 0)
      let endDateValue = new Date(dateFilterEndDate.value)
      endDateValue = new Date(endDateValue.setDate(endDateValue.getDate()))
      endDateValue = endDateValue.setHours(23, 59, 59)
      //console.log(dateFilterEndDate.value + dateFilterStartDate.value + 'HALLÅ')
      // date range search
      if (this.$refs.startDateInput.getInput() != null && this.$refs.endDateInput.getInput() != null) {
        console.log('date range start and end')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => startDateValue.valueOf() <= new Date(item.metadata.time).valueOf() && new Date(item.metadata.time).valueOf() <= endDateValue.valueOf()) 
      } else if (this.$refs.endDateInput.getInput() != null) {
        console.log('date range end')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => new Date(item.metadata.time).valueOf() <= endDateValue.valueOf()) 
      } else if (this.$refs.startDateInput.getInput() != null) {
        console.log('date range start')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => startDateValue.valueOf() <= new Date(item.metadata.time).valueOf()) 
      }
      //company name search
      if (this.$refs.companyInput.value !== '' && (this.$refs.startDateInput.getInput() != null || this.$refs.endDateInput.getInput() != null)) {
        console.log('company search with date range')
        this.filteredTransactions = this.filteredTransactions.filter(item => item.payee.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()) || item.payer.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()))//check if whats written in company input exists in item title. 
      } else if (this.$refs.companyInput.value !== '') {
        console.log('company search without date range')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => item.payee.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()) || item.payer.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()))
      }
      //procuct name search
      console.log(this.$refs.productInput.value)
      if (this.$refs.productInput.value !== '' && (this.$refs.startDateInput.getInput() != null || this.$refs.endDateInput.getInput() != null || this.$refs.companyInput.value !== '')) {
        console.log('product search with date range')
        this.filteredTransactions = this.filteredTransactions.filter(item => this.getListing_title(item).toLowerCase().includes(this.$refs.productInput.value.toLowerCase()))//check if whats written in company input exists in item title. 
      } else if (this.$refs.productInput.value !== '') {
        console.log('product search without date range')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => this.getListing_title(item).toLowerCase().includes(this.$refs.productInput.value.toLowerCase()))
      }

      //if any filter is active filterActive is true. this 
      if (this.$refs.productInput.value !== '' || this.$refs.companyInput.value !== '' || this.$refs.startDateInput.getInput() != null || this.$refs.endDateInput.getInput() != null) {
        this.filterActive = true
      } else {
        this.filterActive = false
      } 
      console.log('found ' + this.filteredTransactions.length + ' elements')
      console.log(this.filterActive)
    },
    
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
    }
  }
}
</script>

<style scoped>
.filterObject {
  width: 125px;
  height: 30px;
  margin: 5px;
  align-content: center;
}
.DateFilter {
  width: 125px;
  height: 30px;
  display: inline-block;
}

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
  color: black;
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
  
}

</style>
