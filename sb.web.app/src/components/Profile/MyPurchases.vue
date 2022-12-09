<template>
  <div>
    <!--PAYEE AND PAYER NEEDS TO BE ADJUSTED SO ITS RIGHT-->
    <!--Gets all incomming requests from the Vuex store and displays them to the user. -->
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
        </table>
        <div v-if="!requests">
          <h4> Du har inte fått några köpförfrågningar än. </h4>
        </div>
        <PopupCard v-if="this.payeeTooMuchBkr" @closePopup="this.closePopup" title="Förbjuden förfrågan" btnLink="" btnText="Ok" :cardText="`Köpförfrågan kan inte godkännas, din övre gräns är ` + this.max_limit + ' bKr.'" />
        <PopupCard v-if="this.payerNotEnoughBkr" @closePopup="this.closePopup" title="Förbjuden förfrågan" btnLink="" btnText="Ok" :cardText="`Köpförfrågan kan inte godkännas, köparen har inte tillräckligt med bKr.`" />
      </div>
    </div>
    <!--Gets all Pending purchases from the VueX store. -->
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
            <!--<th>Status</th>-->
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
      <!--Filter buttons and fields-->
      <div className='filter flexbox-item' style ="padding-top: 20px;padding-bottom: 0px; margin-left: 15px;">
        <button @click="filterTransactions()">Filtrera</button><!--filter transactions handles all transcations. -->
        <DateFilter class= "DateFilter filterObject" ref="startDateInput" name="start-date-filter" :placeholder="`Från och med`" @click="handleDate()"/>
        <DateFilter class= "DateFilter filterObject" ref="endDateInput" name="end-date-filter" :placeholder="`Till och med`" @click="handleDate()"/>
        <input class="box-input filterObject" type="text" ref="companyInput" name="company-filter" placeholder="Företag" id="company-input">
        <input class="box-input filterObject" type="text" ref="productInput" name="product-filter" placeholder="Produkt" id="product-input">
        <button @click="downloadFilterView()">Ladda ner lista som CSV</button> <!-- downloadFilterView handles the csv download. -->
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
        </tr>
        <tr v-for="(item) in this.$store.state.completedTransactions" :key="item"><!--If the filter is not active, We get all completed transaction from the VueX store.  -->
          <td>{{item.entries[0].payer}}</td>
          <td>{{item.entries[0].payee}}</td>
          <td v-if="item.entries[0].metadata.id !== '0'">{{getListing_title(item.entries[0])}}</td>
          <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="'0'" :comment="item.entries[0].description"/></td>
          <td>{{item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant}}</td>
          <th>{{item.written}}</th>
          <!--<td><button className="red" @click="invoice('test.txt', item)">Ladda ner faktura</button></td>-->
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
          <!--<th>Faktura</th>-->   
        </tr>
        <tr v-for="(item) in this.filteredTransactions" :key="item"> <!--If the filter is active, We get view all transactions from filtered transactions found below.  -->
          <td>{{item.entries[0].payer}}</td>
          <td>{{item.entries[0].payee}}</td>
          <td v-if="item.entries[0].metadata.id !== '0'">{{getListing_title(item.entries[0])}}</td>
          <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="'0'" :comment="item.entries[0].description"/></td>
          <td>{{item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant}}</td>
          <th>{{item.written}}</th>
          <!--<td><button className="red" @click="invoice('test.txt', item)">Ladda ner faktura</button></td>-->
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
      filterActive: false, //used to check if any filter is applied.
      filteredTransactions: [], //all transactions that pass trough the applied filter will be stored in this array
      //requests: [],
      //componentKey: 0,
      payerNotEnoughBkr: false,
      payeeTooMuchBkr: false,
      max_limit: 0,
      default_min_date: 2020
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
    handleDate () { //HandleDate Moderates what is possible to pick in the dropdown menue. 
      const dateFilterEndDate = document.getElementById('end-date-filter' + '-date-filter') //we get both date Filters by refering to their ID
      const dateFilterStartDate = document.getElementById('start-date-filter' + '-date-filter')
      if (dateFilterStartDate.value === '' || this.$refs.startDateInput.getInput() === null) { //if the Filter is cleared or not initialized
        const minLimitDate = new Date()
        minLimitDate.setFullYear(this.default_min_date, 0, 1)
        dateFilterEndDate.setAttribute('min', minLimitDate.toISOString().split('T')[0]) //we set the date minimum date to 2020-01-01
      } else {
        let startDateValue = new Date(dateFilterStartDate.value) //Otherwise take the value just set by the user
        startDateValue = startDateValue.setDate(startDateValue.getDate() + 1) //add 1 day for it to be correct
        const minLimitEndDate = new Date(startDateValue)
        dateFilterEndDate.setAttribute('min', minLimitEndDate.toISOString().split('T')[0]) // and set min date to that value
      }
      if (dateFilterEndDate.value === '' || this.$refs.endDateInput.getInput() === null) { //see comments above.
        const maxLimitDate = new Date()
        dateFilterStartDate.setAttribute('max', maxLimitDate.toISOString().split('T')[0])
      } else {
        let endDateValue = new Date(dateFilterEndDate.value)
        endDateValue = endDateValue.setDate(endDateValue.getDate() + 1)
        const maxLimitStartDate = new Date(endDateValue)
        dateFilterStartDate.setAttribute('max', maxLimitStartDate.toISOString().split('T')[0])
      }
    },
    getListing (item) { //gets a specific articleobject from allArticles in the vuex store depending on a given id. 
      for (const listing of this.$store.state.allArticles) {
        if (listing.id === item.metadata.id) {
          return listing
        }
      }
    },
    getListing_title (item) { //same as abobe but only the name
      for (const listing of this.$store.state.allArticles) {
        if (listing.id === item.metadata.id) {
          return listing.title
        }
      }
    },
    downloadFilterView () { // takes the data, formats it into CSV form and downloads the csv as a file
      var csv = 'Buyer;Seller;Title;Amount;Price;Sum;Timestamp\n' 
      if (this.filterActive) {  
        this.filteredTransactions.forEach((item) => { //takes from filtered transactions if filter is active
          csv += item.entries[0].payer + ';' 
          csv += item.entries[0].payee + ';' 
          csv += this.getListing_title(item) + ';' 
          csv += item.entries[0].metadata.quantity + ';' 
          csv += (item.entries[0].quant / item.entries[0].metadata.quantity) + ';' 
          csv += item.entries[0].quant + ';'  
          csv += item.written.split('T')[0] 
          //csv += item.written.join(','); 
          csv += '\n' 
        })
      } else {
        this.$store.state.completedTransactions.forEach ((item) => { //if filter is not active, take the display the transactions from Completed transactions in the vuex store
          csv += item.entries[0].payer + ';' 
          csv += item.entries[0].payee + ';' 
          csv += this.getListing_title(item) + ';' 
          csv += item.entries[0].metadata.quantity + ';' 
          csv += (item.entries[0].quant / item.entries[0].metadata.quantity) + ';' 
          csv += item.entries[0].quant + ';'  
          csv += item.written.split('T')[0] 
          //csv += item.written.join(','); 
          csv += '\n' 
        })
      } 
      //Code for the download of the csv
      var hiddenElement = document.createElement('a') 
      hiddenElement.href = 'data:text/csv;charset=UTF-8,' + encodeURI(csv)//creates an uri of the csv
      hiddenElement.target = '_blank'  
      
      //provide the name for the CSV file to be downloaded. If both namefilter are active it will add from and to dates to the name dates. If only one name is active it will set the other one to today or predetermined default_min_date
      const dateFilterEndDate = document.getElementById('end-date-filter' + '-date-filter')
      const dateFilterStartDate = document.getElementById('start-date-filter' + '-date-filter')
      let tmp = new Date(dateFilterEndDate.value)
      tmp = tmp.setDate(tmp.getDate() + 1)
      const endDateValue = new Date(tmp)
      tmp = new Date(dateFilterStartDate.value)
      tmp = tmp.setDate(tmp.getDate() + 1)
      const startDateValue = new Date(tmp)
      if (dateFilterEndDate.value !== '' && dateFilterStartDate.value !== '') { //nothing is null print both 
        hiddenElement.download = 'Filtered_Transactions' + startDateValue.toISOString().split('T')[0] + '-' + endDateValue.toISOString().split('T')[0] + '.csv'  
      } else if (dateFilterEndDate.value === '' && dateFilterStartDate.value !== '') { //end date is null print start and today
        console.log(dateFilterEndDate.value === '')
        console.log(this.$refs.endDateInput.getInput() === null)
        tmp = new Date()
        hiddenElement.download = 'Filtered_Transactions' + startDateValue.toISOString().split('T')[0] + '-' + tmp.toISOString().split('T')[0] + '.csv'
      } else if (dateFilterStartDate.value === '' && dateFilterEndDate.value !== '') { //start date is null print default start and end
        tmp = new Date()
        tmp.setFullYear(this.default_min_date, 0, 1)
        hiddenElement.download = 'Filtered_Transactions' + tmp.toISOString().split('T')[0] + '-' + endDateValue.toISOString().split('T')[0] + '.csv'
      } else { //both null print default to today
        const tmpstart = new Date()
        console.log(tmpstart)
        tmpstart.setFullYear(this.default_min_date, 0, 1)
        console.log(tmpstart)
        const tmpend = new Date()
        hiddenElement.download = 'Filtered_Transactions' + tmpstart.toISOString().split('T')[0] + '-' + tmpend.toISOString().split('T')[0] + '.csv'
      }
      hiddenElement.click()  
    },
    filterTransactions () { //handles the filter
      this.filteredTransactions = []
      const dateFilterEndDate = document.getElementById('end-date-filter' + '-date-filter')
      const dateFilterStartDate = document.getElementById('start-date-filter' + '-date-filter')
      let startDateValue = new Date(dateFilterStartDate.value)
      startDateValue = new Date(startDateValue.setDate(startDateValue.getDate()))
      startDateValue = startDateValue.setHours(0, 0, 0)
      let endDateValue = new Date(dateFilterEndDate.value)
      endDateValue = new Date(endDateValue.setDate(endDateValue.getDate()))
      endDateValue = endDateValue.setHours(23, 59, 59)

      // date range search
      if (dateFilterEndDate.value !== '' && dateFilterStartDate.value !== '') { //if we have daterange filter for it. Save result in Filtered Transactions
        //console.log('date range start and end')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => startDateValue.valueOf() <= new Date(item.written).valueOf() && new Date(item.written).valueOf() <= endDateValue.valueOf()) 
      } else if (dateFilterEndDate.value !== '') {  
        //console.log('date range end')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => new Date(item.written).valueOf() <= endDateValue.valueOf()) 
      } else if (dateFilterStartDate.value !== '') { 
        //console.log('date range start')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => startDateValue.valueOf() <= new Date(item.written).valueOf()) 
      }
      //company name search
      if (this.$refs.companyInput.value !== '' && (dateFilterStartDate.value !== '' || dateFilterEndDate.value !== '')) { //if we have used a filter before, filter for company in filtered transactions and save in filteredTransactions
        //console.log('company search with date range')
        this.filteredTransactions = this.filteredTransactions.filter(item => item.entries[0].payee.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()) || item.entries[0].payer.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase())) //check if whats written in company input exists in item title. 
      } else if (this.$refs.companyInput.value !== '') { // else filter in vuex store completedTransactions and save in filteredTransactions
        //console.log('company search without date range')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => item.entries[0].payee.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()) || item.entries[0].payer.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()))
      }
      //procuct name search

      if (this.$refs.productInput.value !== '' && (this.$refs.companyInput.value !== '' || dateFilterStartDate.value !== '' || dateFilterEndDate.value !== '')) { //same logic as above.
        //console.log('product search with date range and/or with company')
        this.filteredTransactions = this.filteredTransactions.filter(item => this.getListing_title(item).toLowerCase().includes(this.$refs.productInput.value.toLowerCase())) //check if whats written in product input exists in item title. 
      } else if (this.$refs.productInput.value !== '') {
        //console.log('product search without date range and company')
        this.filteredTransactions = this.$store.state.completedTransactions.filter(item => this.getListing_title(item).toLowerCase().includes(this.$refs.productInput.value.toLowerCase()))
      }

      //if any filter is active filterActive is true.
      if (this.$refs.productInput.value !== '' || this.$refs.companyInput.value !== '' || dateFilterEndDate.value !== '' || dateFilterStartDate.value !== '') { 
        this.filterActive = true
      } else {
        this.filterActive = false
      } 
      //console.log('found ' + this.filteredTransactions.length + ' elements')
    },
    
    invoice (filename, item) { // not used atm. used for generating invoices
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
    cancel (id, index) { //cancel order button
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
