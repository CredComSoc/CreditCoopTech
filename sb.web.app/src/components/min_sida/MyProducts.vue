<template>
  <div>
    <h1><b> Aktiva Artiklar </b></h1>
    <div v-if="articles.length==0">
      <p style="font-style: italic;"> Du har inga aktiva artiklar just nu. </p>
    </div>
    <div v-if="articles.length!=0" style="max-height: 50em; overflow: scroll;">
      <table>
        <tr>
          <th></th>
          <th>Artikel</th>
          <th>Kategori</th>
          <th>Pris</th>
        </tr>
        <tr v-for="(item, index) in articles" :key="item">
          <td>{{index + 1 + '.'}} </td>
          <td><Listing className='article' :listingObj="item"/></td>
          <td>{{item.category}}</td>
          <td>{{item.price}}</td>
          <td> 
            <div class="edit">
              <!-- <router-link :to="{name:'New_Article', params:{artID: item.id}}"> Redigera annons </router-link> -->
            </div> 
          </td>
        </tr>
      </table>
    </div>
    <h1><b> Väntande Artiklar </b></h1>
    <p> Du har en artikel som väntar på att bli godkänd av admin. Artikeln kommer bli granskad inom 24h. </p>
 </div>
</template>

<script>
import { getArticles } from '../../serverFetch'
import Listing from '@/components/userstory4/Listing.vue'

export default {
  data () {
    return {
      articles: []
    }
  },
  mounted () {
    getArticles()
      .then(res => {
        this.articles = res.products
      })
  },
  components: {
    Listing
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

h1 {
  padding: 10px 0px 10px 20px;
  font-size: 1.6rem;
}

p {
  padding: 10px 0px 10px 20px;
  font-size: 1.2rem;
}

th {
  padding: 20px;
  font-weight: normal;
}

td {
  padding: 10px 0px 10px 0px;
}

.article {
  align-content: center;
  display: flex;
  justify-content: center;
}

</style>
