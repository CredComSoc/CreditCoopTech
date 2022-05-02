const express = require('express');
const assert = require('assert');
const server = require('../server')
const dbConfig = require('../mongoDB-config')
const {MongoClient} = require('mongodb');
let fetch = require('node-fetch')
const fetchCookie = require('fetch-cookie')
fetch = fetchCookie(fetch)
const FormData = require('form-data')
const fastJson = require('fast-json-stringify')

const test_port = 3001
const express_url = 'http://localhost:' + test_port
const app = express()
let server_instance;

const member = 'TestUser'
const admin = 'TestAdmin'
const password = '123'

async function dropCollection(name) {
  const db = await MongoClient.connect(dbConfig.mongoURL("test", true))
  const dbo = db.db("test");
  await dbo.collection(name).deleteMany({})
  db.close();
}

async function getCollection(name) {
  const db = await MongoClient.connect(dbConfig.mongoURL("test", true))
  const dbo = db.db("test");
  const result = await dbo.collection(name).find({}).toArray()
  db.close();
  return result
}

async function loginUser(user) {
    return await fetch(express_url + '/login', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ email: user, password: password }),
        credentials: 'include'
    })
}

async function logoutUser() {
    return await fetch(express_url + '/logout', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        credentials: 'include'
    })
}

async function registerUser(name, is_admin) {
  await fetch(express_url + '/register', {
    method: 'POST',
    headers: {
      'Content-Type': 'application/json'
    },
    body: JSON.stringify({
      "password"  : password,
      "email"     : name,
      "min_limit" : -100,
      "max_limit" : 100,
      "is_active" : true,
      "is_admin"  : is_admin
    }),
    credentials: 'include'
  })
}

/*****************************************************************************
 *****************************************************************************
 * 
 *                                Index Routes
 *                 
 *****************************************************************************
 *****************************************************************************/

