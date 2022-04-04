const express = require('express');
var router = express.Router();
const mongoose = require('mongoose');
const Grid = require('gridfs-stream');
var path = require('path');
const bodyParser = require('body-parser');
const multer = require('multer');
const crypto = require('crypto');
const {MongoClient} = require('mongodb');
const { GridFsStorage } = require('multer-gridfs-storage');
const methodOverride = require('method-override');

/**
 * vvvvv CHANGE URL AND DB FOLDER NAME HERE vvvvv
 */
let url = "mongodb://localhost:27017/"
//let dbFolder = "Test"
//let userFolder = "Users"
//let url = "mongodb+srv://sb:sb-password@cluster0.i2vzq.mongodb.net/myFirstDatabase?retryWrites=true&w=majority"
let dbFolder = "sb"
let userFolder = "user1"

const mongoURI = url;
const conn = mongoose.createConnection(mongoURI).useDb(dbFolder);

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

const upload = multer({ storage });

// create a db objects in sb folder
router.post('/upload', upload.single('file'), (req, res) => {
  console.log(req.file);
  res.json({ file: req.file });
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


// Authenticate user before making a request to cc-node
// cause cc-node doesn't authenticate?
// also returns a username for the cc-node request
router.get("/authenticate", (req, res) => {
  let myquery = { sessionID: req.params.sessionID}

  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)

      } 
      else if (result != null) {
        res.status(200).send({userID : res.userID, sessionID: res.sessionID})
  
      }
      else {
        //If we dont find a result
        res.sendStatus(500)
        db.close();

      }
    })
  })
}) 

// Om användaren loggar in,
// params = användarnamn, hashat lösenord
// kolla om användarnamn finns, om det finns, kolla om hashat lösenord matchar
// om det matchar, returnera ett nytt sessionID, lägg även till nytt sessionID
// i databasen.
// om inte finns returnera status, baserat på status skriv felmeddelane. 
// returnerna en session ID/Token

router.post("/login", (req, res) => {
  let username = req.body.username;
  let pw = req.body.password;
  let myquery = { userID: username, password: pw}

  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)

      } 
      else if (result != null) {

        //Skapar sessionID
        let sessionIDvalue = uuidv4();
        let newSessionID = { $set: {sessionID: sessionIDvalue} };

        dbo.collection("users").updateOne(myquery, newSessionID, function(err, result2) {
          if (err) {
            db.close();
            res.sendStatus(500)
 
          }
          else {
            db.close()
            res.status(200).send({sessionID: sessionIDvalue})

          }
        });
      } 
      else {
        //If we dont find a result
        res.sendStatus(500)
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
            admin: no, 
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
  
router.get("/profile", (req, res) => {
  let myquery = { userID: req.params.acc_id}

  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null) {
        let userData = {
          "name"        : result.name,
          "description" : result.description,
          "adress"      : result.adress,
          "city"        : result.city,
          "contact"     : result.contact
        }
        res.status(200).send(userData)
        db.close();
      }
      else {
        // If we dont find a result
        res.status(404).send("The account doesn't exist.")
        db.close();      
      } 
    })
  })
})


// Routes required by the Credits Common Node
// https://gitlab.com/credit-commons-software-stack/cc-node/-/blob/master/AccountStore/accountstore.openapi.yml
// /filter/{full} is unused?

router.get("/filter/full", (req, res) => { 
  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").find({}).toArray(function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else  {
        let userArray = []
        if (result != null) {
          for (user of result) {
            let userData = {
          	"id"      : user.userID,
          	"status"  : user.is_active,
          	"min"     : user.min_limit,
          	"max"     : user.max_limit,
          	"admin"   : user.is_admin ? 1 : 0
            }
            userArray.push(userData);
          
          } 
        }
        res.status(200).send(userArray)
        db.close();
      }
    })
  })
}) 
  
router.get("/filter", (req, res) => {

  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").find({}).toArray(function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else  {
        let userArray = []
        if (result != null) {
          result.forEach(user => userArray.push(user.userID))
        }
        res.status(200).send(userArray)
        db.close();
      }
    })
  })
})

router.get("/:acc_id", (req, res) => {
  let myquery = { userID: req.params.acc_id, sessionID : req.params.x}

  MongoClient.connect(url, (err, db) => {
    let dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        res.sendStatus(500)
        db.close();
      }
      else if (result != null) {
        let userData = {
          "id"      : result.userID,
          "status"  : result.is_active,
          "min"     : result.min_limit,
          "max"     : result.max_limit,
          "admin"   : result.is_admin ? 1 : 0
        }
        res.status(200).send(userData)
        db.close();
      }
      else {
        // If we dont find a result
        res.status(404).send("The account doesn't exist.")
        db.close();      
      } 
    })
  })
})

module.exports = router;
