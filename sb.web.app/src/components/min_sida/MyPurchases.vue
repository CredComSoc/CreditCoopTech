<template >
    <div >
      <h1><b> Bekräftade köp </b></h1>
      <p> Dina bekräftade köp. </p>
      <div style="max-height: 50em; overflow: scroll;">
      <table>
        <tr>
          <th></th>
          <th>Säljare</th>
          <th>Artikel</th>
          <th>Antal</th>
          <th>Pris</th>
          <th>Summa</th>
          <th>Status</th>
          <th>Faktura</th>
        </tr>
        <tr v-for="(item, index) in purchases.filter(purchase => purchase.state==='completed')" :key="item">
          <td>{{index + 1 + '.'}}</td>
          <td>{{item.entries[0].payee}}</td>
          <td><img src="../../assets/städning.png" alt="Generisk Bild"></td>
          <td>{{'1'}}</td>
          <td>{{item.entries[0].quant}}</td>
          <td>{{item.entries[0].quant}}</td>
          <td className="green">{{item.state}}</td>
          <td><a href="" className="red">Ladda ner faktura</a></td>
        </tr>
      </table>
      </div>

      <h1> Väntande köp </h1>
      <p> Du har ett väntande köp som ska godkännas av köparen innan köpet genomförs. Du kommer få en notis när köparen godkänt köpet. </p>
      <div style="max-height: 50em; overflow: scroll;">
        <table>
          <tr>
            <th></th>
            <th>Säljare</th>
            <th>Artikel</th>
            <th>Antal</th>
            <th>Pris</th>
            <th>Summa</th>
            <th>Status</th>
          </tr>
          <tr v-for="(item, index) in purchases.filter(purchase => purchase.state==='validated')" :key="item">
            <td>{{index + 1 + '.'}}</td>
            <td>{{item.entries[0].payee}}</td>
            <td><img src="../../assets/städning.png" alt="Städservice AB"></td>
            <td>{{'1'}}</td>
            <td>{{item.entries[0].quant}}</td>
            <td>{{item.entries[0].quant}}</td>
            <td style="color: silver;">{{item.state}}</td>
          </tr>
        </table>
      </div>
    </div>
</template>

<script>
import { getTransactions } from '../../serverFetch'

export default {

  data () {
    return {
      purchases: []
    }
  },
  mounted () {
    getTransactions()
      .then(res => {
        this.purchases = res
      })
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

.green {
  color:green;
}

.red {
  color: red;
}

</style>
