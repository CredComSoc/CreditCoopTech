<!--Glöm inte att skydda /admineconomy länk-->

<template>
    <div class="wrapper">   
        <div style="display:flex; justify-content:center;">
            <h2 class="center-text">{{ $t('user.financialOverview') }}</h2>
        </div>
        <div class="EconomyStats">
          <div><!--Displays the total number of trascations and their turnover this month-->
            <b>Dagens</b>
            <p>{{ $t('number_of_transactions') }}: {{this.numberOfTradesDay}}<br>
            Omsättning: {{this.turnOverDay}}</p>
          </div>
          <div>
            <b>Veckans</b>
            <p>{{ $t('number_of_transactions') }}: {{this.numberOfTradesWeek}}<br>
            Omsättning: {{this.turnOverWeek}}</p>
          </div>
          <div>
            <b>Månadens</b>
            <p>{{ $t('number_of_transactions') }}: {{this.numberOfTradesMonth}}<br>
            Omsättning: {{this.turnOverMonth}}</p>
          </div>
          <div>
            <b>Årets</b>
            <p>{{ $t('number_of_transactions') }}: {{this.numberOfTradesYear}}<br>
            Omsättning: {{this.turnOverYear}}</p>
          </div>
        </div>
        <!--This is a similar solution to the filter from profile->MyPurchases -->
        <div className='filter'>
          <button @click="filterTransactions()">Filtrera</button><!--filter transactions handles all transcations. -->
          <DateFilter class= "DateFilter filterObject" ref="startDateInput" name="start-date-filter" :placeholder="`Från och med`" @click="handleDate()"/>
          <DateFilter class= "DateFilter filterObject" ref="endDateInput" name="end-date-filter" :placeholder="`Till och med`" @click="handleDate()"/>
          <input class="box-input filterObject" type="text" ref="companyInput" name="company-filter" placeholder="{{ $t('business') }}" id="company-input">
          <input class="box-input filterObject" type="text" ref="productInput" name="product-filter" :placeholder="$('product')" id="product-input">
          <!--<input class="box-input filterObject" type="text" v-model="entries" ref="entriesInput" name="entries-filter" placeholder="Max antal rader" id="entries-input">-->
          <button @click="downloadFilterView()">Ladda ner lista som CSV</button><!-- downloadFilterView handles the csv download. -->
        </div>
        <table v-if="(this.filterActive)"> <!--We dont display anything unless anyone has clicked the filter button-->
        <tr>
          <th>{{ $t('Buyer') }}</th>
          <th>{{ $t('Salesperson') }}</th>
          <th>{{ $t('article') }}</th>
          <th>{{ $t('quantity') }}</th>
          <th>{{ $t('price') }}</th>
          <th>{{ $t('amount') }}</th>
          <th>{{ $t('timestamp') }}</th>   
        </tr>
        <tr v-for="(item) in this.filteredTransactions" :key="item"><!--We get all transactions from the database. and display desired values-->
          <td>{{item.entries[0].payer}}</td>
          <td>{{item.entries[0].payee}}</td>
          <td v-if="item.entries[0].metadata.id !== '0'">{{getListing_title(item.entries[0])}}</td>
          <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="'0'" :comment="item.entries[0].description"/></td>
          <td>{{item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant}}</td>
          <!--<td>{{item.written.split('T')[0]}}</td>-->
          <th>{{item.written}}</th>
          <!--<td><button className="red" @click="invoice('test.txt', item)">Ladda ner faktura</button></td>-->
        </tr>
      </table>
    </div>
    <div class="chat">
      <p style="text-align: center;">chat goes here</p>
    </div>
</template>
<script>
import Listing from '@/components/SharedComponents/Listing.vue'
import { fetchEconomy } from '@/serverFetch.js'
import DateFilter from '@/components/Profile/DateFilter.vue'
import { TO_DISPLAY_STRING } from '@vue/compiler-core'

