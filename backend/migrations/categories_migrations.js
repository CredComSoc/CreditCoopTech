const fs = require('fs');
const axios = require('axios').default;

const FormData = require('form-data');
let categories = [
    {
        name: 'food',
        image_url: './categories_images/060-food.png'
    },
    {
        name: 'fiber',
        image_url: './categories_images/026-cotton.png'
    },
    {
        name: 'fuel',
        image_url: './categories_images/028-fossil-fuel.png'
    },
    {
        name: 'plants',
        image_url: './categories_images/031-plants.png'
    },
    {
        name: 'animals',
        image_url: './categories_images/032-livestock.png'
    },
    {
        name: 'farm services',
        image_url: './categories_images/051-agriculture.png'
    },
    {
        name: 'farm supplies',
        image_url: './categories_images/024-supplies.png'
    },
    {
        name: 'general services',
        image_url: './categories_images/044-customer-service-1.png'
    },
    {
        name: 'general products',
        image_url: './categories_images/043-box.png'
    },
    {
        name: 'food',
        image_url: './categories_images/060-food.png'
    },
    {
        name: 'building',
        image_url: './categories_images/047-brickwall.png'
    },
    {
        name: 'fixing',
        image_url: './categories_images/tool-box.png'
    },
    {
        name: 'healing',
        image_url: './categories_images/017-healing.png'
    },
    {
        name: 'labor',
        image_url: './categories_images/055-shovel.png'
    },
    {
        name: 'education',
        image_url: './categories_images/012-exchange.png'
    },
    {
        name: 'knowledge',
        image_url: './categories_images/011-education.png'
    },
    {
        name: 'art',
        image_url: './categories_images/005-hobby.png'
    },
]
let count  = 0;
categories.forEach(async el => {
    const fileData = fs.createReadStream(el.image_url);

    let model = new FormData();
    model.append('name', el.name);
    model.append('file', fileData, fileData.name);
    model.append('coverImgInd', 0)
    const boundary = model.getHeaders()['content-type'].split('=')[1]; // Extract boundary from content-type header

    const options = {
        method: 'POST',
        headers: {
            ...model.getHeaders(), // Set headers directly from FormData
        },
        credentials: 'include',
    };

    await axios.post('http://127.0.0.1:3000/categories', model, options)
        .then(response => {
            count++;
            console.log('Response:', response.data);
        })
        .catch(error => {
            console.error('Error:', error);
        });
console.log(count);        
})

// read me
// Path: backend\migrations\categories_migrations.js
// run the backend first by navigating to backend the npm run start
// run this file in node js as 'node categories_migrations.js'
// this will create categories in the database
// finally check if the console log in the final is 17 other wise there is a category that is not saved
// to run in production just change the url to the production deployed backend url
