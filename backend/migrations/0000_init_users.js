//const config = require('../config')
//const {MongoClient} = require('mongodb');

const FormData = require('form-data')
//const fetchCookie = require('fetch-cookie')
const fetch = require('node-fetch')

const test_port = 3000
const express_url = 'http://localhost:' + test_port

// const app = express()
// let server_instance;

min_limit_default = -1000;
max_limit_default = 1000;

async function registerUser (isadmin, username, password, email) {
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
        phone: 'phone'
    }))
    return await fetch(express_url + '/register', {
        method: 'POST',
        body: data
    })
    .then((response) => {
        return response
    })
}


// const member = 'testuser'
// const admin = 'testadmin'
// const password = '123'

// run async function to register users
// registerUser(false, "testuser", "testpassword", "test@nowhere.com")
// registerUser(false, "testuser2", "testpassword", "test2@nowhere.com")
// registerUser(true, "admintest", "testpassword", "admin@nowhere.com")
registerUser(false, "testuser3", "testpassword", "test3@nowhere.com")