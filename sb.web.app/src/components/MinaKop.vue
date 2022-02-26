<template>
    <div>
      <h1> Bekräftade köp </h1>
      <h2> Dina bekräftade köp. </h2>
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
        <tr v-for="(item, index) in purchases" :key="item" :index="index">
          <td>{{index + '.'}}</td>
          <td>{{item[0]}}</td>
          <td><img src="städning.png" alt="Städservice AB"></td>
          <td>{{item[2]}}</td>
          <td>{{item[3]}}</td>
          <td>{{item[4]}}</td>
          <td>{{item[5]}}</td>
        </tr>
      </table>
    </div>
        <div>
      <h1> Väntande köp </h1>
      <h2> Du har ett väntande köp som ska godkännas av köparen innan köpet genomförs. Du kommer få en notis när köparen godkänt köpet. </h2>
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
          <td>VÄNTANDE</td>
        </tr>
      </table>
    </div>
</template>

<script>
import { minSida } from '../serverFetch'

export default {

  data () {
    const purchases = minSida('TestUser', '123')
    console.log(purchases)
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
}

</style>
