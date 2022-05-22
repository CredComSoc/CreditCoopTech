<template >
    <div >
      <h1><b> Bekräftade köp </b></h1>
      <p> Dina bekräftade köp. </p>
      <div style="max-height: 50em; overflow: scroll;">
      <table v-if="purchases">
        <tr>
          <th></th>
          <th>Företag</th>
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
          <td v-if="item.entries[0].metadata.id !== '0'"><Listing :listingId="getListing(item.entries[0])" /></td>
          <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="getListing(item.entries[0])" :comment="item.entries[0].description"/></td>
          <td>{{item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
          <td>{{item.entries[0].quant}}</td>
          <td className="green">{{item.state}}</td>
          <td><button className="red" @click="invoice('test.txt', item)">Ladda ner faktura</button></td>
        </tr>
      </table>
      </div>

      <h1> Väntande köp </h1>
      <div>
      <p> Du har väntande köp som ska godkännas av köparen innan köpet genomförs. Du kommer få en notis när köparen godkänt köpet. </p>
      </div>
      <div style="max-height: 50em; overflow: scroll;">
        <table>
          <tr>
            <th></th>
            <th>Företag</th>
            <th>Artikel</th>
            <th>Antal</th>
            <th>Pris</th>
            <th>Summa</th>
            <th>Status</th>
          </tr>
          <tr v-for="(item, index) in purchases.filter(purchase => purchase.state==='pending')" :key="item" ref="reqRefs">
            <td>{{index + 1 + '.'}}</td>
            <td>{{item.entries[0].payee}}</td>
            <td v-if="item.entries[0].metadata.id !== '0'"><Listing :listingId="getListing(item.entries[0])" /></td>
            <td v-if="item.entries[0].metadata.id === '0'"><Listing :listingId="getListing(item.entries[0])" :comment="item.entries[0].description"/></td>
            <td>{{item.entries[0].metadata.quantity}}</td>
            <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
            <td>{{item.entries[0].quant}}</td>
            <td id="buttons">
              <button @click="cancel(item.uuid, index)" style="background-color: red;"> Avbryt </button>
            </td>
          </tr>
        </table>
      </div>
    </div>
</template>

<script>
import { getPurchases, cancelRequest } from '../../serverFetch'
import Listing from '@/components/userstory4/Listing.vue'

export default {

  data () {
    return {
      purchases: [],
      componentKey: 0
    }
  },
  mounted () {
    getPurchases()
      .then(res => {
        console.log(res)
        this.purchases = res
      })
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
    getListing (item) {
      console.log(item.metadata.id)
      return item.metadata.id
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

</style>
