const express = require('express');
const passport = require('passport');
const mongoose = require('mongoose');
const Grid = require('gridfs-stream');
const path = require('path');
const multer = require('multer');
const crypto = require('crypto');
const { GridFsStorage } = require('multer-gridfs-storage');
const methodOverride = require('method-override');
const uuid = require('uuid');
const util = require('util');
const {MongoClient} = require('mongodb');

module.exports = function(dbUrl, dbFolder) {
  const router = express.Router();

  /*****************************************************************************
   * 
   *                           Helper Functions
   *                 
   *****************************************************************************/

  async function getUser(user_query) {
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);
    const result = await dbo.collection("users").findOne(user_query)
    db.close();
    return result
  }

  async function updateUser(user_query, update_query) {
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);
    const result = await dbo.collection("users").updateOne(user_query, update_query)
    //console.log(result)
    db.close();
    return result
  }

  /*****************************************************************************
   * 
   *                                   Images
   *                 
   *****************************************************************************/

   let gfs;
   const conn = mongoose.createConnection(dbUrl, { 
     useNewUrlParser: true, 
     useUnifiedTopology: true 
   }).useDb(dbFolder);
   conn.once('open', () => {
     gfs = new mongoose.mongo.GridFSBucket(conn.db, { bucketName: "uploads"})
   });
   
   const storage = new GridFsStorage({
       url: dbUrl,
       file: (req, file) => {
           return new Promise((resolve, reject) => {
               crypto.randomBytes(16, (err, buf) => {
                   if (err) {
                       return reject(err);
                   }
                   const filename = buf.toString('hex') + path.extname(file.originalname);
                   const fileInfo = {
                       filename: filename,
                       bucketName: 'uploads'
                   };
                   resolve(fileInfo);
               });
           });
       }
   }); 
   const upload = multer({ storage });

  router.get('/image/:filename', (req, res) => {
    gfs.find({filename: req.params.filename}).toArray((err, files) => {
        if(!files[0] || files.length === 0) {
          res.status(404).send('No file exists');
        } else {
          if (files[0].contentType === 'image/jpeg' || files[0].contentType === 'image/png') {
            gfs.openDownloadStreamByName(files[0].filename).pipe(res)
          }
        }
    })
  });

  /*****************************************************************************
   * 
   *                           Login & Authentication
   *                 
   *****************************************************************************/

  router.get('/authenticate', (req, res) => {
    if (req.isAuthenticated()) {
      res.status(200).send(true)
    } else {
      res.status(200).send(false)
    } 
  })

  router.post("/login", passport.authenticate('local'), (req, res) => {
    res.sendStatus(200)
  })

  router.post('/logout', function(req, res){
    req.logout()
    res.sendStatus(200)
  });

  router.get("/admin", (req, res) => {
    getUser({"profile.accountName": req.user}).then((user) => {
      res.status(200).json(user.is_admin)
    })
  })

  /*****************************************************************************
   * 
   *                                Admin Page
   *                 
   *****************************************************************************/

  router.post("/register", (req, res) => {

    getUser({email: req.body.email}).then(async (user) => {
      if (user == null) {
        const newUser = {
          email: req.body.email, 
          password: req.body.password, 
          is_active: req.body.is_active, 
          min_limit: req.body.min_limit,
          max_limit: req.body.max_limit,
          is_admin: req.body.is_admin, 
          pendingPosts: {},
          events: {},
          profile: {
            website: "",
            accountName: req.body.email,
            description: "",
            adress: "",
            city: "",
            phone: "",
            billing: {
              name: "",
              box: "",
              adress: "",
              orgNumber: ""
            },
            logo: "",
            logo_id: ""
          },
          messages: {},
          notifications: [],
          cart: []
        }
        const db = await MongoClient.connect(dbUrl)
        const dbo = db.db(dbFolder);
        const result = dbo.collection("users").insertOne(newUser)
        db.close();
        if (result.acknowledged) {
          res.sendStatus(200)
        } else {
          res.sendStatus(500)
        }
      } else {
        //Det finns redan en användare med namnet
        res.sendStatus(500)
      }
    })
  })

  /*****************************************************************************
   * 
   *                                Profile
   *                 
   *****************************************************************************/

  router.get("/profile", (req, res) => {
    getUser({"profile.accountName": req.user}).then((user) => {
      if (user != null) {
        const userData = {
          "name"          : user.profile.accountName,
          "description"   : user.profile.description,
          "adress"        : user.profile.adress,
          "city"          : user.profile.city,
          "billingName"   : user.profile.billing.name,
          "billingBox"    : user.profile.billing.box,
          "billingAdress" : user.profile.billing.adress,
          "orgNumber"     : user.profile.billing.orgNumber,
          "email"         : user.email,
          "phone"         : user.profile.phone,
          "logo"          : user.profile.logo
        }
        res.status(200).send(userData)
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })

  router.post("/updateProfile", upload.single('file'), (req, res) => { 

    getUser({"profile.accountName": req.user}).then((user) => {
      if (user != null) {
        const newPro = JSON.parse(req.body.accountInfo)
        const newProfile = {
          $set: {
            email: newPro.email,
            profile: {
              website: "",
              accountName: newPro.accountName,
              description: newPro.description,
              adress: newPro.adress,
              city: newPro.city,
              billing: {
                  name: newPro.billingName,
                  box: newPro.billingBox,
                  adress: newPro.billingAdress,
                  orgNumber: newPro.orgNumber
              },
              phone: newPro.phone,
              logo: req.file.filename,
              logo_id: req.file.id
            }
          }
        }
        updateUser({"profile.accountName": req.user}, newProfile).then((query) => {
          if (query.acknowledged) {
            // delete old logo if exists
            gridfsBucket = new mongoose.mongo.GridFSBucket(conn.db, {
              bucketName: "uploads",
            });
            gridfsBucket.delete(user.profile.logo_id, function(err, r) {
              if (err) {
                console.log(err)
              }
              else {
                console.log("deleted")
                console.log("image:", user.profile.logo_id, "filename:", user.profile.logo)
              }
            });
            res.sendStatus(200)
          } else {
            res.status(404).send("Unable to update profile.")
          }
        })
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })

  router.get("/articles", (req, res) => {
    MongoClient.connect(dbUrl, (err, db) => {
      const dbo = db.db(dbFolder);
      let products = [];
      
      dbo.collection('posts').find({}).toArray(function (err, posts) {
        if (err) {
          res.sendStatus(500)
          db.close();
        }
        else {
          posts.forEach(listing => {
            if(listing.userUploader === req.user) {
              products.push(listing)
            }
          })
          res.status(200).send({products})
          db.close();
        } 
      })
    })
  })

  /*****************************************************************************
   * 
   *                                Shop
   *                 
   *****************************************************************************/

   router.post('/getAllListings', (req, res) => {
    // fetch all metadata about listing from mongoDB
    let searchword = req.body.searchword.split(' ')
    let destinations = req.body.destinations;
    let categories = req.body.categories;
    let articles = req.body.articles;

    MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder)
        let productsAllListingsArray = []
        let servicesAllListingsArray = []

        searchword = searchword.filter(function(value, index, arr) {
          return value !== "";
        })
        dbo.collection('posts').find({}).toArray(function (err, posts) {
          if (err) {
            res.sendStatus(500)
            db.close();
          }
          else {
            posts.forEach(listing => {
              //Om ARTIKEL
              if (articles.length !== 0) {
                if (!articles.includes(listing.article)) {
                  return
                }
              }
              //OM DESTINATION
              if (destinations.length !== 0) {
                if (!destinations.includes(listing.destination)) {
                  return
                }
              } 
              //OM CATEGORY
              if (categories.length !== 0) {
                if (!categories.includes(listing.category)) {
                  return
                }
              } 
              foundSearchword = true
              if( searchword.length !== 0 ) {
                for (let i = 0; i < searchword.length; i++) {
                  if (!listing.title.match(new RegExp(searchword[i], "i"))) {
                    foundSearchword = false
                    break
                  } 
                }
                if (!foundSearchword) {
                  return
                }
              }
              //TILLDELA TJÄNST ELLER PRODUKT
              if(listing.article === "product") {
                productsAllListingsArray.push(listing)
              } else if (listing.article === "service") {
                servicesAllListingsArray.push(listing)
              }
            })
            res.send({allProducts: productsAllListingsArray, allServices: servicesAllListingsArray})
            db.close();
          } 
        })
    })
  })

  /*****************************************************************************
   * 
   *                                Create Article
   *                 
   *****************************************************************************/

  // create a article object in mongoDB
  router.post('/upload/article', upload.array('file', 5), (req, res) => {
    const newArticle = JSON.parse(req.body.article);
    let images = req.files.map(obj => obj.filename);
    newArticle.coverImg = images[req.body.coverImgInd];
    images = images.filter((img) => { return img !== newArticle.coverImg })
    newArticle.id = uuid.v4().toString();
    newArticle.userUploader = req.user;
    newArticle.img = images;

    // for ttl index in posts
    if ('end-date' in newArticle) {
      newArticle['end-date'] = new Date(newArticle['end-date']);
    }

    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("posts").insertOne(newArticle, (err, result)=>{
        if (err) {
          db.close();
          res.sendStatus(500)
        }
        else if (result != null) {
          db.close();
          res.sendStatus(200);
        }
        else {
          // If we dont find a result
          db.close();      
          res.status(404).send("No posts found.")
        } 
      })
    })
  });

  /*****************************************************************************
   * 
   *                                Members
   *                 
   *****************************************************************************/

   router.get('/getAllMembers2/', (req, res) => {
    // fetch all metadata about listing from mongoDB
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder)
      dbo.collection('users').find({}).toArray(function (err, users) { 
        if (err) {
          res.sendStatus(500)
          db.close();
        }
        else {
          users.forEach(user => user.password = null)
          res.send(users)
          db.close()
        }
      })
    })
  })

  /*****************************************************************************
   * 
   *                                Notifications
   *                 
   *****************************************************************************/

  router.get("/notification", (req, res) => {
     getUser({"profile.accountName": req.user}).then((user) => {
       if (user != null) {
        res.status(200).json(user.notifications)
       } else {
        res.status(404).send("User not found.")
       }
     })
  })

  router.post("/notification", (req, res) => {
    let notification = req.body
    notification.date = new Date()
    notification.fromUser = req.user

    getUser({'profile.accountName': notification.toUser}).then((user) => {
      if (user != null) {
        let notification_list = user.notifications
        if (notification_list.length >= 4) {
          notification_list = [notification, notification_list[0], notification_list[1], notification_list[2]]
        } else {
          notification_list.push(notification)
        }
        updateUser({'profile.accountName': notification.toUser}, {$set: {notifications: notification_list}}).then((query) => {
          if (query.acknowledged) {
            res.sendStatus(200)
          } else {
            res.status(404).send("User not found.")
          }
        })
      } else {
        res.status(404).send("User not found.")
      }
    })
  })

  router.patch("/notification", (req, res) => {
    getUser({"profile.accountName": req.user}).then((user) => {
      if (user != null) {
        let notification_list = user.notifications
        notification_list.forEach(notification => notification.seen = true)
        updateUser({"profile.accountName": req.user}, {$set: {notifications: notification_list}}).then((query) => {
          if (query.acknowledged) {
            res.sendStatus(200)
          } else {
            res.status(404).send("User not found.")
          }
        })
      } else {
        res.status(404).send("User not found.")
      }
    })
  })

  /*****************************************************************************
   * 
   *                                Cart
   *                 
   *****************************************************************************/

  router.get('/cart', (req, res) => {
    getUser({"profile.accountName": req.user}).then((user) => {
      if (user != null) {
        res.status(200).json(user.cart)
      } else {
        res.status(204).json(null);
      }
    })
  });

  router.post('/cart', (req, res) =>  {
    updateUser({"profile.accountName": req.user}, {$push: {cart: req.body}}).then((query) => {
      if (query.modifiedCount == 1) {
        res.sendStatus(200);
      } else {
        res.status(404).send("No posts found.")
      }
    })
  });

  router.post('/cart/remove', (req, res) => {
    updateUser({"profile.accountName": req.user}, {$set: {cart: []}}).then((query) => {
      if (query.modifiedCount == 1) {
        res.status(200).send("Removed cart");
      } else {
        res.status(404).send("No cart found");
      }
    })
  });

  router.post('/cart/remove/item/:id', (req, res) => {
    updateUser({"profile.accountName": req.user}, {$pull: {cart: {id: req.params.id}}}).then((query) => {
      if (query.modifiedCount == 1) {
        res.status(200).send("Removed from cart");
      } else {
        res.status(404).send("No item found");
      }
    })
  });

  router.get("/minlimit", (req, res) => { 
    getUser({"profile.accountName": req.user}).then(user => {
      console.log(user)
      if (user != null) {
        res.status(200).json(user.min_limit)
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })

  return router
}




