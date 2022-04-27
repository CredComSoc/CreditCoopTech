const express = require('express');
const assert = require('assert');
const server = require('../server')
let fetch = require('node-fetch')
const fetchCookie = require('fetch-cookie')
fetch = fetchCookie(fetch)

const test_port = 3001
const express_url = 'http://localhost:' + test_port
const app = express()
let server_instance;

const member = 'TestUser'
const admin = 'TestAdmin'
const password = '123'

async function loginUser(user) {
    const res = await fetch(express_url + '/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ username: user, password: password }),
        credentials: 'include'
    })
}

async function logoutUser() {
    await fetch(express_url + '/logout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include'
    })
}

describe('index routes', function () {
    this.beforeAll(async function() {
        server.initApp(app, dbFolder="test")
        // register user
    })

    this.beforeEach(function(done) {
        server_instance = server.startServer(app, test_port)
        done()
    })

    this.afterEach(function(done) {
        server.stopServer(server_instance)
        done()
    }) 

    describe('GET /authenticate', function() {
        it('Fail to authenticate', async function() {
            await fetch(express_url + '/authenticate', { 
                method: 'GET',
                headers: {
                  'Content-Type': 'application/json'
                },
                credentials: 'include'
              }).then((response) => {
                if (!response.ok) {
                  assert.ok(true)
                  return false
                }
                assert.fail()
              }).catch(() => {
                assert.ok(true)
                return false
              }) 
        })

        it('Authenticate member', async function() {
            await loginUser(member)
            await fetch(express_url + '/authenticate', { 
                method: 'GET',
                headers: {
                  'Content-Type': 'application/json'
                },
                credentials: 'include'
              }).then((response) => {
                if (!response.ok) {
                  assert.fail()
                }
                assert.ok(true)
              })
            await logoutUser()
        })

        describe('Authenticate admin', function() {
            it('todo')
        })

    })

    describe('GET /admin', function() {
        it('Admin status: logged out')

        it('Admin status: member')

        it('Admin status: admin', async function() {
            await loginUser(admin)
            await fetch(express_url + '/admin', { 
                headers: {
                  'Content-Type': 'application/json'
                },
                credentials: 'include'
              }).then((response) => {
                if (!response.ok) {
                  assert.fail()
                }
                assert.ok(true)
              }).catch(() => {
                assert.fail()
              }) 
            await logoutUser()
        })
    })

    describe('POST /login', function() {
        it('todo')
    })

    describe('POST /logout', function() {
        it('todo')
    })

    describe('GET /profile', function() {
        it('todo')
    })

    describe('POST /getAllListings', function() {
        it('todo')
    })

    describe('GET /image/:filename', function() {
        it('todo')
    })

    describe('GET /notification', function() {
        it('todo')
    })

    describe('POST /notification', function() {
        it('todo')
    })

    describe('PATCH /notification', function() {
        it('todo')
    })

    describe('POST /register', function() {
        it('todo')
    })

    describe('POST /updateProfile', function() {
        it('todo')
    })

    describe('GET /articles', function() {
        it('todo')
    })
})


describe('ccRequest routes', function () {

})

describe('ccUserStore routes', function () {

}) 