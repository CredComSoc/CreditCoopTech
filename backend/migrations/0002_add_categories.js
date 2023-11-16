const fs = require('fs');
const axios = require('axios').default;
const config = require('../config')
const express_url = config.BACK_END_API_URL

const FormData = require('form-data');
let categories = [
    {
        name: 'Food',
        image_url: './migrations/categories_images/060-food.png'
    },
    {
        name: 'Fiber',
        image_url: './migrations/categories_images/026-cotton.png'
    },
    {
        name: 'Fuel',
        image_url: './migrations/categories_images/028-fossil-fuel.png'
    },
    {
        name: 'Plants',
        image_url: './migrations/categories_images/031-plants.png'
    },
    {
        name: 'Animals',
        image_url: './migrations/categories_images/032-livestock.png'
    },
    {
        name: 'Farm services',
        image_url: './migrations/categories_images/051-agriculture.png'
    },
    {
        name: 'Farm supplies',
        image_url: './migrations/categories_images/024-supplies.png'
    },
    {
        name: 'General services',
        image_url: './migrations/categories_images/044-customer-service-1.png'
    },
    {
        name: 'General products',
        image_url: './migrations/categories_images/043-box.png'
    },
    {
        name: 'Building',
        image_url: './migrations/categories_images/047-brickwall.png'
    },
    {
        name: 'Fixing',
        image_url: './migrations/categories_images/tool-box.png'
    },
    {
        name: 'Healing',
        image_url: './migrations/categories_images/017-healing.png'
    },
    {
        name: 'Labor',
        image_url: './migrations/categories_images/055-shovel.png'
    },
    {
        name: 'Education',
        image_url: './migrations/categories_images/012-exchange.png'
    },
    {
        name: 'Knowledge',
        image_url: './migrations/categories_images/011-education.png'
    },
    {
        name: 'Art',
        image_url: './migrations/categories_images/005-hobby.png'
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

    await axios.post(express_url + '/categories', model, options)
        .then((response) => {
            if (response.status == 200) {
                count++;
                console.log(count + ":" + response.status + ": " + response.data)
            }
            else {
                console.log("Error adding category")
                console.log(count + ":" + response.status + " (" + response.statusText + ") - " + response.data)
            }
        })
        .catch(error => {
            console.error('Error:', error);
        });
})

// read me
// Path: backend\migrations\categories_migrations.js
// run the backend first by navigating to backend the npm run start
// run this file in node js as 'node categories_migrations.js'
// this will create categories in the database
// finally check if the console log in the final is 17 other wise there is a category that is not saved
// to run in production just change the url to the production deployed backend url
