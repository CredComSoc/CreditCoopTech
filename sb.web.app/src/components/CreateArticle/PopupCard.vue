<template>
<div id="fullscreen-container">
  <div id="popup-container">
    <div id="popup-head">
      <h3>{{ this.title }}</h3>
      <a v-if="this.btnLink !== undefined" id="popup-close-img" @click="this.closePopup" :href="this.btnLink"> <img src="../../assets/link_arrow/popup_close.png" /> </a>
      <a v-if="this.btnLink === undefined" id="popup-close-img" @click="this.closePopup" href="Javascript:void(0)"> <img src="../../assets/link_arrow/popup_close.png" /> </a>
    </div>
    <div id="popup-middle">
      <p>{{ this.cardText }}</p>
    </div>
    <div v-if="this.btnLink !== undefined" id="popup-bottom">
      <form :action="this.btnLink" @click="this.closePopup">
        <input id="confirm-btn" type="submit" :value="this.btnText"/>
      </form>
    </div>
    <div v-if="this.btnLink === undefined" id="popup-bottom">
      <form @click="this.closePopup">
        <input id="confirm-btn" type="button" :value="this.btnText"/>
      </form>
    </div>
  </div>
</div>
</template>

<script>
export default {
  name: 'PopupCard',
  props: ['title', 'cardText', 'btnLink', 'btnText'],
  methods: {
    closePopup () {
      if (this.btnLink === null || this.btnLink === undefined) {
        this.$emit('closePopup')
      }
    }
  }
}
</script>

<style scoped>

* {
  font-family: 'Ubuntu', sans-serif;
  padding: 0;
  margin: 0;
  box-sizing: border-box;
}

#fullscreen-container {
  position: fixed;
  overflow: hidden;
  top: 0;
  left: 0;
  width: 100%;
  height: 100%;
  background: rgba(0, 0, 0, 0.3);
  z-index: 100;
}

#popup-container {
  width: 500px;
  height: 232px;
  border: 1px solid #CED4DA;
  display: flex;
  flex-direction: column;
  border-radius: 12px 12px 12px 12px;
  border: 1px solid #CED4DA;
  top: 50%;
  position: fixed;
  left: 50%;
  transform: translateX(-50%) translateY(-50%);
}

#popup-head {
  background: white;
  height: 51px;
  width: 100%;
  border-bottom: 1px solid #CED4DA;
  display: flex;
  border-radius: 12px 12px 0 0;
  flex-direction: row;
  align-items: center;
}

h3 {
  font-style: normal;
  font-weight: 700;
  font-size: 16px;
  line-height: 18px;
  margin-left: 20px;
}

#popup-close-img {
  margin-left: auto;
  margin-right: 15px;
}

#popup-middle {
  background: white;
  height: 110px;
  width: 100%;
  border-top: 1px solid #CED4DA;
  border-bottom: 1px solid #CED4DA;
}

p {
  margin-left: 20px;
  margin-top: 15px;
  white-space: pre-line;
}

#popup-bottom {
  background: white;
  height: 71px;
  width: 100%;
  border-top: 1px solid #CED4DA;
  display: flex;
  align-items: center;
  flex-direction: row-reverse;
  border-radius: 0 0 12px 12px;
}

#confirm-btn {
  background: #4690CD;
  border-radius: 4px;
  color: white;
  border-color: #4690CD;
  padding: 6px 14px;
  margin-right: 15px;
}

#confirm-btn:hover {
  background: #0b5485;
  border-color: #0b5485;
}

@media (max-width: 600px) {
  #popup-container {
    width: 80%;
  }

  p {
    font-size: 14px;
  }
}

</style>
