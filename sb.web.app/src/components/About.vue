<template>
  <div>
    <h1><b> Skapad av </b></h1>
    <div>
      <p style="font-style: italic; width: 950px;"> Sidan skapades ursprunglingen för projektkursen TDP032: Agile System Development på Linköpings Universitet, av följande studenter:  </p>
    </div>
    <div style="max-height: 50em;">
      <table>
        <tr>
        </tr>
        <tr v-for="item in creators" :key="item">
          <td> {{item}} </td>

        </tr>
      </table>
    </div>

 </div>
</template>

<script>

export default {
  data () {
    return {
      creators: ['Vincent Ahlström', 'Alicia Bergman', 'Robin Jensen', 'Linus Johansson', 'Kasper Nilsson', 'Viktor Rösler']
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