describe('index routes', function () {
  this.beforeAll(async function() {
      await dropCollection("users")
      server.initApp(app, dbFolder="test", localDbUrl = true)
      server_instance = server.startServer(app, test_port)
      await registerUser(member, false)
      await registerUser(admin, true)
  })

  this.afterAll(async function() {
    server.stopServer(server_instance)
  })

  this.beforeEach(function(done) {
      done()
  })

  this.afterEach(function(done) {
      done()
  }) 

  /*****************************************************************************
   * 
   *                                Admin Page
   *                 
   *****************************************************************************/

  describe('POST /register', function() {
    it('todo')
  })

  /*****************************************************************************
   * 
   *                                   Images
   *                 
   *****************************************************************************/

    describe('GET /image/:filename', function() {
      it('todo')
    })

  /*****************************************************************************
   * 
   *                           Login & Authentication
   *                 
   *****************************************************************************/

    describe('POST /login', function() {
        it('Login fail', async function() {
          const result = await loginUser("invalidUser")
          assert(result.status === 401)
        })

        it('Login member', async function() {
          const result = await loginUser(member)
          logoutUser()
          assert(result.status === 200)
        })

        it('Login admin', async function() {
          const result = await loginUser(admin)
          logoutUser()
          assert(result.status === 200)
        })

        it('Login twice, same user', async function() {
          await loginUser(admin)
          const result = await loginUser(admin)
          logoutUser()
          assert(result.status === 200)
        })

        it('Login twice, different users', async function() {
          await loginUser(admin)
          const result = await loginUser(member)
          logoutUser()
          assert(result.status === 200)
        })
    })

    describe('POST /logout', async function() {
        it('Logout w/o being logged in', async function() {
          const result = await logoutUser()
          assert(result.status === 200)
        })

        it('Logout member', async function() {
          await loginUser(member)
          const result = await logoutUser()
          assert(result.status === 200)
        })

        it('Logout admin', async function() {
          await loginUser(admin)
          const result = await logoutUser()
          assert(result.status === 200)
        })
    })

    describe('GET /authenticate', function() {
      async function authenticate() {
        return fetch(express_url + '/authenticate', { 
          method: 'GET',
          headers: {
            'Content-Type': 'application/json'
          },
          credentials: 'include'
        }).then((response) => {
          return response
        }).catch((error) => {
          return error
        }) 
      }

      it('Fail to authenticate', async function() {
        result = await authenticate()
        assert(result.status === 400)
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
    
      describe('Authenticate admin', async function() {
        await loginUser(admin)
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

    })

    describe('GET /admin', function() {
      it('Admin status: logged out')

      it('Admin status: member', async function() {
        await loginUser(member)
        await fetch(express_url + '/admin', { 
            headers: {
              'Content-Type': 'application/json'
            },
            credentials: 'include'
          }).then((response) => {
            return response.json()
          }).then(result => {
            if (!result) {
              assert.ok(true)
            } else {
              assert.fail()
            }
          }).catch(() => {
            assert.fail()
          }) 
        await logoutUser()
    })

      it('Admin status: admin', async function() {
          await loginUser(admin)
          await fetch(express_url + '/admin', { 
              headers: {
                'Content-Type': 'application/json'
              },
              credentials: 'include'
            }).then((response) => {
              return response.json()
            }).then(result => {
              if (result) {
                assert.ok(true)
              } else {
                assert.fail()
              }
            }).catch(() => {
              assert.fail()
            }) 
          await logoutUser()
      })
    })

  /*****************************************************************************
   * 
   *                                Create Article
   *                 
   *****************************************************************************/

  describe('Create article', function() {

    const stringify = fastJson({
      title: 'Test Article Schema',
      type: 'object',
      properties: {
        value: {
          type: 'integer'
        }
      }
    })

    async function createArticle(data) {
      return await fetch(express_url + '/upload/article', { 
        method: 'POST',
        credentials: 'include',
        body: data 
      }).then((res) => {
        return res
      }).then((success) => {
        return success
      }).catch(error => {
        return error
      }) 
    }

    it('Fail to create article when logged out', async function() {
      const data = new FormData()
      data.append('article', stringify({'value': 1}))
      const result = await createArticle(data)
      assert(result.status === 401)
    })

    it('Create article', async function() {
      await dropCollection("posts")
      await loginUser(member)
      const data = new FormData()
      data.append('article', stringify({'value': 2}))
      const result = await createArticle(data)
      await logoutUser()
      if (result.status !== 200) {
        assert.fail()
      }
      const posts = await getCollection("posts")
      if (posts[0].value === 2 && posts[0].userUploader === member) {
        assert.ok(true)
      } else {
        assert.fail()
      }
    })

    it('Create several articles', async function() {

    })
  })

  /*****************************************************************************
   * 
   *                                Profile
   *                 
   *****************************************************************************/

  describe('GET /profile', function() {
        it('Get profile data when logged out')

        it('Get invalid user profile data')

        it('Get member profile data')

        it('Get admin profile data')
  })

    describe('POST /updateProfile', function() {
      it('todo')
  })

  describe('GET /articles', function() {
      it('todo')
  })


  /*****************************************************************************
   * 
   *                                Shop
   *                 
   *****************************************************************************/

  describe('POST /getAllListings', function() {
    it('todo')
  })

  /*****************************************************************************
   * 
   *                                Cart
   *                 
   *****************************************************************************/

  /*****************************************************************************
   * 
   *                                Balance Limit
   *                 
   *****************************************************************************/

  /*****************************************************************************
   * 
   *                                Notifications
   *                 
   *****************************************************************************/

  describe('GET /notification', function() {
      it('todo')
  })

  describe('POST /notification', function() {
      it('todo')
  })

  describe('PATCH /notification', function() {
      it('todo')
  })

/*****************************************************************************
 * 
 *                                Members
 *                 
 *****************************************************************************/

/*****************************************************************************
 * 
 *                                Profile
 *                 
 *****************************************************************************/

})


describe('ccRequest routes', function () {

})

describe('ccUserStore routes', function () {

}) 