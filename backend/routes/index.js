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
const { MongoClient, ObjectId } = require('mongodb');
const nodemailer = require('nodemailer')
const { promisify } = require('util');

FRONTEND_URL = 'http://155.4.159.231:8080'

const transporter = nodemailer.createTransport({
  service: 'hotmail',
  auth: {
    user: 'svenskbarter.reset@outlook.com',
    pass: 'SvenskBarter2022!'
  }
})

module.exports = async function(dbUrl, dbFolder) {
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
    db.close()
    return result
  }

  async function updateUser(user_query, update_query) {
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);
    const result = await dbo.collection("users").updateOne(user_query, update_query)
    db.close()
    //console.log(result)
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
    gfs = new mongoose.mongo.GridFSBucket(conn.db, { bucketName: "uploads" })
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
    try {
      gfs.find({ filename: req.params.filename }).toArray((err, files) => {
        if (!files[0] || files.length === 0) {
          res.status(404).send('No file exists');
        } else {
          if (files[0].contentType === 'image/jpeg' || files[0].contentType === 'image/png' || files[0].contentType === 'image/gif') {
            gfs.openDownloadStreamByName(files[0].filename).pipe(res)
          }
        }
      })
    } catch (error) {
      res.status(404).send('No file exists');
    }
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
      res.status(400).send(false)
    } 
  })

  router.post("/login", passport.authenticate('local'), (req, res) => {
    res.sendStatus(200)
  })

  router.post('/logout', function (req, res) {
    req.logout()
    res.sendStatus(200)
  });

  router.get("/admin", (req, res) => {
    getUser({ "profile.accountName": req.user }).then((user) => {
      res.status(200).json(user.is_admin)
    }).catch((error) => {
      res.sendStatus(500)
    })
  })

  /*****************************************************************************
   * 
   *                                Admin Page
   *                 
   *****************************************************************************/

  router.post("/register", (req, res) => {
    getUser({ email: req.body.email }).then(async (user) => {
      if (user == null) {
        const newUser = {
          email: req.body.email.toLowerCase(),
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
        }
        const db = await MongoClient.connect(dbUrl)
        const dbo = db.db(dbFolder);
        const result = await dbo.collection("users").insertOne(newUser)
        if (result.acknowledged) {
          res.sendStatus(200)
          db.close()
        } else {
          res.sendStatus(500)
          db.close()
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
    getUser({ "profile.accountName": req.user }).then((user) => {
      if (user != null) {
        const userData = {
          "name": user.profile.accountName,
          "description": user.profile.description,
          "adress": user.profile.adress,
          "city": user.profile.city,
          "billingName": user.profile.billing.name,
          "billingBox": user.profile.billing.box,
          "billingAdress": user.profile.billing.adress,
          "orgNumber": user.profile.billing.orgNumber,
          "email": user.email,
          "phone": user.profile.phone,
          "logo": user.profile.logo
        }
        res.status(200).send(userData)
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })

  router.post("/updateProfile", upload.single('file'), (req, res) => {
    getUser({ "profile.accountName": req.user }).then((user) => {
      if (user != null) {
        const newPro = JSON.parse(req.body.accountInfo)
        let newProfile = {
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
        }
        if (req.file) {
          newProfile.logo = req.file.filename
          newProfile.logo_id = req.file.id
        } else {
          newProfile.logo = user.profile.logo
          newProfile.logo_id = user.profile.logo_id
        }
        const query = {
          $set: {
            email: newPro.email,
            profile: newProfile
          }
        }
        updateUser({ "profile.accountName": req.user }, query).then((query) => {
          if (query.acknowledged) {
            // delete old logo if exists
            if (req.file) {
              gridfsBucket = new mongoose.mongo.GridFSBucket(conn.db, {
                bucketName: "uploads",
              });
              gridfsBucket.delete(user.profile.logo_id, function (err, r) {
                if (err) {
                  console.log(err)
                }
              });
            }
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

  /*****************************************************************************
   * 
   *                                Articles
   *                 
   *****************************************************************************/

  router.get("/articles", async (req, res) => {
    if (!req.isAuthenticated()) {
      res.sendStatus(401)
    } else {
      let products = [];
      const db = await MongoClient.connect(dbUrl)
      const dbo = db.db(dbFolder);
      dbo.collection('posts').find({}).toArray(function (err, posts) {
        if (err) {
          res.sendStatus(500)
          db.close()
        }
        else {
          posts.forEach(listing => {
            if(listing.userUploader === req.user) {
              products.push(listing)
            }
          })
          res.status(200).send({products})
          db.close()
        } 
      })
    }
  })

  router.get("/article/:id", async (req, res) => {
    if (!req.isAuthenticated()) {
      res.sendStatus(401)
    } else {
      const db = await MongoClient.connect(dbUrl)
      const dbo = db.db(dbFolder);
      dbo.collection('posts').find({}).toArray(function (err, posts) {
        if (err) {
          res.sendStatus(500)
          db.close()
        }
        else {
          let foundArticle = false
          posts.forEach(listing => {
            if(listing.id === req.params.id) {
              foundArticle = true;
              res.status(200).send({listing})
              db.close()
            }
          })
          if (!foundArticle) {
            res.sendStatus(500)
            db.close()
          }
        } 
      })
    }
  })

    // create a article object in mongoDB
    router.post('/upload/article', upload.array('file', 5), async (req, res) => {
      if (!req.isAuthenticated()) {
        res.sendStatus(401)
      } else {
        const newArticle = JSON.parse(req.body.article);
        if (req.files)
        {
          let images = req.files.map(obj => obj.filename);
          newArticle.coverImg = images[req.body.coverImgInd];
          images = images.filter((img) => { return img !== newArticle.coverImg })
          newArticle.img = images;
        }
        newArticle.id = uuid.v4().toString();
        newArticle.userUploader = req.user;
        
        // for ttl index in posts
        if ('end-date' in newArticle) {
          newArticle['end-date'] = new Date(newArticle['end-date']);
        }
        const db = await MongoClient.connect(dbUrl)
        const dbo = db.db(dbFolder);
        dbo.collection("posts").insertOne(newArticle, (err, result)=>{
          if (err) {
            res.sendStatus(500)
            db.close()
          }
          else if (result != null) {
            res.sendStatus(200);
            db.close()
          }
          else {
            // If we dont find a result
            db.close();
            res.status(404).send("No posts found.")
          }
        })
      }
    });
  
    router.post('/article/remove/:id', (req, res) => {
  
      // get all img id from request payload
      const imgIDs = req.body.imgIDs; 
  
      // delete article from db
      const query = { id: req.params.id };
      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);
        dbo.collection('posts').deleteOne(query, function (err, result) {
          if (err) {
            db.close();
            res.sendStatus(500);
          }
          else if (result.matchedCount != 0) {
            // delete all img of existing article
            for (const id of imgIDs) {
              gfs.delete(ObjectId(id), function (err, r2) {
                if (err) {
                  console.log(err)
                }
                else {
                  console.log("deleted")
                  console.log("image:", id)
                }
              });
            }
            db.close();
            res.sendStatus(200);
          }
          else {
            // If we dont find a result
            db.close();
            res.sendStatus(204);
          }
        })
      })
    });

  /*****************************************************************************
   * 
   *                                Chat
   *                 
   *****************************************************************************/

  router.get('/chat/histories', (req, res) => {
    const { getAllChatHistories } = require('./chatFunctions.js');
    getAllChatHistories(req.user).then((histories) => {
      const userInfo = {};
      userInfo.histories = histories;
      userInfo.username = req.user;
      res.json(userInfo);
    }
    ).catch((err) => {
      res.status(500).json(err);
    }
    )
  })

  router.get('/chat/history/:id', (req, res) => {
    const chatID = req.params.id;
    const { getChatHistory } = require('./chatFunctions.js');
    getChatHistory(chatID).then((history) => {
      res.json(history);
    });
  })

  router.get('/chat/:user', (req, res) => {
    const { chatExists } = require('./chatFunctions.js');
    const chatter = req.params.user;
    console.log(req.user)
    chatExists(req.user, chatter).then((exists) => {
      res.json(exists);
    });
  })

  /*****************************************************************************
   * 
   *                                Shop
   *                 
   *****************************************************************************/

  router.post('/getAllListings', async (req, res) => {
    if (!req.isAuthenticated()) {
      res.sendStatus(401)
    } else {
          // fetch all metadata about listing from mongoDB
    let searchword = req.body.searchword.split(' ')
    let destinations = req.body.destinations;
    let categories = req.body.categories;
    let articles = req.body.articles;
    let status = req.body.status;

    let productsAllListingsArray = []
    let servicesAllListingsArray = []

    searchword = searchword.filter(function(value, index, arr) {
      return value !== "";
    })

    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);
    dbo.collection('posts').find({}).toArray(function (err, posts) {
      if (err) {
        res.sendStatus(500)
        db.close()
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
          // OM STATUS
          if (status.length !== 0) {
            if (!status.includes(listing.status)) {
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
        res.send({allProducts: productsAllListingsArray, allServices: servicesAllListingsArray, username: req.user})
        db.close();
      }
    })
    }
  })


  /*****************************************************************************
   * 
   *                                Members
   *                 
   *****************************************************************************/

  router.post('/member', (req, res) => {
    getUser(req.body).then((user) => {
      if (user != null) {
        const userData = {
          "name": user.profile.accountName,
          "description": user.profile.description,
          "adress": user.profile.adress,
          "city": user.profile.city,
          "billingName": user.profile.billing.name,
          "billingBox": user.profile.billing.box,
          "billingAdress": user.profile.billing.adress,
          "orgNumber": user.profile.billing.orgNumber,
          "email": user.email,
          "phone": user.profile.phone,
          "logo": user.profile.logo
        }
        res.status(200).send(userData)
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  });

   router.get('/getAllMembers2/', async (req, res) => {
    // fetch all metadata about listing from mongoDB
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);
    dbo.collection('users').find({}).toArray(function (err, users) { 
      if (err) {
        res.sendStatus(500)
        db.close()
      }
      else {
        users.forEach(user => user.password = null)
        res.send(users)
        db.close()
      }
    })
  })

  /*****************************************************************************
   * 
   *                                Notifications
   *                 
   *****************************************************************************/

  router.get("/notification", (req, res) => {
    getUser({ "profile.accountName": req.user }).then((user) => {
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

    getUser({ 'profile.accountName': notification.toUser }).then((user) => {
      if (user != null) {
        let notification_list = user.notifications
        if (notification_list.length >= 4) {
          notification_list = [notification, notification_list[0], notification_list[1], notification_list[2]]
        } else {
          notification_list.push(notification)
        }
        updateUser({ 'profile.accountName': notification.toUser }, { $set: { notifications: notification_list } }).then((query) => {
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
    getUser({ "profile.accountName": req.user }).then((user) => {
      if (user != null) {
        let notification_list = user.notifications
        notification_list.forEach(notification => notification.seen = true)
        updateUser({ "profile.accountName": req.user }, { $set: { notifications: notification_list } }).then((query) => {
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
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("carts").find({ cartOwner: req.user }).toArray(function (err, result) {
        if (err) {
          db.close();
          res.sendStatus(500)
        }
        else if (result != null) {
          const cart = result;
          db.close();
          res.status(200).json(cart);
        }
        else {
          // If we dont find a result
          db.close();
          res.sendStatus(404).json(null);
        }
      });
    });
  });


  router.post('/cart', (req, res) => {
    const cartItem = req.body;
    console.log(cartItem);
    cartItem.cartOwner = req.user;
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("carts").updateOne({ id: cartItem.id, cartOwner: req.user }, { $inc: { quantity: cartItem.quantity } }, (err, result) => {
        if (err) {
          db.close();
          res.sendStatus(500)
        }
        else if (result.matchedCount == 0) {
          dbo.collection("carts").insertOne(cartItem, (err, result) => {
            if (err) {
              db.close();
              res.sendStatus(500)
            }
            else if (result != null) {
              console.log(result)
              db.close();
              res.sendStatus(200)
            }
            else {
              // If we dont find a result
              db.close();
              res.sendStatus(404)
            }
          })
        }
      })
    })
  });


  router.post('/cart/remove', (req, res) => {
    const user = { cartOwner: req.user };
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection("carts").deleteMany(user, function (err, result) {
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
          res.status(404).send("No cart found");
        }
      })
    })
  });


  router.post('/cart/remove/item/:id', (req, res) => {
    const query = { cartOwner: req.user };
    const id = req.params.id;
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection('carts').deleteOne(query, { id: id }, function (err, result) {
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
          res.status(404).send("No item found");
        }
      })
    })
  });

  router.post('/cart/remove/item/edit/:id', (req, res) => {
    const query = { id: req.params.id };
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection('carts').deleteMany(query, function (err, result) {
        if (err) {
          db.close();
          res.sendStatus(500);
        }
        else if (result.matchedCount != 0) {
          db.close();
          res.sendStatus(200);
        }
        else {
          // If we dont find a result
          db.close();
          res.sendStatus(404);
        }
      })
    })
  });


  /*****************************************************************************
   * 
   *                                Balance Limit
   *                 
   *****************************************************************************/

  router.get("/limits/min", (req, res) => {
    getUser({ "profile.accountName": req.user }).then(user => {
      if (user != null) {
        res.status(200).json(user.min_limit)
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })

  router.post("/limits/min", (req, res) => {
    getUser(req.body).then(user => {
      if (user != null) {
        res.status(200).json(user.min_limit)
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })

  router.get("/limits", (req, res) => {
    getUser({ "profile.accountName": req.user }).then(user => {
      if (user != null) {
        res.status(200).json({min: user.min_limit, max: user.max_limit})
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })

  router.post("/limits", (req, res) => {
    getUser(req.body).then(user => {
      if (user != null) {
        res.status(200).json({min: user.min_limit, max: user.max_limit})
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })
  /*****************************************************************************
   * 
   *                                Home
   *                 
   *****************************************************************************/

  router.get("/home", (req, res) => {
    getUser({ "profile.accountName": req.user }).then(user => {
      if (user != null) {
        const homeData = {
          companyName: user.profile.accountName,
          shop: [],
          members: []
        };
        MongoClient.connect(dbUrl, (err, db) => {
          let dbo = db.db(dbFolder);
          dbo.collection("posts").find().sort({uploadDate: -1}).limit(10).toArray(function (err, res1) {
            if (err) {
              db.close();
              res.sendStatus(500)
            }
            else if (res1 != null) {
              for (const post of res1) {
                const postInfo = { id: post.id, title: post.title, desc:post.shortDesc, theme: 'regular', img_path: post.coverImg };
                homeData.shop.push(postInfo);
              }
              // TODO not sorted based on newest member
              dbo.collection("users").find().limit(10).toArray(function (err, res2) {
                if (err) {
                  db.close();
                  res.sendStatus(500)
                }
                else if (res2 != null) {
                  db.close();
                  let index = 0;
                  for (const member of res2) {
                    const memberInfo = {id: index, img_path: member.profile.logo, title: member.profile.accountName, theme: 'ellipse'}
                    homeData.members.push(memberInfo);
                    index++;
                  }
                  res.status(200).json(homeData);
                  //TODO ADD MONGO QUERY FOR EVENTS NEXT
                }
                else {
                  // If we dont find a result
                  db.close();
                  res.sendStatus(204)
                }
              });
            }
            else {
              // If we dont find a result
              db.close();
              res.sendStatus(204)
            }
          });
        });
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })


  /*****************************************************************************
   * 
   *                                Reset Password
   *                 
   *****************************************************************************/

  router.post("/forgot", async (req, res) => {
    
    const user = await getUser({ "email": req.body.email })

    if (!user) {
      return res.status(404).send("There is no user with this email.")
    }

    const token = (await promisify(crypto.randomBytes)(20)).toString('hex');

    const query = {
      $set: {
        resetPasswordToken: token,
        resetPasswordExpires: Date.now() + 3600000
      }
    }
    console.log(user)
    updateUser(user, query)

    await transporter.sendMail({
      from: 'svenskbarter.reset@outlook.com', // sender address
      to: user.email, // list of receivers
      subject: 'Återställning av lösenord', // Subject line
      text: `
      Du får det här mailet för att du (eller någon annan) har begärt att ditt lösenord hos Svensk Barter ska återställas.
      Vänligen klicka på följande länk eller klistra in den i en webbläsare för att slutföra processen:
      
      ${FRONTEND_URL}/reset/${token}

      Om du inte har begärt detta, vänligen ignorera detta mail så kommer ditt lösenord förbli oförändrat.
    `
    })
    return res.status(200).send("Email successfully sent")
  }) 

  router.post('/reset/:token', async (req, res) => {

    const user = await getUser({ "resetPasswordToken": req.params.token })
  
    if (!user || !user.resetPasswordExpires > Date.now() || !crypto.timingSafeEqual(Buffer.from(user.resetPasswordToken), Buffer.from(req.params.token))) {
      return res.status(404).send("Password reset token is invalid or has expired.")
    }

    const query = {
      $set: {
        password: req.body.newpass,
        resetPasswordToken: null,
        resetPasswordExpires: null
      }
    }

    updateUser(user, query)
  
    const resetEmail = {
      to: user.email,
      from: 'svenskbarter.reset@outlook.com',
      subject: 'Ditt lösenord har ändrats',
      text: `
      Det här är en bekräftelse på att lösenordet för ditt konto "${user.profile.accountName}" hos Svensk Barter har ändrats.
      `,
    };
    await transporter.sendMail(resetEmail);
    return res.status(200).send("Email successfully sent")
  })

  return { 'router': router, 'conn': conn }
}
