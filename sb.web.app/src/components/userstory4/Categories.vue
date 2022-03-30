<template>
    <div className="categories">
      <div className="category">
        <h1>Typ av artikel</h1>
        <div className="label">
          <input type="checkbox" v-model="checkedProd" @click="$emit('filterEvent', checkedProd, 'article', 'product')" >
          <p>Produkter</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedServ" @click="$emit('filterEvent', checkedServ, 'article', 'service')" >
          <p>Tjänster</p>
        </div>
      </div>
      <div className="category">
        <h1>Plats</h1>
        <div className="label">
          <input type="checkbox" v-model="checkedNorr" @click="$emit('filterEvent', checkedNorr, 'destination', 'norrkoping')" >
          <p>Norrköping</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedSoder" @click="$emit('filterEvent', checkedSoder, 'destination', 'soderkoping')" >
          <p>Söderköping</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedLin" @click="$emit('filterEvent', checkedLin, 'destination', 'linkoping')" >
          <p>Linköping</p>
        </div>
      </div>
      <div className="category">
        <h1>Kategorier</h1>
        <div className="label">
          <input type="checkbox" v-model="checkedAff" @click="$emit('filterEvent', checkedAff, 'category', 'affarsutveckling&strategi')" >
          <p>Affärsutveckling & strategi</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedArb" @click="$emit('filterEvent', checkedArb, 'category', 'arbetsyta')" >
          <p>Arbetsyta</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedFot" @click="$emit('filterEvent', checkedFot, 'category', 'fotografering')" >
          <p>Fotografering</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedKok" @click="$emit('filterEvent', checkedKok, 'category', 'kok&restaurang')" >
          <p>Kök & restaurang</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedMar" @click="$emit('filterEvent', checkedMar, 'category', 'marknadsforing')" >
          <p>Marknadsföring</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedRen" @click="$emit('filterEvent', checkedRen, 'category', 'rengoring&stad')" > 
          <p>Rengöring & städ</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedSko" @click="$emit('filterEvent', checkedSko, 'category', 'skonhet')" >
          <p>Skönhet</p>
        </div>
        <div className="label">
          <input type="checkbox" v-model="checkedSom" @click="$emit('filterEvent', checkedSom, 'category', 'somnad&tyg')" >
          <p>Sömnad & tyg</p>
        </div>
      </div>
    </div>
</template>

<script>
export default {
  data () {
    return {
      message: ''
    }
  }
}
</script>
<style scoped>
  h1 {
    font-size: 18px;
  }

  input {
    margin-right:0.2rem;
    margin-top: 0.1rem
    /* margin:rem; */
  }

  .categories {
    box-shadow: 0px 4px 4px 0px #00000040;
    border: 3px solid #C4C4C4;
    height: 100%;
    margin: 1rem;
    padding: 1rem;
  }

  .label {
    display:flex;
    padding:0;
    line-height: 0.9;
  }
</style>
