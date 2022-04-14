const express = require('express');
const passport = require('passport')
const router = express.Router();
const {MongoClient} = require('mongodb');
const mongoose = require('mongoose');
const Grid = require('gridfs-stream');
const path = require('path');
const bodyParser = require('body-parser');
const multer = require('multer');
const crypto = require('crypto');
const { GridFsStorage } = require('multer-gridfs-storage');
const methodOverride = require('method-override');

const url = require('../mongoDB-config')


let dbFolder = "tvitter"
let userFolder = "users"
const mongoURI = url;

// Test Route
router.get("/", (req, res) => {
   res.status(200).send("Yo")
})


router.get('/authenticate', (req, res) => {
  if (req.isAuthenticated()) {
    res.sendStatus(200)
  } else {
    res.sendStatus(500)
  } 
})

router.get("/admin", (req, res) => {
  let myquery = { userID: req.user}
  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null && result.is_admin) {
        res.sendStatus(200)
        db.close();
      }
      else {
        res.sendStatus(500)
        db.close();      
      } 
    })
  })
})

router.post("/login", passport.authenticate('local'), (req, res) => {
  res.sendStatus(200)
})

router.post('/logout', function(req, res){
  req.logout()
  res.sendStatus(200)
});

router.get("/profile", (req, res) => {
  // console.log(req)
  let myquery = { userID: req.user}

  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null) {
        let userData = {
          "name"        : result.profile.accountName,
          "description" : result.profile.description,
          "adress"      : result.profile.adress,
          "city"        : result.profile.city,
          "billingName": result.profile.billing.name,
          "billingBox": result.profile.billing.box,
          "billingAdress": result.profile.billing.adress,
          "orgNumber": result.profile.billing.orgNumber,
          "email"     : result.profile.contact.email,
          "phone"     : result.profile.contact.phone
        }
        res.status(200).send(userData)
        db.close();
      }
      else {
        // If we dont find a result
        res.status(404).send("The profile doesn't exist.")
        db.close();      
      } 
    })
  })
})


// Init gfs
let gfs;
const conn = mongoose.createConnection(url, 
  { useNewUrlParser: true, useUnifiedTopology: true }
).useDb(dbFolder);
conn.once('open', () => {
  // Init stream
  gfs = Grid(conn.db, mongoose.mongo);
  gfs.collection('uploads');
});
//conn.close()

// Create storage engine
const storage = new GridFsStorage({
  url: mongoURI,
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

// create a db objects in sb folder WIP
// router.post('/upload', upload.single('file'), (req, res) => {
//   console.log(req.file);
//   res.json({ file: req.file });
// });

router.get('/image/:filename', (req, res) => {
  gfs.files.findOne({filename: req.params.filename}, (err, file) => {
    if (!file || file.length === 0) {
      return res.status(404).json({
        err: 'No file exists'
      });
    }

    // Check if image
    if (file.contentType === 'image/jpeg' || file.contentType === 'image/png') {
      // Read output to browser
      const readstream = gfs.createReadStream(file.filename);
      readstream.pipe(res);
    } else {
      res.status(404).json({
        err: 'Not an image'
      });
    }
  });
});

router.post('/getAllListings/', (req, res) => {
    // fetch all metadata about listing from mongoDB
    let searchword = req.body.searchword.split(' ')
    let destinations = req.body.destinations;
    let categories = req.body.categories;
    let articles = req.body.articles;

    MongoClient.connect(url, (err, db) => {
        let dbo = db.db(dbFolder)
        let productsAllListingsArray = []
        let servicesAllListingsArray = []

        searchword = searchword.filter(function(value, index, arr) {
          return value !== "";
        })

        dbo.collection(userFolder).find({}).toArray(function (err, users) {
          if (err) {
            res.sendStatus(500)
            db.close();
          }
          else {
            users.forEach(user => {
              user.posts.forEach(listing => {
                
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
                } else {
                  //res.sendStatus(304).send('No tag for listing')
                }
              })
            })
            res.send({allProducts: productsAllListingsArray, allServices: servicesAllListingsArray})
            db.close();
          } 
        })
    })
})


router.get("/notification", (req, res) => {
  const myquery = { userID: req.user}

  MongoClient.connect(url, (err, db) => {
    const dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null) {
        res.status(200).send(result.notifications)
        db.close();
      }
      else {
        // If we dont find a result
        res.status(404).send("The profile doesn't exist.")
        db.close();      
      } 
    })
  })
})