export default {
  mounted  () {
    this.getEconomy()
  },
  data () {
    return {
      filterActive: false, //used to check if any filter is applied.
      filteredTransactions: [], //all transactions that pass trough the applied filter will be stored in this array
      allTransactions: [],
      default_min_date: 2020,
      turnOverDay: 0, //turnover day,week,month,year
      turnOverWeek: 0, 
      turnOverMonth: 0,
      turnOverYear: 0,
      numberOfTradesDay: 0, //number of trades day,week,month,year
      numberOfTradesWeek: 0,
      numberOfTradesMonth: 0,
      numberOfTradesYear: 0

    }
  },
  components: {
    Listing,
    DateFilter
  },
  methods: {
    calculateSTATS () { // calculate turnover 
      let todaynight = new Date()
      let todayday = new Date()
      let lastWeek = new Date()
      let lastMonth = new Date()
      let lastYear = new Date()
      
      todaynight = todaynight.setHours(23, 59, 59)
      todaynight = new Date(todaynight)

      todayday = todayday.setHours(0, 0, 1)
      todayday = new Date(todayday)

      lastWeek = lastWeek.setDate(todayday.getDate() - 7)
      lastWeek = new Date(lastWeek)
      lastWeek = lastWeek.setHours(0, 0, 1)
      lastWeek = new Date(lastWeek)

      lastMonth = lastMonth.setMonth(todayday.getMonth() - 1)
      lastMonth = new Date(lastMonth)
      lastMonth = lastMonth.setHours(0, 0, 1)
      lastMonth = new Date(lastMonth)
      
      lastYear = lastYear.setFullYear(todayday.getFullYear() - 1)
      lastYear = new Date(lastYear)
      lastYear = lastYear.setHours(0, 0, 1)
      lastYear = new Date(lastYear)

      let transactionsDay = []
      let transactionsWeek = []
      let transactionsMonth = []
      let transactionsYear = []
      //filter out transactions based on date from all transactions and fills arrays
      console.log(this.allTransactions)
      transactionsDay = this.allTransactions.filter(item => todayday.valueOf() <= new Date(item.written).valueOf() && new Date(item.written).valueOf() <= todaynight.valueOf())
      transactionsWeek = this.allTransactions.filter(item => lastWeek.valueOf() <= new Date(item.written).valueOf() && new Date(item.written).valueOf() <= todaynight.valueOf())
      transactionsMonth = this.allTransactions.filter(item => lastMonth.valueOf() <= new Date(item.written).valueOf() && new Date(item.written).valueOf() <= todaynight.valueOf())
      transactionsYear = this.allTransactions.filter(item => lastYear.valueOf() <= new Date(item.written).valueOf() && new Date(item.written).valueOf() <= todaynight.valueOf())
      //adds everything in all the arrays together. 
      for (const entry of transactionsDay) {
        this.turnOverDay += entry.entries[0].quant
        this.numberOfTradesDay += 1
      }
      for (const entry of transactionsWeek) {
        this.turnOverWeek += entry.entries[0].quant
        this.numberOfTradesWeek += 1
      }
      for (const entry of transactionsMonth) {
        this.turnOverMonth += entry.entries[0].quant
        this.numberOfTradesMonth += 1
      }
      for (const entry of transactionsYear) {
        this.turnOverYear += entry.entries[0].quant
        this.numberOfTradesYear += 1
      }
      /*
      console.log('TO Day' + this.turnOverDay)
      console.log('TO Week' + this.turnOverWeek)
      console.log('TO Month' + this.turnOverMonth)
      console.log('TO Year' + this.turnOverYear)
      console.log('NO Trades Day' + this.numberOfTradesDay)
      console.log('NO Trades Week' + this.numberOfTradesWeek)
      console.log('NO Trades Month' + this.numberOfTradesMonth)
      console.log('NO Trades Year' + this.numberOfTradesYear)
      */
    },
    handleDate () { //HandleDate Moderates what is possible to pick in the dropdown menue fot the date filters. Should be able to just inport this function from profile/mypurchases but didnt get it to work.
      const dateFilterEndDate = document.getElementById('end-date-filter' + '-date-filter') //we get both date Filters by refering to their ID
      const dateFilterStartDate = document.getElementById('start-date-filter' + '-date-filter')
      if (dateFilterStartDate.value === '' || this.$refs.startDateInput.getInput() === null) { //if the Filter is cleared or not initialized
        const minLimitDate = new Date()
        minLimitDate.setFullYear(this.default_min_date, 0, 1)
        //console.log(minLimitDate)
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

    async getEconomy () {
      //below comments were an attempt to remove the need to fetch all transactions from the backend. we would then try to only fetch the needed results given the search params from the filter.
      
      /*
      const dateFilterEndDate = document.getElementById('end-date-filter' + '-date-filter')
      const dateFilterStartDate = document.getElementById('start-date-filter' + '-date-filter')
      let startDateValue = new Date(dateFilterStartDate.value)
      startDateValue = new Date(startDateValue.setDate(startDateValue.getDate()))
      startDateValue = startDateValue.setHours(0, 0, 0)
      let endDateValue = new Date(dateFilterEndDate.value)
      endDateValue = new Date(endDateValue.setDate(endDateValue.getDate()))
      endDateValue = endDateValue.setHours(23, 59, 59)
      */
      //const searchParams = []
      /*
      if (dateFilterEndDate.value === '') {
        endDateValue = new Date()
        //searchParams.push(endDateValue)
      }
      if (dateFilterStartDate.value === '') {
        startDateValue = new Date()
        startDateValue.setFullYear(2020, 0, 1)
        //searchParams.push(startDateValue)
      }*/
      /*
      const searchParams = {
        max_date: endDateValue,
        min_date: startDateValue,
        company_name: this.$refs.companyInput.value,
        product_name: this.$refs.productInput.value,
        entries: this.$refs.entriesInput.value
      } 
      const data = await fetchEconomy(endDateValue, startDateValue, this.$refs.companyInput.value, this.$refs.productInput.value, this.$refs.entriesInput.value).then((res) => {
        if (res) {
          return res
        }
      })*/ 

      //get all transactions from backend
      this.allTransactions = await fetchEconomy()
      this.calculateSTATS()
      this.filterActive = true
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
        this.filteredTransactions = this.allTransactions.filter(item => startDateValue.valueOf() <= new Date(item.written).valueOf() && new Date(item.written).valueOf() <= endDateValue.valueOf()) 
      } else if (dateFilterEndDate.value !== '') {  
        //console.log('date range end')
        this.filteredTransactions = this.allTransactions.filter(item => new Date(item.written).valueOf() <= endDateValue.valueOf()) 
      } else if (dateFilterStartDate.value !== '') { 
        //console.log('date range start')
        this.filteredTransactions = this.allTransactions.filter(item => startDateValue.valueOf() <= new Date(item.written).valueOf()) 
      }
      //company name search
      if (this.$refs.companyInput.value !== '' && (dateFilterStartDate.value !== '' || dateFilterEndDate.value !== '')) { //if we have used a filter before, filter for company in filtered transactions and save in filteredTransactions
        //console.log('company search with date range')
        this.filteredTransactions = this.filteredTransactions.filter(item => item.entries[0].payee.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()) || item.entries[0].payer.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase())) //check if whats written in company input exists in item title. 
      } else if (this.$refs.companyInput.value !== '') { // else filter in vuex store completedTransactions and save in filteredTransactions
        //console.log('company search without date range')
        this.filteredTransactions = this.allTransactions.filter(item => item.entries[0].payee.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()) || item.entries[0].payer.toLowerCase().includes(this.$refs.companyInput.value.toLowerCase()))
      }
      //procuct name search

      if (this.$refs.productInput.value !== '' && (this.$refs.companyInput.value !== '' || dateFilterStartDate.value !== '' || dateFilterEndDate.value !== '')) { //same logic as above.
        //console.log('product search with date range and/or with company')
        this.filteredTransactions = this.filteredTransactions.filter(item => this.getListing_title(item.entries[0]).toLowerCase().includes(this.$refs.productInput.value.toLowerCase())) //check if whats written in product input exists in item title. 
      } else if (this.$refs.productInput.value !== '') {
        //console.log('product search without date range and company')
        this.filteredTransactions = this.allTransactions.filter(item => this.getListing_title(item.entries[0]).toLowerCase().includes(this.$refs.productInput.value.toLowerCase()))
      }
      if (!(this.$refs.productInput.value !== '' || this.$refs.companyInput.value !== '' || dateFilterEndDate.value !== '' || dateFilterStartDate.value !== '')) { 
        this.filteredTransactions = this.allTransactions
      }

      //console.log('found ' + this.allTransactions.length + ' elements')
    },

    getListing_title (item) { //get name of article from vuex store
      for (const listing of this.$store.state.allArticles) {
        if (listing.id === item.metadata.id) {
          return listing.title
        }
      }
    },
    downloadFilterView () { // takes the data, formats it into CSV form and downloads the csv as a file
      var csv = 'Buyer;Seller;Title;Amount;Price;Sum;Timestamp\n' 
      
      this.filteredTransactions.forEach((item) => { //takes from filtered transactions if filter is active
        csv += item.entries[0].payer + ';' 
        csv += item.entries[0].payee + ';' 
        csv += this.getListing_title(item.entries[0]) + ';' 
        csv += item.entries[0].metadata.quantity + ';' 
        csv += (item.entries[0].quant / item.entries[0].metadata.quantity) + ';' 
        csv += item.entries[0].quant + ';'  
        csv += item.written.split('T')[0] 
        //csv += item.written.join(','); 
        csv += '\n' 
      })
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
    }
  }
}

