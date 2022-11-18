<!--Glöm inte att skydda /admineconomy länk-->

<template>
    <div class="wrapper">   
        <div>
            <h2 class="center-text">Ekonomi :-)</h2>
        </div>
        <button @click="getEconomy()">Hämta filtrerat urval</button>
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
export default {

  data () {
    return {
      filterActive: false, //used to check if any filter is applied.
      filteredTransactions: [] //all transactions that pass trough the applied filter will be stored in this array
    //requests: [],
    //componentKey: 0,
    //payerNotEnoughBkr: false,
    //payeeTooMuchBkr: false,
    //max_limit: 0,
    //default_min_date: 2020
    }
  },
  components: {
    Listing
  },
  methods: {
    async getEconomy () {
      const data = await fetchEconomy().then((res) => {
        if (res) {
          return res
        }
      })
      this.filteredTransactions = data
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
</style>
