//const config = require('../config')
//const {MongoClient} = require('mongodb');

const FormData = require('form-data')
//const fetchCookie = require('fetch-cookie')
const fetch = require('node-fetch')

const test_port = 3000
const express_url = 'http://localhost:' + test_port

// const app = express()
// let server_instance;


async function registerUser (isadmin, username, password, email) {
    const data = new FormData()
    data.append('accountInfo', JSON.stringify({ 
        is_admin: isadmin,
        accountName: username,
        password: password,
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
registerUser(false, "testuser", "testpassword", "test@nowhere.com")