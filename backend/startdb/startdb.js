const express = require('express');
const assert = require('assert');
const server = require('../server')
let fetch = require('node-fetch')
const fetchCookie = require('fetch-cookie')
fetch = fetchCookie(fetch)
const FormData = require('form-data')

// this should ensure we use the config from .env.test
process.env.NODE_ENV = 'test';
const test_port = 3002
const express_url = 'http://localhost:' + test_port
const app = express()
let server_instance;

const admin = 'admin'
const password = '123'
const email = 'sbwebapp@outlook.com' 

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

describe('index routes', function () {
    this.beforeAll(async function() {
        server.initApp(app, test=true)
        server_instance = server.startServer(app, test_port)
    })
  
    this.afterAll(async function() {
      server.stopServer(server_instance)
    })
  
    this.beforeEach(async function() {
    })
  
    this.afterEach(async function(done) {
        done()
    }) 

    describe('POST /register', function() {
        it('Login admin', async function() {
          const result = await registerUser(true, admin, password, email)
          assert(result.status === 200)
        })
    })
})