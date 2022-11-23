<template >
    <div >
        <div>
            <table style="hej">
                <tr>
                    <th><h1>Saldo</h1></th>
                    <th><h1>Kreditgräns</h1></th>
                    <th><h1>Tillgängligt belopp</h1></th>
                    <th><h1>Avgifter</h1></th>
                </tr>
                <tr>
                    <td>100kr</td>
                    <td>50kr</td>
                    <td>50kr</td>
                    <td>5000kr</td>
                    
                </tr>
            </table>
        </div>
    </div>
</template>

<script>

export default {

  data () {
    return {
    }
  },
  components: {
  },
  methods: {
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
  width:25%;
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
