<template>
    <div class="listingStyle">
      <div class="listingImage">
        <p>{{ image }}</p>
      </div>
      <div class="listingFooter">
        <p class="listingTitle">{{ title }}</p>
        <p class="listingDescription">{{ description }}</p>
      </div>
    </div>

</template>
<script>
export default {

  props: {
    title: String,
    description: String,
    image: String
  }
}
</script>

<style scoped>
.listingStyle {
  /**border: solid black 1px;**/
  margin: 1rem;
  width: 200px;
  height:265px;
  box-shadow: 0px 4px 4px 0 rgba(0,0,0,0.16);
}

.listingImage {
  width: 200px;
  height: 180px;
}

.listingFooter {
}

.listingTitle {
  font-size: 15px;
  font-weight: bold;
  line-height: 3.5px;
}

.listingDescription {
  font-size: 11px;
  font-weight: normal;
  line-height: normal;

}

</style>
