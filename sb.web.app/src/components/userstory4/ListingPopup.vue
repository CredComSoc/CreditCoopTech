<template>
  <b-container>
    <b-row>
      <div class="popup">
        <div class="popup-inner">
          <b-col cols="8">
            <img class="p-image" src="./images/bulles2.png" alt="Coffea">
          </b-col>
          <b-col cols="4" class="content-right">
            <b-row>
              <p>x</p>
            </b-row>
            <b-row>
              <b-col class="d-flex align-items-center justify-content-center">
               <h5>{{listingObj.title}}</h5>
              </b-col>
            </b-row>
            <b-row>
              <p>{{listingObj.longDesc}}</p>
            </b-row>
            <b-row>
              <p>{{listingObj.destination}}</p>
            </b-row>
            <b-row>
              <p>{{listingObj.price}}</p>
            </b-row>
            <b-row>
              <p>{{amount}}</p>
            </b-row>
            <b-row>
              <p>lägg i varukorg</p>
            </b-row>
          </b-col>
        </div>
      </div>
    </b-row>
  </b-container>
</template>
<script>
export default {

  props: {
    listingObj: Object
  },

  methods: {
  }
}
</script>

<style scoped>

.popup {
  position: fixed;
  top: 0;
  left: 0;
  right: 0;
  bottom: 0;
  z-index: 99;
  background-color: rgba(0, 0, 0, 0.05);
  display: flex;
  align-items: center;
  justify-content: center;
}

.popup-inner {
  background: #FFFFFF;
  border: 4px solid #C4C4C4;
  box-sizing: border-box;
  box-shadow: 0px 4px 4px rgba(0, 0, 0, 0.1);
  display: flex;
  width: 45rem;
  height: 30rem;
}

.content-left {
  width: 60%;
  height: 100%;
}

.content-right {
  background-color: white;
}

.p-image {
  height: 100%
}

</style>
