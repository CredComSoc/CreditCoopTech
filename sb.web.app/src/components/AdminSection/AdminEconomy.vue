<!--Glöm inte att skydda /admineconomy länk-->

<template>
    <div class="wrapper">   
        <div>
            <h2 class="center-text">Ekonomi :-)</h2>
        </div>
          <div className='filter flexbox-item' style ="padding-top: 20px;padding-bottom: 0px; margin-left: 15px;">
          <button @click="getEconomy()">Filtrera</button><!--filter transactions handles all transcations. -->
          <DateFilter class= "DateFilter filterObject" ref="startDateInput" name="start-date-filter" :placeholder="`Från och med`" @click="handleDate()"/>
          <DateFilter class= "DateFilter filterObject" ref="endDateInput" name="end-date-filter" :placeholder="`Till och med`" @click="handleDate()"/>
          <input class="box-input filterObject" type="text" v-model="company" ref="companyInput" name="company-filter" placeholder="Företag" id="company-input">
          <input class="box-input filterObject" type="text" v-model="product" ref="productInput" name="product-filter" placeholder="Produkt" id="product-input">
          <input class="box-input filterObject" type="text" v-model="entries" ref="entriesInput" name="entries-filter" placeholder="Max antal rader" id="entries-input">
          <!--<button @click="downloadFilterView()">Ladda ner lista som CSV</button> --><!-- downloadFilterView handles the csv download. -->
        </div>
        <table v-if="(this.filterActive)">
        <tr>
          <th>Köpare</th>
          <th>Säljare</th>
          <th>Artikel</th>
          <th>Antal</th>
          <th>Pris</th>
          <th>Summa</th>
          <th>Tidstämpel</th>   
        </tr>
        <tr v-for="(item) in this.filteredTransactions" :key="item"><!--If the filter is not active, We get all transactions from the database.  -->
          <td>{{item.payer}}</td>
          <td>{{item.payee}}</td>
          <td v-if="item.metadata.id !== '0'">{{getListing_title(item)}}</td>
          <td v-if="item.metadata.id === '0'"><Listing :listingId="'0'" :comment="item.description"/></td>
          <td>{{item.metadata.quantity}}</td>
          <td>{{item.quant / item.metadata.quantity}}</td>
          <td>{{item.quant}}</td>
          <td>{{item.metadata.time.split('T')[0]}}</td>
          <th>{{item.written}}</th>
          <!--<td><button className="red" @click="invoice('test.txt', item)">Ladda ner faktura</button></td>-->
        </tr>
      </table>
    </div>
</template>
<script>
import Listing from '@/components/SharedComponents/Listing.vue'
import { fetchEconomy } from '@/serverFetch'
import DateFilter from '@/components/Profile/DateFilter.vue'
export default {

  data () {
    return {
      filterActive: false, //used to check if any filter is applied.
      filteredTransactions: [], //all transactions that pass trough the applied filter will be stored in this array
      entries: 10,
      //requests: [],
      //componentKey: 0,
      //payerNotEnoughBkr: false,
      //payeeTooMuchBkr: false,
      //max_limit: 0,
      default_min_date: 2020
    }
  },
  components: {
    Listing,
    DateFilter
  },
  methods: {
    handleDate () { //HandleDate Moderates what is possible to pick in the dropdown menue. 
      const dateFilterEndDate = document.getElementById('end-date-filter' + '-date-filter') //we get both date Filters by refering to their ID
      const dateFilterStartDate = document.getElementById('start-date-filter' + '-date-filter')
      if (dateFilterStartDate.value === '' || this.$refs.startDateInput.getInput() === null) { //if the Filter is cleared or not initialized
        const minLimitDate = new Date()
        minLimitDate.setFullYear(this.default_min_date, 0, 1)
        console.log(minLimitDate)
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
      const dateFilterEndDate = document.getElementById('end-date-filter' + '-date-filter')
      const dateFilterStartDate = document.getElementById('start-date-filter' + '-date-filter')
      let startDateValue = new Date(dateFilterStartDate.value)
      startDateValue = new Date(startDateValue.setDate(startDateValue.getDate()))
      startDateValue = startDateValue.setHours(0, 0, 0)
      let endDateValue = new Date(dateFilterEndDate.value)
      endDateValue = new Date(endDateValue.setDate(endDateValue.getDate()))
      endDateValue = endDateValue.setHours(23, 59, 59)
      //const searchParams = []

      const searchParams = new FormData()
      if (dateFilterEndDate.value === '') {
        endDateValue = new Date()
        //searchParams.push(endDateValue)
      }
      if (dateFilterStartDate.value === '') {
        startDateValue = new Date().setFullYear(2020, 0, 1)
        //searchParams.push(startDateValue)
      }
      searchParams.append('Filterdata', JSON.stringify({
        max_date: endDateValue,
        min_date: startDateValue,
        company_name: this.$refs.companyInput.value,
        product_name: this.$refs.companyInput.value,
        entries: this.$refs.entriesInput.value
      }))
      //searchParams.push(this.$refs.companyInput.value)
      //searchParams.push(this.$refs.productInput.value)
      //searchParams.push(this.$refs.entriesInput.value)

      const data = await fetchEconomy(searchParams).then((res) => {
        if (res) {
          return res
        }
      })
      this.filteredTransactions = data
      console.log(this.filteredTransactions[0])
      //console.log(this.filteredTransactions[0])
      this.filterActive = true
    },
    getListing_title (item) { //get name of article from vuex store
      for (const listing of this.$store.state.allArticles) {
        if (listing.id === item.metadata.id) {
          return listing.title
        }
      }
    }
  }
}

</script>
<style scoped>
button {
  color: black;
  margin-right: 10px;
  border-radius: 5px;
  font-size: 1.2rem;
  padding: 2px 15px 2px 15px;
}
.DateFilter {
  width: 125px;
  height: 30px;
  display: inline-block;
}
</style>
