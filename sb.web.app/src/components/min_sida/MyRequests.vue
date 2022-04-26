<template>
  <div style="max-height: 50em; overflow: scroll;">
    <table>
      <tr>
        <th></th>
        <th>Köpare</th>
        <th>Artikel</th>
        <th>Antal</th>
        <th>Pris</th>
        <th>Summa</th>
        <th>Status</th>
      </tr>
      <tr v-for="(item, index) in requests.filter(request => request.state==='pending')" :key="item" ref="reqRefs">
        <td>{{index + 1 + '.'}}</td>
        <td>{{item.entries[0].payer}}</td>
        <td><img src="../../assets/städning.png" alt="Generisk Bild"></td>
        <td>{{item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant}}</td>
        <td id="buttons">
          <button @click="cancel(item.uuid, item.entries[0].payer, index)" style="background-color: red;"> Avbryt </button>
          <button @click="accept(item.uuid, item.entries[0].payer, index)" style="background-color: green;"> Godkänn </button>
        </td>
      </tr>
      <tr v-for="(item, index) in requests.filter(request => request.state==='completed')" :key="item">
        <td>{{index + 1 + '.'}}</td>
        <td>{{item.entries[0].payer}}</td>
        <td><img src="../../assets/städning.png" alt="Generisk Bild"></td>
        <td>{{item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant / item.entries[0].metadata.quantity}}</td>
        <td>{{item.entries[0].quant}}</td>
        <td><p style="color: green;">GODKÄND</p></td>
      </tr>
    </table>
  </div>
</template>

<script>
import { getRequests, cancelRequest, acceptRequest, postNotification } from '../../serverFetch'

export default {

  data () {
    return {
      requests: []
    }
  },
  mounted () {
    getRequests()
      .then(res => {
        this.requests = res
      })
  },
  methods: {
    cancel (id, payer, index) {
      //const element = this.$refs.reqRefs[index]
      //element.parentNode.removeChild(element)
      this.statusSwap(index, 'cancel')
      cancelRequest(id)
      postNotification('saleRequestDenied', payer)
    },

    accept (id, payer, index) {
      this.statusSwap(index, 'accept')
      acceptRequest(id)
      postNotification('saleRequestAccepted', payer)
    },

    statusSwap (index, answer) {
      var tag = document.createElement('p')
      var text
      if (answer === 'cancel') {
        text = document.createTextNode('NEKAD')
        tag.style.color = 'red'
      } else {
        text = document.createTextNode('GODKÄND')
        tag.style.color = 'green'
      }
      tag.appendChild(text)
      const element = this.$refs.reqRefs[index]
      const child = element.lastElementChild
      var grandChild = child.lastElementChild
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

button {
  color: white;
  margin-right: 10px;
  border-radius: 5px;
  font-size: 1.2rem;
  padding: 2px 15px 2px 15px;
}

</style>
