<template>
    <div class="confirm-dialog-box">
        <div id="popup-container">
            <a  id="popup-close-img" @click="onCancel" > <img src="../../assets/link_arrow/popup_close.png" /> </a>
        <h3>{{ title }}</h3>
    </div>
        <p>{{ body }}</p>
        <div class="button-container">
            <button id='confirm-btn' @click="onConfirm">{{ confirmText }}</button>
            <button id='cancel-btn' @click="onCancel">{{ cancelText }}</button>
        </div>
    </div>
</template>

<script>
/* eslint-disable */
import { isProxy, toRaw } from 'vue'
export default {
    props: {
        title: {
            type: String,
            required: true
        },
        body: {
            type: String,
            required: true
        },
        confirmText: {
            type: String,
            default: 'Confirm'
        },
        cancelText: {
            type: String,
            default: 'Cancel'
        },
        values: {
            type: Object,
            default: () => {}
        }
    },
    emits: ['confirm', 'cancel'],
    methods: {
        onConfirm() {
            let values  = isProxy(this.values) ? toRaw(this.values) : this.values
            this.$emit('confirm', {...values})
        },
        onCancel() {
            this.$emit('cancel')
        }
    }
};
</script>

<style scoped>
.confirm-dialog-box {
    background-color: white;
    border: 1px solid black;
    padding: 20px;
    max-width: 400px;
    margin: 0 auto;
    z-index: 9999;
}


h3 {
  font-style: normal;
  font-weight: 700;
  font-size: 16px;
  line-height: 18px;
  margin-left: 20px;
}
.button-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
}
#popup-container {
  display: flex;
  flex-direction: column;
  text-align: left;
}
#confirm-btn {
  background: #4690CD;
  border-radius: 4px;
  color: white;
  border-color: #4690CD;
  padding: 6px 14px;
  margin-right: 15px;
}
#cancel-btn {
  background: brown;
  border-radius: 4px;
  color: white;
  border-color: #4690CD;
  padding: 6px 14px;
  margin-right: 15px;
}
#popup-close-img {
    margin-left: auto;
    margin-right: 15px;
    cursor: pointer;
}

</style>