</script>
<style scoped>
.chat {
  width: 25%;
  height: 700px;
  display: inline-block;
  position: fixed;
}
.wrapper{
  width: 75%;
  float: left;
  border-right: 2px solid;
}
button {
  color: black;
  margin-right: 10px;
  border-radius: 5px;
  font-size: 1.2rem;
  padding: 2px 15px 2px 15px;
  margin-left: 2.5%;
}
.DateFilter {
  width: 125px;
  height: 30px;
  display: inline-block;
}
.EconomyStats {
  /*
  display:block;
  margin:auto;
  align-items: center;
  width: 70%;
  */
  display: flex;
  justify-content: center;
}
.EconomyStats div {
border: 3px solid;
border-radius: 10px;
text-align: center;
margin:20px;
padding: 20px;
padding-bottom: 10px;;
/*width: 20%;*/
/*display: inline-block;

margin-left:2.5%;
margin-right:2%;
*/
}
.filter {
  /*
  padding-top: 5px;
  padding-bottom: 5px; 
  margin-left:17%;
  margin-right: 17%;
  width: auto;
  */
  display:flex;
  justify-content: center;
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
.filterObject {
  height: 30px;
  margin: 5px;
  align-content: center;
  margin-left:2.5%;
}
.filterObject {
  width: 125px;
  height: 30px;
  margin: 5px;
  align-content: center;
  margin-left:2%;
}

table {
  margin-left: auto;
  margin-right: auto;
  border-spacing:50px;
  width: 100%;
  text-align: center;
  font-size: 1.2rem;
}
tr
{
  border-bottom: 2px solid;
}

th {
  padding: 20px;
  font-weight: normal;
}

td {
  padding: 10px 0px 10px 0px;
}

</style>
