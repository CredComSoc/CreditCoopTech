<template>
    <div>
        <span v-for="(item, index) in mediaItems" :key="index" class="image-item">
          <img :src="item.baseUrl" :alt="item.filename">
            <input type="checkbox" class="checkbox" :value="item" v-model="selectedItems">
        </span>

        <button @click="fetchImages('prev')">Previous</button>
        <button @click="fetchImages('next')">Next</button>

        <button @click="submitSelection">Submit</button>
    </div>
</template>
  
<script>
/* eslint-disable */
import {  getPhotosArray } from '../../serverFetch'

export default {
    name: 'ImageDisplay',
    props: ['jsonData', 'token'], emits: ['submit'],
    data() {
        return {
            mediaItems: this.jsonData.mediaItems || [],
            nextPageToken: this.jsonData.nextPageToken || '',
            prevPageToken: this.jsonData.prevPageToken || '',
            selectedItems: [], 
            token: this.token
        };
    },
    methods: {
        async fetchImages(direction) {
            // You would implement the logic to fetch the next or previous set of images.
            // This will depend on how you're interacting with the Google API.
            // Use `nextPageToken` or `prevPageToken` accordingly.

            if (direction === 'next') {
                // Fetch the next set of images
                console.log(this.nextPageToken, 'next page token')
                let val = await getPhotosArray(this.token, this.nextPageToken)
                this.mediaItems = val.mediaItems
                this.nextPageToken = val.nextPageToken
                this.prevPageToken = val.prevPageToken
            } else {
                // Fetch the previous set of images
            }
        },
        async submitSelection() {
            // Temporary array to hold image URLs
            let temp = [];

            // Use a for...of loop to handle asynchronous operations
            for (const item of this.selectedItems) {
                try {
                    // Fetch the image from the product URL
                    const response = await fetch(item.baseUrl, {
                        headers: new Headers({ Authorization: `Bearer ${this.token}` })
                    })
                    // const response = await fetch(item.productUrl);
                    if (!response.ok) {
                        throw new Error(`Failed to fetch: ${response.statusText}`);
                    }
                    const blob = await response.blob();

                    // Create a local URL from the blob
                    const imageUrl = URL.createObjectURL(blob);
                    temp.push({ imageUrl });
                    } catch (error) {
                        console.error('Error fetching image:', error);
                }
            }
            // Log the temporary array after all items have been processed
            console.log(temp, 'selected items');
            // Emit an event with the selected items after processing is complete
            this.$emit('submit', temp);
        }
    },
    mounted() {
        // console.log('images', this.jsonData)
    }
};
</script>
  
<style>
    .image-item {
        display: inline-block;
        margin: 10px;
    }
    .image-item img {
        width: 200px;
        height: 200px;
    }
    .checkbox {
        display: block;
        
    }
/* Add your styles here */
</style>
  