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

const test_port = 3002
const express_url = 'http://localhost:' + test_port
const app = express()
let server_instance;

const member = 'testuser'
const admin = 'testadmin'
const password = '123'

async function deleteCollection(name) {
  const db = await MongoClient.connect(dbConfig.mongoURL("test", true))
  const dbo = db.db("test");
  const result = await dbo.collection(name).deleteMany({})
  db.close();
  return result
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
      server.initApp(app, dbFolder="test", localDbUrl = true)
      server_instance = server.startServer(app, test_port)
  })

  this.afterAll(async function() {
    server.stopServer(server_instance)
  })

  this.beforeEach(async function() {
    await deleteCollection("users")
    await deleteCollection("posts")
    await deleteCollection("carts")
    await registerUser(member, false)
    await registerUser(admin, true)
  })

  this.afterEach(async function(done) {
      done()
  }) 

  /*****************************************************************************
   * 
   *                                Admin Page
   *                 
   *****************************************************************************/

  describe('POST /register', function() {
    it('Successfully Register')

    it('Fail to Register with bad email')

    it('Fail to Register with bad password')

    it('Fail to Register with bad admin status')

    it('Fail to Register with bad min limit')

    it('Fail to Register with bad max limit')
  })

  /*****************************************************************************
   * 
   *                                   Images
   *                 
   *****************************************************************************/

    describe('GET /image/:filename', function() {
      it('Get existing image')

      it('Fail to get non-existing image')
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
          await logoutUser()
          assert(result.status === 200)
        })

        it('Login admin', async function() {
          const result = await loginUser(admin)
          await logoutUser()
          assert(result.status === 200)
        })

        it('Login twice, same user', async function() {
          await loginUser(admin)
          const result = await loginUser(admin)
          await logoutUser()
          assert(result.status === 200)
        })

        it('Login twice, different users', async function() {
          await loginUser(admin)
          const result = await loginUser(member)
          await logoutUser()
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

      it('Fail to authenticate when logged out', async function() {
        result = await authenticate()
        assert(result.status === 400)
      })

      it('Authenticate member', async function() {
        await loginUser(member)
        const result = await authenticate()
        assert(result.status === 200)
        await logoutUser()
      })
    
      describe('Authenticate admin', async function() {
        await loginUser(admin)
        const result = await authenticate()
        assert(result.status === 200)
        await logoutUser()
      })

    })

    describe('GET /admin', function() {
      async function getAdminStatus() {
        return fetch(express_url + '/admin', { 
          headers: {
            'Content-Type': 'application/json'
          },
          credentials: 'include'
        })
      }

      it('Admin status: logged out', async function() {
        const result = await getAdminStatus()
        assert(result.status = 500)
      })

      it('Admin status: member', async function() {
        await loginUser(member)
        const result = await getAdminStatus()
        await logoutUser()
        assert(result.status = 200 && !(await result.json()))
    })

      it('Admin status: admin', async function() {
        await loginUser(admin)
        const result = await getAdminStatus()
        await logoutUser()
        assert(result.status = 200 && (await result.json()))
      })
    })

  /*****************************************************************************
   * 
   *                                Articles
   *                 
   *****************************************************************************/
    describe('Articles', function() {

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
        })
      }

      async function getArticles() {
        return await fetch(express_url + '/articles', {
          method: 'GET',
          credentials: 'include'
        })
      } 

      async function getArticle(id) {
        return await fetch(express_url + '/article/' + id, {
          method: 'GET',
          credentials: 'include'
        })
      }

      describe('Create article', function() {
      
        it('Fail to create article when logged out', async function() {
          const data = new FormData()
          data.append('article', stringify({'value': 1}))
          const result = await createArticle(data)
          assert(result.status === 401)
        })
    
        it('Create article w/o image', async function() {
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
  
        it('Create several articles w/o images', async function() {
          await loginUser(member)
          for (let i = 0; i < 10; ++i) {
            const data = new FormData()
            data.append('article', stringify({'value': i}))
            const result = await createArticle(data)
            if (result.status !== 200) {
              assert.fail()
            }
          }
          await logoutUser()
          const posts = await getCollection("posts")
          for (let i = 0; i < 10; ++i) {
            if (!(posts[i].value === i)) {
              assert.fail()
            }
          }
          assert.ok(true)
        })
  
        it('Create article with one image')
    
        it('Create article with three images')
      })

      describe('Get article', function() {
      
        describe('GET /articles', function() {
          it('Fail to get articles when logged out', async function() {
            const result = await getArticles()
            if (result.status !== 401) {
              assert.fail()
            }
            assert.ok(true)
          })
  
          it('Get articles when none exist', async function() {
            await deleteCollection("posts")
            await loginUser(member)
            const result = await getArticles()
            const data = await result.json()
            await logoutUser()
            if (result.status !== 200) {
              assert.fail()
            }
            if (data.products.length > 0) {
              assert.fail()
            }
            assert.ok(true)
          })
  
          it('Get articles successfully', async function() {
            await loginUser(member)
            const data = new FormData()
            data.append('article', stringify({'value': 2}))
            await createArticle(data)
            const result = await getArticles()
            const data2 = await result.json()
            await logoutUser()
            if (result.status !== 200) {
              assert.fail()
            }
            if (data2.products.length == 0) {
              assert.fail()
            }
            assert.ok(true)
          })
      })
  
      describe('GET /article/:id', function() {
        it('Fail to get article when logged out', async function() {
          await loginUser(member)

          const data = new FormData()
          data.append('article', stringify({'value': 2}))
          await createArticle(data)

          const result = await getArticles()
          const data2 = await result.json()
          const id = data2.products[0].id

          await logoutUser()

          const result2 = await getArticle(id)
          if (result2.status === 200) {
            assert.fail()
          }

          assert.ok(true)
        })
      
  
        it('Fail to get article that doesnt exist', async function() {
          await loginUser(member)
          const result = await getArticle("abc123")
          await logoutUser()

          if (result.status === 200) {
            assert.fail()
          }

          assert.ok(true)
        })
    
        it('Get article successfully', async function() {
          await loginUser(member)

          const data = new FormData()
          data.append('article', stringify({'value': 2}))
          await createArticle(data)

          const result = await getArticles()
          const data2 = await result.json()
          const id = data2.products[0].id

          const result2 = await getArticle(id)
          if (result2.status !== 200) {
            assert.fail()
          }

          await logoutUser()

          assert.ok(true)
        })
      })
    })
  })
  

  /*****************************************************************************
   * 
   *                                Profile
   *                 
   *****************************************************************************/

  async function getProfile() {
    const result = await fetch(express_url + '/profile', {
      method: 'GET',
      headers: {
        'Content-Type': 'application/json'
      },
      credentials: 'include'
    })
    return result
  }

  async function updateProfile(name, email, description="", logo="") {
    const stringify = fastJson({
      title: 'Test Update Profile Schema',
      type: 'object',
      properties: {
        accountName: {
          type: 'string'
        },
        description: {
          type: 'string'
        },
        adress: {
          type: 'string'
        },
        city: {
          type: 'string'
        },
        billingName: {
          type: 'string'
        },
        billingBox: {
          type: 'string'
        },
        billingAdress: {
          type: 'string'
        },
        orgNumber: {
          type: 'string'
        },
        email: {
          type: 'string'
        },
        phone: {
          type: 'string'
        }, 
        logo: {
          type: 'string'
        }
      }
    })
    const data = new FormData()
    data.append('accountInfo', stringify({ 
      accountName: name,
      description: description,
      adress: "",
      city: "",
      billingName: "",
      billingBox: "",
      billingAdress: "",
      orgNumber: "", 
      email: email,
      phone: "",
      logo: logo
    }))
    return await fetch(express_url + '/updateProfile', {
      method: 'POST',
      credentials: 'include',
      body: data 
    })
  }

  describe('GET /profile', function() {
    it('Fail to get profile data when logged out', async function() {
      const result = await getProfile()
      if (result.status !== 404) {
        assert.fail()
      }
      assert.ok(true)
    })

    it('Get profile data', async function() {
      await loginUser(member)
      const result = await getProfile()
      await logoutUser()
      if (result.status !== 200) {
        assert.fail()
      }
      const data = await result.json()
      if(data.email !== member) {
        assert.fail()
      }
      assert.ok(true)
    })
  })

  describe('POST /updateProfile', function() {
    it('Fail to update profile when logged out', async function() {
      const result = await updateProfile(member, member)
      if (result.status !== 404) {
        assert.fail()
      }
      assert.ok(true)
    })

    it('Update profile successfully w/o any new data', async function() {
      await loginUser(member)
      const result = await updateProfile(member, member)
      const data = await (await getProfile()).json()
      await logoutUser()
      if (result.status !== 200) {
        assert.fail()
      }
      if(data.email !== member && data.profile.accountName !== member && data.description !== "") {
        assert.fail()
      }
      assert.ok(true)
    })

    it('Update profile successfully with new data, old logo', async function() {
      await loginUser(member)
      const result = await updateProfile(member, member, description="hej!")
      const data = await (await getProfile()).json()
      await logoutUser()
      if (result.status !== 200) {
        assert.fail()
      }
      if(data.email !== member && data.profile.accountName !== member && data.description !== "hej!") {
        assert.fail()
      }
      assert.ok(true)
    })

    it('Update profile successfully with new logo')
  })




  /*****************************************************************************
   * 
   *                                Shop
   *                 
   *****************************************************************************/

  describe('POST /getAllListings', function() {

    const stringify = fastJson({
      title: 'Test Article Schema',
      type: 'object',
      properties: {
        title: {
          type: 'string'
        },
        longDesc: {
          type: 'string'
        }, 
        article: {
          type: 'string'
        },
        category: {
          type: 'string'
        },
        shortDesc: {
          type: 'string'
        },
        destination: {
          type: 'string'
        },
      }
    })

    async function createArticle(data) {
      return await fetch(express_url + '/upload/article', { 
        method: 'POST',
        credentials: 'include',
        body: data 
      })
    }

    async function createArticles() {
      const titles = ["aaa sss", "bbb", "ccc", "ddd", "eee", "fff", "ggg", "hhh", "iii", "jjj"]
      const longDescs = ["A0A0", "B1B1", "C2C2", "D3D3", "E4E4", "F5F5", "G6G6", "H7H7", "I8I8", "J9J9"]
      const articles = ["product", "service", "product", "service", "product", "service", "product", "service", "product", "service"]
      const categories = ["M", "N", "O", "M", "N", "O", "M", "N", "O", "M"]
      const shortDescs = ["A!", "B!", "C!", "D!", "E!", "F!", "G!", "H!", "I!", "J!"]
      const destinations = ["U", "V", "W", "X", "Y", "U", "V", "W", "X", "Y"]
      
      await loginUser(member)
      for (let i = 0; i < 10; ++i) {
        const data = new FormData()
        data.append('article', stringify(
          {
            title: titles[i],
            longDesc: longDescs[i], 
            article: articles[i], 
            category: categories[i],
            shortDesc: shortDescs[i],
            destination: destinations[i],
            status: "selling"
          }))
        const result = await createArticle(data)
      }
      await logoutUser()
    }

    async function getListings(searchword, destinationsArray, categoryArray, articleArray) {
      return await fetch(express_url + '/getAllListings/', {
        method: 'POST',
        headers: {
          'Content-Type': 'application/json'
        },
        body: JSON.stringify({ searchword: searchword, destinations: destinationsArray, categories: categoryArray, articles: articleArray, status: [] })
      })
    }

    it('Fail to get listings when logged out', async function() {
      await createArticles()
      const result = await getListings("", [], [], [])
      if (result.status == 200) {
        assert.fail()
      }
      assert.ok(true)
    })

    it('Get all listings', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("", [], [], [])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 || data.allProducts.length + data.allServices.length != 10) {
        assert.fail()
      }
      assert.ok(true)
    })

    it('Get all listings, search title', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("aa", [], [], [])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 || data.allProducts.length != 1) {
        assert.fail()
      }
      assert.ok(true)
    })

    /*
    it('Get all listings, search short description', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("A!", [], [], [])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 && TODO) {
        assert.fail()
      }
      assert.ok(true)
    }) */

    /*
    it('Get all listings, search description', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("A0", [], [], [])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 && TODO) {
        assert.fail()
      }
      assert.ok(true)
    }) */

    it('Get all listings, filter by destinations', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("", ["V", "U"], [], [])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 || data.allProducts.length != 2 || data.allServices.length != 2) {
        assert.fail()
      }
      assert.ok(true)
    })

    it('Get all listings, filter by categories', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("", [], ["N"], [])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 || data.allProducts.length + data.allServices.length != 3) {
        assert.fail()
      }
      assert.ok(true)
    })

    it('Get all listings, filter by articles', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("", [], [], ["service"])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 || data.allProducts.length != 0 || data.allServices.length != 5) {
        assert.fail()
      }
      assert.ok(true)
    })

    it('Get all listings, filter by title, destination, category, article', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("b", ["V", "W", "Y"], ["M", "N"], ["service"])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 || data.allServices.length != 1) {
        assert.fail()
      }
      assert.ok(true)  
    })

    it('Get no listings when searching for non-existing searchword', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("x", [], [], [])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 || data.allServices.length + data.allProducts != 0) {
        assert.fail()
      }
      assert.ok(true)  
    })

    it('Get a listing when using two search words', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("aa s", [], [], [])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 || data.allServices.length + data.allProducts.length != 1) {
        assert.fail()
      }
      assert.ok(true)  
    })

    it('Get a listing when using a searchword with the wrong capitalization', async function() {
      await createArticles()
      await loginUser(member)
      const result = await getListings("BBB", [], [], [])
      await logoutUser()
      const data = await result.json()
      if (result.status != 200 || data.allServices.length + data.allProducts.length != 1) {
        assert.fail()
      }
      assert.ok(true) 
    })

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

})


describe('ccRequest routes', function () {

})

describe('ccUserStore routes', function () {

}) 