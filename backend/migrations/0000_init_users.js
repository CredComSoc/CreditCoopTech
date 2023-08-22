const FormData = require('form-data')
const fetch = require('node-fetch')
const config = require('../config')

// POST to API end point (which has to be started first) to register users

const express_url = config.BACK_END_API_URL

min_limit_default = -1000;
max_limit_default = 1000;

async function registerUser (isadmin, username, password, email) {
    console.log("Registering user " + username + " with email " + email)
    const data = new FormData()
    data.append('accountInfo', JSON.stringify({ 
        is_admin: isadmin,
        accountName: username,
        password: password,
        min_limit: min_limit_default,
        max_limit: max_limit_default,
        description: 'description',
        adress: 'adress',
        city: 'city',
        billingName: 'billingName',
        billingBox: 'billingBox',
        billingAdress: 'billingAdress',
        orgNumber: 'orgNumber', 
        email: email.toLowerCase(),
        sendWelcomeEmail: false,
        phone: 'phone'
    }))
    return await fetch(express_url + '/register', {
        method: 'POST',
        body: data
    })
    .then((response) => {
        if (response.status == 200) {
            response.text().then(text => 
                console.log(response.status + ": " + text)
            )
        }
        else {
            console.log("Error registering user " + username)
            response.text().then(text => 
                console.log(response.status + " (" + response.statusText + ") - " + text)
            )
        }
        return response
    })
}


// const member = 'testuser'
// const admin = 'testadmin'
// const password = '123'

// registerUser(false, "testuser", "testpassword", "test@nowhere.com")
// registerUser(false, "testuser2", "testpassword", "test2@nowhere.com")
// registerUser(true, "admintest", "testpassword", "admin@nowhere.com")
// registerUser(false, "testuser3", "testpassword", "test3@nowhere.com")

registerUser(false, "testuser4", "testpassword", "test4@nowhere.com")