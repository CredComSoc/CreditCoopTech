<template>
    <div>
      <h1><b> Bekräftade köp </b></h1>
      <p> Dina bekräftade köp. </p>
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
        <tr v-for="(item, index) in purchases" :key="item" :index="index">
          <td>{{index + 1 + '.'}}</td>
          <td>{{item.entries[0].payee}}</td>
          <td><img src="städning.png" alt="Generisk Bild"></td>
          <td>{{'1'}}</td>
          <td>{{item.entries[0].quant}}</td>
          <td>{{item.entries[0].quant}}</td>
          <td className="green">{{item.state}}</td>
          <td  className="red">Ladda ner faktura</td>
        </tr>
      </table>
    </div>
        <div>
      <h1> Väntande köp </h1>
      <p> Du har ett väntande köp som ska godkännas av köparen innan köpet genomförs. Du kommer få en notis när köparen godkänt köpet. </p>
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
        <tr>
          <td>1.</td>
          <td>Städservice AB</td>
          <td><img src="städning.png" alt="Städservice AB"></td>
          <td>1</td>
          <td>750</td>
          <td>750</td>
          <td style="color: silver;">VÄNTANDE</td>
        </tr>
      </table>
    </div>
</template>

<script>
import { myTransactions, transaction } from '../serverFetch'

export default {

  data () {
    const purchases = []
    myTransactions('TestAdmin', '123')
      .then(res => {
        Object.keys(res).forEach(function (key) {
          // console.log(res[key])
          transaction('TestAdmin', '123', res[key])
            .then(res => {
              purchases.push(res)
            })
        })
        console.log(purchases)
      })

    return {
      purchases: purchases
      // purchases: [['Städservice AB', 'bild', '1', '500', '500', 'BEKRÄFTAD', 'Ladda ner faktura'], ['Flytt AB', 'bild', '1', '5700', '5700', 'BEKRÄFTAD', 'Ladda ner faktura'], ['Snickeri AB', 'bild', '3', '500', '1500', 'BEKRÄFTAD', 'Ladda ner faktura']]
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

.green {
  color:green;
}

.red {
  color: red;
}

</style>
