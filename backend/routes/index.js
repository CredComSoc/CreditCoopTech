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
const uuid = require('uuid');
const util = require('util');
/**
 * vvvvv CHANGE URL AND DB FOLDER NAME HERE vvvvv
 */
//let url = "mongodb://localhost:27017/"
//let dbFolder = "Test"
//let userFolder = "Users"
/**
 * vvvvv ALICIA OCH KASPER HAR ANVÄNT vvvvv
 */
// let url = "mongodb://localhost:27017/"
// let dbFolder = "sb"
// let userFolder = "user1"
 let url = "mongodb+srv://sb:sb-password@cluster0.i2vzq.mongodb.net/tvitter?retryWrites=true&w=majority"
 let dbFolder = "tvitter"
 let userFolder = "users"

const { 
    v1: uuidv1,
    v4: uuidv4,
  } = require('uuid'); 

const mongoURI = url;
const conn = mongoose.createConnection(mongoURI).useDb(dbFolder);


// Test Route
router.get("/", (req, res) => {
   res.status(200).send("Yo")
})


router.get('/authenticate', (req, res) => {
  if (req.isAuthenticated()) {
    // console.log(req)
    res.sendStatus(200)
  } else {
    res.sendStatus(500)
  } 
})

router.post("/login", passport.authenticate('local'), (req, res) => {
  res.sendStatus(200)
})

router.get("/profile", (req, res) => {
  // console.log(req)
  let myquery = { userID: req.user }

  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null) {
        let userData = {
          "name"        : result.profile.accountname,
          "description" : result.profile.description,
          "adress"      : result.profile.adress,
          "city"        : result.profile.city,
          "billing_name": result.profile.billing.name,
          "billing_box": result.profile.billing.box,
          "billing_adress": result.profile.billing.adress,
          "billing_orgNumber": result.profile.billing.orgNumber,
          "contact_email"     : result.profile.contact.mail,
          "contact_phone"     : result.profile.contact.phone
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
conn.once('open', () => {
  // Init stream
  gfs = Grid(conn.db, mongoose.mongo);
  gfs.collection('uploads');
});

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

const upload = multer({ storage })

// create a article object in mongoDB
router.post('/upload/article', upload.array('file', 5), (req, res) => {
  const newArticle = JSON.parse(req.body.article);
  let images = req.files.map(obj => obj.filename);
  newArticle.coverImg = images[req.body.coverImgInd];
  images = images.filter((img) => { return img !== newArticle.coverImg })
  newArticle.id = uuid.v4().toString();
  newArticle.img = images;

  MongoClient.connect(url, (err, db) => {
    let dbo = db.db(dbFolder);
    const myquery = { userID : "TestUser2" };
    dbo.collection("users").updateOne(myquery, {$push: {posts : newArticle}}, (err, result) => {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null) {
        res.sendStatus(200);
        db.close();
      }
      else {
        // If we dont find a result
        res.status(404).send("No posts found.")
        db.close();      
      } 
    })
  })
});

router.get('/cart/:userID', (req, res) => {
  const myquery = { userID: req.params.userID }
  
  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null) {
        const cart = result.posts;
        res.status(200).json(cart);
        db.close();
      }
      else {
        // If we dont find a result
        res.status(204).json("No cart found.");
        db.close();      
      } 
    })
  })
});

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
                  res.sendStatus(304).send('No tag for listing')
                }
              })
            })
            res.send({allProducts: productsAllListingsArray, allServices: servicesAllListingsArray})
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
          let id = uuidv4();
          let newUser = {
            userID: username, 
            password: pw, 
            email: mail, 
            sessionID: id, 
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
            notifications: {}
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

  // Om användare loggar ut
// params = session ID
// ta bort session ID, returna status
// returnarar status (ok)
router.patch("/logout", (req, res) => {
  let id = req.body.sessionID;
  let myquery = { sessionID: id}

  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
      } 
      else if (result != null) {
        let sessionIDvalue = uuidv4();
        let newSessionID = { $set: {sessionID: sessionIDvalue} };
        dbo.collection("users").updateOne(myquery, newSessionID, function(err, result2) {
          if (err) {
            db.close();
            res.sendStatus(500)
          }
          else {
            db.close()
            res.sendStatus(200)
          }
        });

      } 
      else {
        //If we dont find a result
        res.status(500).send("nothing found!")
        db.close();
      }
    })
  })
})
  


module.exports = router;
