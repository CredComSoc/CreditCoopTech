<template>
  <b-container class="mb-12">
    <form name = "form_input" enctype="multipart/form-data">
      <input type="file" name="file" id="file" />
      <button type="button" @click="uploadFile()">Enter</button>
    </form>
  </b-container>
</template>

<script>
import { uploadFile } from './../../../serverFetch.js'

export default {
  methods: {
    uploadFile (listingObj) {
      //Kommer hämta serverFetch uploadFile funktion med filen som parameter
      const file = document.getElementById('file')
      const uploadPromise = uploadFile(file.files[0])
      uploadPromise.then(res => {
        console.log('aaa')
      })
    }
  }
}
</script>