router.post("/notification", (req, res) => {
  console.log(req.body)
  let notification = req.body
  notification.date = new Date()
  notification.fromUser = req.user

  const myquery = { userID: notification.toUser}
  MongoClient.connect(url, (err, db) => {
    const dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null) {
        // update notification list
        let notification_list = result.notifications
        if (notification_list.length >= 3) {
          notification_list = [notification, notification_list[0], notification_list[1]]
        } else {
          notification_list.push(notification)
        }


        // add updated notification list to db
        dbo.collection("users").updateOne(myquery, {$set: {notifications: notification_list}}, function(err, result) {
          if (err) {
            res.sendStatus(500)
            db.close();
          }
          else {
            res.sendStatus(200)
            db.close();
          }
        })
      }
      else {
        // If we dont find a result
        res.status(404).send("The profile doesn't exist.")
        db.close();        
      } 
    })
  })
})

router.patch("/notification", (req, res) => {
  const myquery = { userID: req.user}

  MongoClient.connect(url, (err, db) => {
    const dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null) {
        // update notification list
        let notification_list = result.notifications
        notification_list.forEach(notification => notification.seen = true)

        // add updated notification list to db
        dbo.collection("users").updateOne(myquery, {$set: {notifications: notification_list}}, function(err, result) {
          if (err) {
            res.sendStatus(500)
            db.close();
          }
          else {
            res.sendStatus(200)
            db.close();
          }
        })
      }
      else {
        // If we dont find a result
        res.status(404).send("The profile doesn't exist.")
        db.close();        
      } 
    })
  })
})


// Om användaren registerar sig,
// params = användarnamn, hashat lösenord
// kolla om användarnamn finns, om det finns returna fel, annnars lägg till
// användare.
// returnear status (ok)
// LÄGG TILL CHECK ATT INDATA ÄR OK (inte tom etc)
router.post("/register", (req, res) => {

    let username = req.body.username;
    let pw = req.body.password;
    let mail = req.body.email;
    let min = req.body.min_limit;
    let max = req.body.max_limit;
    let active = req.body.is_active;
    let admin = req.body.is_admin
    
    let myquery = { userID: username}
    MongoClient.connect(url, (err, db) => {
      let dbo = db.db("tvitter");
      dbo.collection("users").findOne(myquery, function(err, result) {
        if (err) {
          res.sendStatus(500)
        } 
        else if (result != null) {
          //Det finns redan en användare med namnet
          res.sendStatus(500)
          db.close();
        } 
        else {
          //skapa användarobjekt
          let newUser = {
            userID: username, 
            password: pw, 
            email: mail, 
            is_active: active, 
            min_limit: min,
            max_limit: max,
            is_admin: admin, 
            posts: {},
            pendingPosts: {},
            events: {},
            profile: {
              website: "",
              accountname: "",
              description: "",
              adress: "",
              city: "",
              contact: {mail: "", phone: ""},
            },
            messages: {},
            notifications: []
          }

          dbo.collection('users').insertOne(newUser, function(err, result) {
            if (err) {throw err}
            else {
              db.close();
              res.sendStatus(200)
            }
          });
        }
      })
    })
  })

  
router.post("/updateProfile", (req, res) => { 
  let myquery = { userID: req.user}
  console.log("User: " + req.user)
  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
      } 
      else if (result != null) {
        //Det finns en användare med namnet
        //Uppdatera profil
        let newProfile = {
          $set: {
            profile: {
              website: "",
              accountName: req.body.accountName,
              description: req.body.description,
              adress: req.body.adress,
              city: req.body.city,
              billing: {
                  name: req.body.billingName,
                  box: req.body.billingBox,
                  adress: req.body.billingAdress,
                  orgNumber: req.body.orgNumber
              },
              contact: {
                  email: req.body.email,
                  phone: req.body.phone
              }
            }
          }
        }
        
        dbo.collection("users").updateOne(myquery, newProfile, function(err, result) {
          if (err) {throw err}
          else {
            db.close();
            res.sendStatus(200)
          }
        });
      }
      else {
        console.log("No result?" + result)
        db.close();
        res.sendStatus(500)
      }
    })
  })
})


module.exports = router;
