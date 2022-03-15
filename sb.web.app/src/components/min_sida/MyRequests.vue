<template>
    <div>
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
        <tr>
          <td>1.</td>
          <td>Ingelas sommarcafe</td>
          <td><img src="../../assets/städning.png" alt="Städservice AB"></td>
          <td>2</td>
          <td>250</td>
          <td>500</td>
          <td><button style="background-color: red;"> Avbryt </button><button style="background-color: green;"> Godkänn </button></td>
        </tr>
        <tr>
          <td>2.</td>
          <td>Ingelas sommarcafe</td>
          <td><img src="../../assets/städning.png" alt="Städservice AB"></td>
          <td>1</td>
          <td>500</td>
          <td>500</td>
          <td style="color: green;">GODKÄND</td>
        </tr>
      </table>
    </div>
</template>

<script>

export default {}

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
