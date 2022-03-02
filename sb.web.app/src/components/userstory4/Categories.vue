<template>
    <div className="categories">
      <div className="category">
        <h1>Typ av artikel</h1>
        <div className="label">
          <input type="checkbox">
          <p>Tjänster</p>
        </div>
        <div className="label">
          <input type="checkbox">
          <p>Produkter</p>
        </div>
      </div>
      <div className="category">
        <h1>Plats</h1>
        <div className="label">
          <input type="checkbox">
          <p>Norrköping</p>
        </div>
        <div className="label">
          <input type="checkbox">
          <p>Söderköping</p>
        </div>
        <div className="label">
          <input type="checkbox">
          <p>Linköping</p>
        </div>
      </div>
      <div className="category">
        <h1>Kategorier</h1>
        <div className="label">
          <input type="checkbox">
          <p>Affärsutveckling & strategi</p>
        </div>
        <div className="label">
          <input type="checkbox">
          <p>Arbetsyta</p>
        </div>
        <div className="label">
          <input type="checkbox">
          <p>Fotografering</p>
        </div>
        <div className="label">
          <input type="checkbox">
          <p>Kök & restaurang</p>
        </div>
        <div className="label">
          <input type="checkbox">
          <p>Marknadsföring</p>
        </div>
        <div className="label">
          <input type="checkbox">
          <p>Rengöring & städ</p>
        </div>
        <div className="label">
          <input type="checkbox">
          <p>Skönhet</p>
        </div>
        <div className="label">
          <input type="checkbox">
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
