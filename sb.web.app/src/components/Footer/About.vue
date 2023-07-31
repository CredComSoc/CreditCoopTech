<template>
  <div>
    <h1><b>{{ $t('created_by') }}</b></h1>
    <div>
      <p style="font-style: italic;">{{ $t('initial_student_group') }}</p>
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
    <div>
      <p style="font-style: italic;"> {{ $t('second_student_group') }}  </p>
    </div>
    <div style="max-height: 50em;">
      <table>
        <tr>
        </tr>
        <tr v-for="item in creators2" :key="item">
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
      creators: ['Vincent Ahlström', 'Alicia Bergman', 'Robin Jensen', 'Linus Johansson', 'Kasper Nilsson', 'Viktor Rösler'],
      creators2: ['Oscar Castenäs', 'Simon Jernström', 'Hanna Häger', 'Rojal Maharyan', 'Pernilla Ljungqvist', 'Alma Wärnsäter', 'Rikard Arnemo']
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
  padding: 60px 0px 10px 20px;
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
