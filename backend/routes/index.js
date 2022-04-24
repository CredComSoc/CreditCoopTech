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


  let gfs;
  const conn = mongoose.createConnection(dbUrl, { 
    useNewUrlParser: true, 
    useUnifiedTopology: true 
  }).useDb(dbFolder);
  conn.once('open', () => {
    /*
    gfs = Grid(conn.db, mongoose.mongo);
    gfs.collection('uploads'); */
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
    let myquery = { "profile.accountName": req.user}
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
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
    let myquery = { "profile.accountName": req.user}

    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("users").findOne(myquery, function(err, result) {
        if (err) {
          res.sendStatus(500)
          db.close();
        }
        else if (result != null) {
          let userData = {
            "name"          : result.profile.accountName,
            "description"   : result.profile.description,
            "adress"        : result.profile.adress,
            "city"          : result.profile.city,
            "billingName"   : result.profile.billing.name,
            "billingBox"    : result.profile.billing.box,
            "billingAdress" : result.profile.billing.adress,
            "orgNumber"     : result.profile.billing.orgNumber,
            "email"         : result.email,
            "phone"         : result.profile.ssphone,
            "logo"          : result.profile.logo
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

  router.post("/members/", (req, res) => {
    let accountname = req.body.accountname
    console.log(accountname)
    //let myquery = { accountname: accountname}
    let myquery = {"profile.accountName" :  accountname}

    MongoClient.connect(url, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("users").findOne(myquery, function(err, result) {
        if (err) {
          res.sendStatus(500)
          db.close();
        }
        else if (result != null) {
          let userData = {
            "accountname"       : result.profile.accountName,
            "description"       : result.profile.description,
            "adress"            : result.profile.adress,
            "city"              : result.profile.city,
            "billing_name"      : result.profile.billing.name,
            "billing_box"       : result.profile.billing.box,
            "billing_adress"    : result.profile.billing.adress,
            "billing_orgNumber" : result.profile.billing.orgNumber,
            "contact_email"     : result.mail,
            "contact_phone"     : result.profile.phone
          }
          res.status(200).send(userData)
          db.close();
        }
        else {
          // If we dont find a result
          res.status(405).send("The profile doesn't exist.")
          db.close();      
        } 
      })
    })
  })

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
      const myquery = { 'profile.accountName': req.user };;
      /*
      dbo.collection("users").updateOne(myquery, {$push: {posts : newArticle}}, (err, result) => {
        if (err) {
          db.close();
          res.sendStatus(500)
        }
        else if (result != null || result.matchedCount != 0) {
          console.log(result)
          db.close();
          res.sendStatus(200);
        }
        else {
          // If we dont find a result
          db.close();      
          res.status(404).send("No posts found.")
        } 
      })
      */
    
    dbo.collection("posts").insertOne(newArticle, (err, result)=>{
      if (err) {
        db.close();
        res.sendStatus(500)
      }
      else if (result != null) {
        console.log(result)
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

  router.post('/cart', (req, res) =>  {
    const cartItem = req.body;
    
    MongoClient.connect(url, (err, db) => {
      let dbo = db.db(dbFolder);
      const myquery = { 'profile.accountName': req.user };
      dbo.collection("users").updateOne(myquery, {$push: {cart : cartItem}}, (err, result) => {
        if (err) {
          db.close();
          res.sendStatus(500)
        }
        else if (result != null || result.matchedCount != 0) {
          console.log(result)
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

  router.get('/cart', (req, res) => {
    const myquery = { 'profile.accountName': req.user };
    console.log(req.user)
    
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("users").findOne(myquery, function(err, result) {
        if (err) {
          db.close();
          res.sendStatus(500)
        }
        else if (result != null) {
          const cart = result.cart;
          db.close();
          res.status(200).json(cart);
        }
        else {
          // If we dont find a result
          db.close();      
          res.status(204).json(null);
        } 
      })
    })
  });

  router.post('/cart/remove', (req, res) => {
    const user = { 'profile.accountName': req.user };
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("users").updateOne(user, {$set: { cart: [] } }, function(err, result) {
        if (err) {
          db.close();
          res.sendStatus(500);
        }
        else if (result != null) {
          db.close();
          res.status(200).send("Removed cart");
        }
        else {
          // If we dont find a result
          db.close();      
          res.status(204).send("No cart found");
        } 
      })
    })
  });


  router.post('/cart/remove/item/:id', (req, res) => {
    const user = { 'profile.accountName': req.user };
    const id = req.params.id;
    console.log(id)
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("users").updateOne(user, {$pull: { cart: { id: id } } }, function(err, result) {
        if (err) {
          db.close();
          res.sendStatus(500);
        }
        else if (result != null) {
          db.close();
          res.status(200).send("Removed from cart");
        }
        else {
          // If we dont find a result
          db.close();      
          res.status(204).send("No item found");
        } 
      })
    })
  });


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

  // create a db objects in sb folder WIP
  // router.post('/upload', upload.single('file'), (req, res) => {
  //   console.log(req.file);
  //   res.json({ file: req.file });
  // });

  router.get('/image/:filename', (req, res) => {
    gfs.find({filename: req.params.filename}).toArray((err, files) => {
        if(!files[0] || files.length === 0) {
          res.status(500).send('No file exists');
        } else {
          if (files[0].contentType === 'image/jpeg' || files[0].contentType === 'image/png') {
            gfs.openDownloadStreamByName(files[0].filename).pipe(res)
          }
        }
    })
    /*
    console.log(gfs)
    gfs.files.findOne({filename: req.params.filename}, (err, file) => {
      if (file == null || file.length === 0) {
        res.status(500).send('No file exists');
      } else {
        // Check if image
        if (file.contentType === 'image/jpeg' || file.contentType === 'image/png') {
          // Read output to browser
          const stream =  gfs.createReadStream(file.filename);
          stream.pipe(res);
        } else {
          res.status(500).send('Not an image');
        }
      }
    });
    */
  });

  router.get("/notification", (req, res) => {
  const myquery = { "profile.accountName": req.user}

  MongoClient.connect(dbUrl, (err, db) => {
    const dbo = db.db(dbFolder);
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
    let notification = req.body
    notification.date = new Date()
    notification.fromUser = req.user

    const myquery = { 'profile.accountName': notification.toUser}
    MongoClient.connect(dbUrl, (err, db) => {
      const dbo = db.db(dbFolder);
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
    const myquery = { "profile.accountName": req.user}

    MongoClient.connect(dbUrl, (err, db) => {
      const dbo = db.db(dbFolder);
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

  router.post('/getAllMembers/', (req, res) => {
    // fetch all metadata about listing from mongoDB
    let searchword = req.body.searchword.split(' ')

    MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder)
        let allMembersArray = []

        searchword = searchword.filter(function(value, index, arr) {
          return value !== "";
        })

        dbo.collection('users').find({}).toArray(function (err, users) {
          
          if (err) {
            res.sendStatus(500)
            db.close();
          }
          else {
            users.forEach(user => {
              let name = user.profile.accountname
              foundSearchword = true
              if( searchword.length !== 0 ) {
                for (let i = 0; i < searchword.length; i++) {
                  if (!name.match(new RegExp(searchword[i], "i"))) {
                    foundSearchword = false
                    break
                  } 
                }
                if (!foundSearchword) {
                  return
                }
              }
              allMembersArray.push(user.profile)
            })
            res.send({allMembers: allMembersArray})
            db.close();
          }
        })
    })
  })

  router.post('/getAllMembers2/', (req, res) => {
    // fetch all metadata about listing from mongoDB
    let searchword = req.body.searchword.split(' ')

    MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder)
        let allMembersArray = new Map()
        let adminMembersArray = new Map()

        searchword = searchword.filter(function(value, index, arr) {
          return value !== "";
        })

        dbo.collection('users').find({}).toArray(function (err, users) {
          
          if (err) {
            res.sendStatus(500)
            db.close();
          }
          else {
            users.forEach(user => {
              let name = user.profile.accountName
              foundSearchword = true
              if( searchword.length !== 0 ) {
                for (let i = 0; i < searchword.length; i++) {
                  if (!name.match(new RegExp(searchword[i], "i"))) {
                    foundSearchword = false
                    break
                  } 
                }
                if (!foundSearchword) {
                  return
                }
              }
              if(user.is_admin) {
                if (!adminMembersArray.has("Admin")) {
                  adminMembersArray.set("Admin", [])
                }
                adminMembersArray.get("Admin").push(user.profile)
              } else {
                if (!allMembersArray.has(user.profile.city)) {
                  allMembersArray.set(user.profile.city, [])
                }
                allMembersArray.get(user.profile.city).push(user.profile)
                }
            })
            
            //Sort alphabetically by swedish.
            console.log("hej")
            //console.log(allMembersArray.values())

            for (const value of allMembersArray.values()) {
              value.sort((a, b) => a.accountName.localeCompare(b.accountName));
            }
            console.log(allMembersArray)
            let sortedMap = new Map([...allMembersArray].sort((a, b) => String(a[0]).localeCompare(b[0], 'sv')))
            let finishMap = new Map([...adminMembersArray, ...sortedMap])

            res.send({allMembers: finishMap})
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

    let pw = req.body.password;
    let mail = req.body.email;
    let min = req.body.min_limit;
    let max = req.body.max_limit;
    let active = req.body.is_active;
    let admin = req.body.is_admin
    
    let myquery = { email: mail}
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("users").findOne(myquery, function(err, result) {
        if (err) {
          res.sendStatus(500)
          db.close();
        } 
        else if (result != null) {
          //Det finns redan en användare med namnet
          res.sendStatus(500)
          db.close();
        } 
        else {
          //skapa användarobjekt
          let newUser = {
            email: mail, 
            password: pw, 
            is_active: active, 
            min_limit: min,
            max_limit: max,
            is_admin: admin, 
            pendingPosts: {},
            events: {},
            profile: {
              website: "",
              accountName: mail,
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
 
  router.post("/updateProfile", upload.single('file'), (req, res) => { 
    let myquery = { "profile.accountName": req.user}
    const newPro = JSON.parse(req.body.accountInfo)
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("users").findOne(myquery, function(err, result) {
        if (err) {
          res.sendStatus(500)
        } 
        else if (result != null) {
          
          //Det finns en användare med namnet
          //Uppdatera profil
          let newProfile = {
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
      
          dbo.collection("users").updateOne(myquery, newProfile, function(err, r) {
            if (err) {throw err}
            else {
              // delete old logo if exists
              gridfsBucket = new mongoose.mongo.GridFSBucket(conn.db, {
                bucketName: "uploads",
              });
              gridfsBucket.delete(result.profile.logo_id, function(err, r2) {
                if (err) {
                  console.log(err)
                }
                else {
                  console.log("deleted")
                  console.log("image:", result.profile.logo_id, "filename:", result.profile.logo)
                }
              });
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

  router.get("/articles", (req, res) => {
    const myquery = { "profile.accountName": req.user}

    MongoClient.connect(dbUrl, (err, db) => {
      const dbo = db.db(dbFolder);
      dbo.collection("users").findOne(myquery, function(err, result) {
        if (err) {
          res.sendStatus(500)
          db.close();
        }
        else if (result != null) {
          res.status(200).send(result.posts)
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

  return router
}




