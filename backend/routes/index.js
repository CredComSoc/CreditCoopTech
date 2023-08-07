const express = require('express');
const passport = require('passport');
const mongoose = require('mongoose');
const path = require('path');
const multer = require('multer');
const crypto = require('crypto');
const { GridFsStorage } = require('multer-gridfs-storage');
const uuid = require('uuid');
const { MongoClient, ObjectId } = require('mongodb');
const nodemailer = require('nodemailer')
const { promisify } = require('util');
const axios = require('axios').default;

const config = require('../config');



/*
const transporter = nodemailer.createTransport({
  service: 'Outlook365',
  secure: true,
  auth: {
    user: 'sbwebapp@outlook.com',
    pass: '@sbapp_KU5'
  }
})
*/

const ouremail = "sbwebapp@outlook.com"

const transporter = nodemailer.createTransport({
  service: 'hotmail',
  secure: true,
  auth: {
    user: ouremail,
    pass: '@sbapp_KU5'
  }
})

module.exports = function() {

  const dbUrl = config.mongoURL;
  const dbFolder = config.dbFolder;
  const FRONTEND_URL = config.FRONTEND_URL; 
  const CC_NODE_URL = config.CC_NODE_URL; 
  const DISABLE_CC_NODE = config.DISABLE_CC_NODE;
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

  router.get('/file/:filename', (req, res) => {
    try {
      gfs.find({ filename: req.params.filename }).toArray((err, files) => {
        if (!files[0] || files.length === 0) {
          res.status(404).send('No file exists');
        } else {
          //to be implimented{more types of files if needed}
          if (files[0].contentType === 'text/plain' || files[0].contentType === 'application/pdf') {
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
      res.status(200).send(false)
    } 
  })

  //to be implimented{if hashed password is stored in db then converting user input password 
  //and then doing password.authenticate}
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
   *                                User Data
   *                 
   *****************************************************************************/

  router.get("/data", async (req, res) => {

    //FIXME: currently this cal gets made on a polling basis frequently, which is very inefficient

    let data = {
      user: {},
      myArticles: [],
      myCart: [],

      allMembers: [],
      allArticles: [],

      saldo: 0,
      creditLine: 0,

      requests: [],
      pendingPurchases: [],
      completedTransactions: [],
      allEvents: []
    }

    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);

    try {
      // update user online status
      await dbo.collection("users").updateOne({"profile.accountName": req.user }, { $set: { 'profile.last_online': Date.now() }})

      // get current user data
      let user = await dbo.collection("users").findOne({"profile.accountName": req.user })
      data.creditLine = user.min_limit*-1
      const userId = user._id.toString()
      delete user._id
      delete user.password
      data.user = user

      // get article data
      let articles = await dbo.collection("posts").find({}).toArray()
      const myArticles = []
      const allArticles = []
      for (let article of articles) {
        const articleUser = await dbo.collection("users").findOne({'_id': article.userId})
        article.userUploader = articleUser.profile.accountName

        if(article.userId.toString() === userId.toString()) {
          myArticles.push(article)
        }

        const now = new Date()
        const chosenDate = article["end-date"]
        if (now.getTime() < chosenDate.getTime()) {
          allArticles.push(article)
        }
      }
      data.myArticles = myArticles
      data.allArticles = allArticles

      // get all members profile data
      const users = await dbo.collection("users").find({}).toArray()
      const allMembers = []
      for (const user of users) {
        let userData = user.profile
        userData.is_admin = user.is_admin
        userData.email = user.email
        allMembers.push(userData)
      }
      data.allMembers = allMembers

      // get cart data
      const carts = await dbo.collection("carts").find({}).toArray()
      const myCart = []
      for (const cart of carts) {
        if (cart.cartOwner === user.profile.accountName) {
          myCart.push(cart)
        }
      }
      data.myCart = myCart

      // get event data
      data.allEvents = await dbo.collection("events").find({}).toArray()
      // get saldo
      try {
        if (DISABLE_CC_NODE) {
          data.saldo = 0
          return res.status(200).json(data)
        }
        const response = await axios.get(CC_NODE_URL + '/account/summary', { 
        headers: {
        'cc-user': userId,
        'cc-auth': '1'
        }})
        
        user_data = response.data.data[userId]
        data.saldo = user_data.completed.balance
        data.creditLimit = data.creditLine
        if(data.saldo < 0)
        {
          // reduce credit line *only* if negative balance
          data.creditLine += data.saldo
        }

      } catch (error) {
        console.log(error)
      }
      

      // get requests
      try {
        const response = await axios.get(CC_NODE_URL + '/transactions', { 
        headers: {
        'cc-user': userId,
        'cc-auth': '1'
        },
        params: {
          'payee': userId
        }})
        let users = {}
        let entries = response.data.data || []
        for (const entry of entries) {
          if((entry.entries[0].payee !== undefined) && !(entry.entries[0].payee in users)) {
            const payee = await getUser({'_id': ObjectId(entry.entries[0].payee)})
            users[entry.entries[0].payee] = payee.profile.accountName   
          }
          if((entry.entries[0].payer !== undefined) && !(entry.entries[0].payer in users)) {
            const payer = await getUser({'_id': ObjectId(entry.entries[0].payer)})
            users[entry.entries[0].payer] = payer.profile.accountName   
          }
          if((entry.entries[0].author !== undefined) && !(entry.entries[0].author in users)) {
            const author = await getUser({'_id': ObjectId(entry.entries[0].author)})
            users[entry.entries[0].author] = author.profile.accountName   
          }
          entry.entries[0].payee = users[entry.entries[0].payee]
          entry.entries[0].payer = users[entry.entries[0].payer]
          entry.entries[0].author = users[entry.entries[0].author]
          if (entry.state === 'completed') {
            data.completedTransactions.push(entry)
          } else if (entry.state === 'pending') {
            data.requests.push(entry)
          }
        }
        //data.requests = response.data
      } catch (error) {
        console.log(error)
      }

      // get transactions
      try {
        const response = await axios.get(CC_NODE_URL + '/transactions', { 
        headers: {
        'cc-user': userId,
        'cc-auth': '1'
        },
        params: {
          'payer': userId
        }})
        let users = {}
        let entries = response.data.data || []
        for (const entry of entries) {
          //console.log(entry)
          if((entry.entries[0].payee !== undefined) && !(entry.entries[0].payee in users)) {
            const payee = await getUser({'_id': ObjectId(entry.entries[0].payee)})
            users[entry.entries[0].payee] = payee.profile.accountName   
          }
          if((entry.entries[0].payer !== undefined) && !(entry.entries[0].payer in users)) {
            const payer = await getUser({'_id': ObjectId(entry.entries[0].payer)})
            users[entry.entries[0].payer] = payer.profile.accountName   
          }
          if((entry.entries[0].author !== undefined) && !(entry.entries[0].author in users)) {
            const author = await getUser({'_id': ObjectId(entry.entries[0].author)})
            users[entry.entries[0].author] = author.profile.accountName   
          }
          entry.entries[0].payee = users[entry.entries[0].payee]
          entry.entries[0].payer = users[entry.entries[0].payer]
          entry.entries[0].author = users[entry.entries[0].author]

          if (entry.state === 'completed') {
            data.completedTransactions.push(entry)
          } else if (entry.state === 'pending') {
            data.pendingPurchases.push(entry)
          }
        }
      
      } catch (error) {
        console.error(error)
      }

      db.close()
      res.status(200).send(data)
    } catch(e) {
      db.close()
      res.status(200).send(data)
    }
  })

  /*****************************************************************************
   * 
   *                                Admin Page
   *                 
   *****************************************************************************/

  router.post("/register", upload.single('file'), (req, res) => { //register a new user
    console.log(req.body)
    const newPro = JSON.parse(req.body.accountInfo)

    const sendWelcomeEmail = req.body.sendWelcomeEmail === "true" ? true : false
    getUser({ email: newPro.email }).then(async (user) => {
      if (user == null) {
        console.log(req.body.accountInfo)
        const newUser = {
          email: newPro.email,
          //to be implimented {using a hashed password later in accordance with the login code(look into login code)}
          password: newPro.password, 
          is_active: req.body.is_active === "false" ? false : true,
          min_limit: parseInt(newPro.min_limit),
          max_limit: parseInt(newPro.max_limit),
          is_admin: newPro.is_admin ? true : false,  
          profile: {
            website: "",
            accountName: newPro.accountName,
            description: newPro.description,
            adress: newPro.adress,
            city: newPro.city,
            phone: newPro.phone,
            billing: {
              name: newPro.billingName,
              box: newPro.billingBox,
              adress: newPro.billingAdress,
              orgNumber: newPro.orgNumber
            },
            logo: "",
            logo_id: ""
          },
          messages: {},
          notifications: [],
        }
        if (req.file) {
          newUser.logo = req.file.filename
          newUser.logo_id = req.file.id
        }
        const db = await MongoClient.connect(dbUrl)
        const dbo = db.db(dbFolder);
        const result = await dbo.collection("users").insertOne(newUser)
        if (result.acknowledged) {

          if (sendWelcomeEmail) {
            try {
              const reponse = await transporter.sendMail({ //send mail to the new user(admin should be able to change this text later)
                from: ouremail, // sender address

                to: newUser.email, 
                subject: 'Medlem i Svensk Barter', // Subject line
                text: `
                Du får det här mailet för att du har begärt att vara medlem hos Svensk Barter.
                Vänligen klicka på följande länk eller klistra in den i en webbläsare för att slutföra processen:
                
                ${FRONTEND_URL}/login

                Dina inloggningsuppgifter är:
                E-postaddress: ${newPro.email}
                Lösenord: ${newPro.password}
                
                Med vänliga hälsningar,
                Svensk Barter
                `
              })
              console.log(reponse)
            } catch (error) {
              console.log(error)
              const result = await dbo.collection("users").deleteOne(newUser)
              if (!result.acknowledged) {
                console.log("could not delete user " + result)
              }
              res.status(404).send('Det gick inte att skicka e-postmeddelandet till denna medlemmen')
              db.close()
              return
            }
          }
          res.status(200).send('Den nya medlemmen är nu registrerad!')
          db.close()
        } else {
          res.sendStatus(404).send('Det gick inte att registrera medlemmen.')
          db.close()
        }
      } else {
        //Det finns redan en {{ $t('user.member_label') }} med namnet
        res.status(500).send('Denhär medlemmen finns redan.')
      }
    })
  })


//Might not be scalable when there is a lot of transactions
  router.get("/economy", async (req, res) => {
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder)
    let user = await dbo.collection("users").findOne({"profile.accountName": req.user })
    const userId = user._id.toString()
    delete user._id
    delete user.password
    let allTransactions = []
    try{
      const response = await axios.get(CC_NODE_URL + '/transactions', { 
        headers: {
        'cc-user': userId,
        'cc-auth': '123'
        },
        params: {
          'state': 'completed'
        }})
        console.log(response.data)
        let userNames = {}
        for (const entry of response.data) {
          //console.log(entry)
          if(!(entry.entries[0].payee in userNames)) {
            const payee = await getUser({'_id': ObjectId(entry.entries[0].payee)})
            userNames[entry.entries[0].payee] = payee.profile.accountName   
          }
          if(!(entry.entries[0].payer in userNames)) {
            const payer = await getUser({'_id': ObjectId(entry.entries[0].payer)})
            userNames[entry.entries[0].payer] = payer.profile.accountName   
          }
          if(!(entry.entries[0].author in userNames)) {
            const author = await getUser({'_id': ObjectId(entry.entries[0].author)})
            userNames[entry.entries[0].author] = author.profile.accountName   
          }
          console.log(entry)
          entry.entries[0].payee = userNames[entry.entries[0].payee]
          entry.entries[0].payer = userNames[entry.entries[0].payer]
          entry.entries[0].author = userNames[entry.entries[0].author]
          console.log(entry)
          allTransactions.push(entry)
        }
    } catch (error) {
      db.close()
      console.log(error)
    }
      db.close()
      console.log(allTransactions)
      res.status(200).send(allTransactions)
    })
    /*try{
      const db = await MongoClient.connect(dbUrl)
      const dbo = db.db(dbFolder);
      const users = await dbo.collection("users").find({}).toArray()

      //Get all the transcations from the whole system, 
      //when connected to the cc-node we have to get all the transactions for each user seperatly
      let data = await dbo.collection('transaction').find({}).toArray()

      for (const entry of data) { //replace id with account name , might want to store both?
        const payee = users.find(element => element._id == entry.payee);
        const payer = users.find(element => element._id == entry.payer);
        entry.payee = payee.profile.accountName
        entry.payer = payer.profile.accountName
      }
      res.status(200).send(data)
      db.close()
    } catch {
      res.status(500).send(data)
    }*/

 

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
        console.log(newPro.accountName)
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

  router.post("/updateuserProfile/:name", upload.single('file'), (req, res) => {
    
    getUser({ "profile.accountName": req.params.name }).then((user) => {
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
        updateUser({ "profile.accountName": req.params.name }, query).then((query) => {
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
    //console.log(req)
    if (!req.isAuthenticated()) {
      res.sendStatus(401)
    } else {
      const user = await getUser({'profile.accountName': req.user})
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
            if(listing.userId.toString() === user._id.toString()) {
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
      const db = await MongoClient.connect(dbUrl)
        const dbo = db.db(dbFolder);
      if (!req.isAuthenticated()) {
        res.sendStatus(401)
      } else {
        const newArticle = JSON.parse(req.body.article);
        if (req.files.length > 0)
        {
          let images = req.files.map(obj => obj.filename);
          newArticle.coverImg = images[req.body.coverImgInd];
          images = images.filter((img) => { return img !== newArticle.coverImg })
          newArticle.img = images;
        }
        else
        {
          dbo.collection("category").findOne({"name": newArticle.category}).then(res => 
            {
              newArticle.coverImg = res.defaultMainImage;
              newArticle.img = res.defaultImage;
            }) 
        }
        newArticle.id = uuid.v4().toString();
        newArticle.userUploader = req.user

        const user = await getUser({'profile.accountName': req.user})

        newArticle.userId = user._id
        
        // for ttl index in posts
        if ('end-date' in newArticle) {
          newArticle['end-date'] = new Date(newArticle['end-date']);
        }
        
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
      const now = new Date
      const past = new Date(now.getFullYear() - 50, now.getMonth(), now.getDate())
      const query = { id: req.params.id };
      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);
        dbo.collection('posts').updateOne(query, {$set: {'end-date': past}}, function (err, result) {
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

  router.post("/uploadFile", upload.single('file'), (req, res) => {
    getUser({ "profile.accountName": req.user }).then((user) => {
      if (user != null) {
        let newFile = {}
        if (req.file) {
          console.log(req.file)
          newFile.name = req.file.filename
          newFile.fileType = req.file.contentType
          newFile.message = req.file.originalname
          res.status(200).json(newFile)
        } else {
          res.status(404).send("The file doesnot exists.")
        }
      } else {
        res.status(404).send("The profile doesn't exist.")
      }
    })
  })

  /*****************************************************************************
   * 
   *                                Notifications
   *                 
   *****************************************************************************/

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

  router.post('/cart', (req, res) => {
    const cartItem = req.body;
    //console.log(cartItem);
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

  router.post('/cart/set/item/:id', (req, res) => {
    const id = req.params.id;
    const query = { cartOwner: req.user, id: id };
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection('carts').updateOne(query, { $set: {quantity: req.body.quantity}}, function (err, result) {
        if (err) {
          db.close();
          res.sendStatus(500);
        }
        else if (result != null) {
          db.close();
          res.status(200).send("Updated cart");
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
      from: ouremail, // sender address   ???'svenskbarter.reset@outlook.com'???
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
      from: ouremail, //'svenskbarter.reset@outlook.com'
      subject: 'Ditt lösenord har ändrats',
      text: `
      Det här är en bekräftelse på att lösenordet för ditt konto "${user.profile.accountName}" hos Svensk Barter har ändrats.
      `,
    };
    await transporter.sendMail(resetEmail);
    return res.status(200).send("Email successfully sent")
  })

  


  /*****************************************************************************
  * 
  *                                Events
  *                 
  *****************************************************************************/
  router.get("/load/event", async (req, res) => {
    console.log('res' + res)
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);
    dbo.collection('events').find({}).toArray(function (err, eventsdata) {
      if (err) {
        res.sendStatus(500)
        db.close()
      }
      else {
        console.log('i index'+ eventsdata)
        res.status(200).send(eventsdata)
        db.close()
      } 
    })
  })

  router.get("/userId", (req, res) => {
    getUser({ "profile.accountName": req.user }).then((user) => {
      res.status(200).json(user._id)
    }).catch((error) => {
      res.sendStatus(500)
    })
  })

  router.post('/upload/event', async (req, res) => {
    if (!req.isAuthenticated()) {
      res.sendStatus(401)
    } else {
      
      console.log(req.body) //shows contents of body in terminal
      
      let newEvent = {
        title: req.body.title,
        start: new Date(req.body.eventstart),
        end: new Date(req.body.eventend),
        allDay: req.body.eventallDay,
        location: req.body.location,
        description: req.body.description,
        contacts: req.body.contacts,
        webpage: req.body.webpage,
        _startTime: req.body._startTime,
        _endTime: req.body._endTime
      }
      
      const user = await getUser({'profile.accountName': req.user})
      
      newEvent.userId = user._id
      
      // for ttl index in posts
      //if ('end-date' in newArticle) {
      //newArticle['end-date'] = new Date(newArticle['end-date']);
      //}
      const db = await MongoClient.connect(dbUrl)
      const dbo = db.db(dbFolder);
      dbo.collection("events").insertOne(newEvent, (err, result)=>{
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
  })
  router.post('/event/remove/:id', (req, res) => {
    const query = { _id: ObjectId(req.params.id) };
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection('events').deleteOne(query, function (err, result) {
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
  })


    //     Categories



  router.post('/categories', upload.array('file', 5), (req, res) => {
    let images = req.files.map(obj => obj.filename);
    let coverImg = images[req.body.coverImgInd];
    images = images.filter((img) => { return img !== coverImg })
    let categories = {
      name: req.body.name,
      defaultImage: images,
      defaultMainImage: coverImg
    }
    try {
      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);
        dbo.collection("category").insertOne(categories, (err, result) => {
          if (err) {
            res.sendStatus(500)
            db.close()
          }
          else if (result != null) {
            res.sendStatus(200);
            db.close()
          }
        })
      })
    }
    catch (ex) {
      res.status(500).send({ exception: ex });
    }
  })

  router.get("/categories", async (req, res) => {
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);
    const result = await dbo.collection("category").find({});
    
   result.toArray(function (err, categories) {
    if (err) {
      res.sendStatus(400)
      db.close()
    }
    else {
      res.status(200).json(categories)
      db.close()
    } 
  })
  })
return { 'router': router, 'conn': conn }
};
