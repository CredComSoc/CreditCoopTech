const express = require('express');
const bcrypt = require('bcrypt');
const passport = require('passport');
const mongoose = require('mongoose');
const path = require('path');
const multer = require('multer');
const crypto = require('crypto');
const { GridFsStorage } = require('multer-gridfs-storage');
const uuid = require('uuid');
const { MongoClient, ObjectId } = require('mongodb');
const nodemailer = require('nodemailer')
const { marked } = require('marked');
const handlebars = require("handlebars");
const fs = require('fs').promises;
const { promisify } = require('util');
const axios = require('axios').default;
const config = require('../config');
const cacheMiddleware = require('../middlewares/cacheMiddleware');

marked.Renderer.prototype.paragraph = (text) => {
  return text + '\n';
};


module.exports = function() {

  const dbUrl = config.mongoURL;
  const dbFolder = config.dbFolder;
  const FRONTEND_URL = config.FRONT_END_URL; 
  const CC_NODE_URL = config.CC_NODE_URL; 
  const DISABLE_CC_NODE = config.DISABLE_CC_NODE;
  const router = express.Router();

  const support_email = config.SUPPORT_EMAIL
  const support_email_password = config.SUPPORT_EMAIL_PASSWORD
  let email_transporter = null
  if (support_email != undefined && support_email != "disabled") {
    email_transporter = nodemailer.createTransport({
      host: 'smtp.migadu.com',
      port: 587,
      secure: false, 
      auth: {
        user: support_email,
        pass: support_email_password
      }
    })
  }

  const email_enabled = (email_transporter == null) ? false : true

  /*****************************************************************************
   * 
   *                           Helper Functions
   *                 
   *****************************************************************************/
// Function to retrieve email templates
  async function getTemplatesForEmail(subjectTemplate, bodyTemplate, isDynamic, templateData) {
      try {
        // Read the Markdown template
        const markdownTemplate = await fs.readFile('email-templates.md', 'utf-8');

        // Extract the desired template based on the Template tag
        const templateSections = extractTemplates(markdownTemplate);
        const selectedTemplate_1 = templateSections[subjectTemplate];
        const selectedTemplate_2 = templateSections[bodyTemplate];

        if (!selectedTemplate_1 || !selectedTemplate_2) {
          return {'Error': 'Template not found'};
        }

        // convert markdown to html
        if (isDynamic) {
          var bodyTemplate = compileTemplate(selectedTemplate_2, templateData);
          var htmlTemplate_1 = marked(selectedTemplate_1);
          var htmlTemplate_2 = marked(bodyTemplate);
        } else {
          var htmlTemplate_1 = marked(selectedTemplate_1);
          var htmlTemplate_2 = marked(selectedTemplate_2);
        }

        return {
          'subject': htmlTemplate_1,
          'body': htmlTemplate_2
        }

      } catch (error) {
        console.error("Error retrieving email templates: ", error);
      }
  }

// Function to replace variables in the template
  function compileTemplate(template, data) {
    const compiledTemplate = handlebars.compile(template);
    return compiledTemplate(data);
  }


// Function to send emails
  async function sendEmail(htmlContent, to, subject) {
    const mailOptions = {
      from: support_email,
      to,
      subject,
      html: htmlContent,
    };

    return email_transporter.sendMail(mailOptions);
  }

// Function to extract templates based on Template tags
  function extractTemplates(markdownContent) {
    const templateSections = {};
    const lines = markdownContent.split('\n');

    let currentTemplate = '';
    for (const line of lines) {
      const templateTagMatch = line.match(/<!-- Template: (.+) -->/);
      if (templateTagMatch) {
        currentTemplate = templateTagMatch[1];
        templateSections[currentTemplate] = '';
      } else {
        templateSections[currentTemplate] += line + '\n';
      }
    }

    return templateSections;
  }

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
    return result
  }

  async function encryptPassword(password) {
   try {
     const salt = await bcrypt.genSalt(10);
     const hash = await bcrypt.hash(password, salt);
     return hash;
   } catch (error) {
     console.log(error)
     throw error;
   }
  }

  function cleanQuantityData(quant) {
    if(quant.toString().split('$').length > 1) {
      return parseInt(quant.toString().split('$')[1])
    }
    else {
      return quant
    }
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
          if (files[0].contentType === 'image/jpeg' || files[0].contentType === 'image/png' || files[0].contentType === 'image/gif' || files[0].contentType === 'image/JPG') {
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
    let errors = []
    let userId

    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);

    try {
      // update user online status
      await dbo.collection("users").updateOne({"profile.accountName": req.user }, { $set: { 'profile.last_online': Date.now() }})

      // get current user data
      let user = await dbo.collection("users").findOne({"profile.accountName": req.user })
      if (user) {
        data.creditLine = user.min_limit*-1
        userId = user._id.toString()
        delete user._id
        delete user.password
        data.user = user
      }

      // get article data
      let articles = await dbo.collection("posts").find({}).toArray()
      const myArticles = []
      const allArticles = []
      for (let article of articles) {
        const articleUser = await dbo.collection("users").findOne({'_id': article.userId})
        try {
          if (articleUser) {
            article.userUploader = articleUser.profile.accountName

            if(userId && article.userId.toString() === userId.toString()) {
              myArticles.push(article)
            }
          }

          const now = new Date()
          const chosenDate = article["end-date"]
          if (now.getTime() < Date.parse(chosenDate)) {
            allArticles.push(article)
          }

        } catch (error) {
          console.error(error)
          errors.push("Error with processing articles/items")
        }
      }
      myArticles.sort((a, b) => new Date(b.uploadDate) - new Date(a.uploadDate))
     allArticles.sort((a, b) => new Date(b.uploadDate) - new Date(a.uploadDate))
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
      const carts = await dbo.collection("carts").find({"cartOwner": user.profile.accountName}).toArray()
      data.myCart = carts
      
      // get event data
      data.allEvents = await dbo.collection("events").find({}).toArray()
      // get saldo
      try {
        if (DISABLE_CC_NODE) {
          data.saldo = 0
          return res.status(200).json(data)
        }
        if (userId) {
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
        }
      } catch (error) {
        console.error(error.response.data)
        errors.push("Error processing  CC_NODE events")
      }
      
      db.close()
      res.status(200).send(data)
    } catch (error) {
      console.error(error)
      db.close()
      res.status(200).send(data)
    }
  })

  /*****************************************************************************
   * 
   *                                Admin Page
   *                 
   *****************************************************************************/

  router.post("/register", upload.single('file'), async (req, res) => { //register a new user
    const newPro = JSON.parse(req.body.accountInfo)

    // const sendWelcomeEmail = req.body.sendWelcomeEmail === true ? true : false
    getUser({ email: newPro.email }).then(async (user) => {
      if (user == null) {
        console.log(req.body.accountInfo)
        const newUser = {
          email: newPro.email,
          password: await encryptPassword(newPro.password),
          is_active: req.body.is_active === "false" ? false : true,
          min_limit: parseInt(newPro.min_limit),
          max_limit: parseInt(newPro.max_limit),
          is_admin: newPro.is_admin ? true : false,  
          profile: {
            website: "",
            accountName: newPro.accountName,
            description: newPro.description,
            address: newPro.address,
            city: newPro.city,
            phone: newPro.phone,
            billing: {
              name: newPro.billingName,
              box: newPro.billingBox,
              address: newPro.billingAddress,
              orgNumber: newPro.orgNumber
            },
            logo: "",
            logo_id: ""
          },
          messages: {},
          //notifications: [],
        }
        if (req.file) {
          newUser.logo = req.file.filename
          newUser.logo_id = req.file.id
        }
        const db = await MongoClient.connect(dbUrl)
        const dbo = db.db(dbFolder);
        const result = await dbo.collection("users").insertOne(newUser)
        if (result.acknowledged) {

          if (email_enabled) {
            try {
              // TODO: May be change the language to english if that is the users are english speaking
              const templateData = {
                FRONTEND_URL: `${FRONTEND_URL}`,
                email: newPro.email,
                password: newPro.password
              }
              const templates = await getTemplatesForEmail('WelcomeSubject', 'WelcomeBody', true, templateData)

              const reponse = await sendEmail(templates['body'], newUser.email, templates['subject'])
              console.log(reponse)
            } catch (error) {
              console.log(error)
              const result = await dbo.collection("users").deleteOne(newUser)
              if (!result.acknowledged) {
                console.log("could not delete user " + result)
              }
              res.status(404).send('Email could not be sent to this member.')
              console.error('Could not send welcome email to user...')

              db.close()
              return
            }
          }
          res.status(200).send('The new member is now registered!')
          db.close()
        } else {
          res.sendStatus(404).send('The new member could not be registered.')
          db.close()
        }
      } else {
        //Det finns redan en {{ $t('user.member_label') }} med namnet
        res.status(500).send('This member already exists.')
      }
    })
  })


//Might not be scalable when there is a lot of transactions
  router.get("/economy", async (req, res) => {
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder)
    let allTransactions = []
    let user = await dbo.collection("users").findOne({"profile.accountName": req.user })
    if (user) {
      const userId = user._id.toString()
      delete user._id
      delete user.password
      try{
        const response = await axios.get(CC_NODE_URL + '/transactions', {
          headers: {
            'cc-user': userId,
            'cc-auth': '123'
          },
          params: {
            'state': 'completed'
          }
        })
        let userNames = {}
        var arrayData = response.data;
        if(arrayData.data)
      {
        for (const entry of arrayData.data) {
          if(!(entry.entries[0].payee in userNames)) {
            const payee = await getUser({'_id': ObjectId(entry.entries[0].payee)})
            userNames[entry.entries[0].payee] = payee? payee.profile.accountName: ""   
          }
          if(!(entry.entries[0].payer in userNames)) {
            const payer = await getUser({'_id': ObjectId(entry.entries[0].payer)})
            userNames[entry.entries[0].payer] = payer ? payer.profile.accountName: ""
          }
          if(!(entry.entries[0].author in userNames)) {
            const author = await getUser({'_id': ObjectId(entry.entries[0].author)})
            userNames[entry.entries[0].author] = author? author.profile.accountName: ""   
          }
          entry.entries[0].payee = userNames[entry.entries[0].payee]
          entry.entries[0].payer = userNames[entry.entries[0].payer]
          entry.entries[0].author = userNames[entry.entries[0].author]
          allTransactions.push(entry)
        }
      }
    } catch (error) {
        db.close()
        console.log(error)
      }
    }
    db.close()
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
          "address": user.profile.address,
          "city": user.profile.city,
          "billingName": user.profile.billing.name,
          "billingBox": user.profile.billing.box,
          "billingAddress": user.profile.billing.address,
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
          address: newPro.address,
          city: newPro.city,
          billing: {
            name: newPro.billingName,
            box: newPro.billingBox,
            address: newPro.billingAddress,
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
          address: newPro.address,
          city: newPro.city,
          billing: {
            name: newPro.billingName,
            box: newPro.billingBox,
            address: newPro.billingAddress,
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
    if (!req.isAuthenticated()) {
      res.sendStatus(401)
    } else {
      const user = await getUser({'profile.accountName': req.user})
      if (user) {
        
        let err = false;
        const db = await MongoClient.connect(dbUrl)
        const dbo = db.db(dbFolder);
        dbo.collection('posts').find({"userId": user._id}).toArray(function (err, result) {
          if (err) {
            db.close();
            res.status(400).send("Error in retrieving articles")
          }
          else if (result != null) {
            db.close();
            res.status(200).send(result)
          }
          else {
            db.close();
            res.status(204).send("No matching articles found")
          }
        })
      } else {
        res.sendStatus(401)
        db.close()
      }
    }
  })

  router.get("/article/:id", async (req, res) => {
    if (!req.isAuthenticated()) {
      res.sendStatus(401)
    } else {
      const db = await MongoClient.connect(dbUrl)
      const dbo = db.db(dbFolder);
      // TODO instead of finding all posts then filter them it should be done with query
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
        try {
        const newArticle = JSON.parse(req.body.article);
        if (req.files.length > 0)
        {
          let images = req.files.map(obj => obj.filename);
          if(req.body.coverImgInd)
          {
            newArticle.coverImg = images[req.body.coverImgInd];
          }
          else
          {
            newArticle.coverImg = images[0];
          }
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
        newArticle.uploadDate = new Date();
        newArticle.userUploader = req.user

        const user = await getUser({'profile.accountName': req.user})
        if (user) {
          newArticle.userId = user._id
        }
        // for ttl index in posts
        if ('end-date' in newArticle) {
          newArticle['end-date'] = new Date(newArticle['end-date']);
        }
        
        dbo.collection("posts").insertOne(newArticle, (err, result)=>{
          if (err) {
            console.error(err)
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
        } catch (error) {

        console.error(error)
        res.status(500).send(`Server error occured!\n${error}`)
        }
      }
    });

  router.patch('/edit/article/:id', upload.array('file', 5), async (req, res) => {
    if (!req.isAuthenticated()) {
      res.sendStatus(401)
    } else {
      try {

        const editArticle = JSON.parse(req.body.article);
        if (req.files) {
          let images = req.files.map(obj => obj.filename);
          editArticle.coverImg = images[req.body.coverImgInd];
          images = images.filter((img) => { return img !== editArticle.coverImg })
          editArticle.img = images;
        }
        editArticle['end-date'] = new Date(editArticle['end-date']);
        editArticle['item_update_date'] = new Date();
        const query = { id: req.params.id };
        delete editArticle._id
        const options = { returnNewDocument: true }
        const db = await MongoClient.connect(dbUrl);
        const dbo = db.db(dbFolder);
        dbo.collection('posts').updateOne(query, {$set: editArticle}, options, (err, result) => {
          if (err) {
            res.sendStatus(500);
            console.log(err)
            db.close();
          } else if (result != null) {
            res.status(200).send(result);
            db.close();
          } else {
            db.close();
            res.status(404).send("No post found.");
          }
        });
      } catch (error) {
        console.error(error)
        res.status(500).send("Server error occured!")
      }
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
            res.sendStatus(400).send(err);
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


    router.post('/article/activate/:id', (req, res) => {
      
      const now = new Date()
      const newEndDate = new Date(now.getFullYear() , now.getMonth() + 1, now.getDate())
      const query = { id: req.params.id };
      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);
        try {
        dbo.collection('posts').updateOne(query, {$set: {'end-date': newEndDate}}, function (err, result) {
          if (err) {
            db.close();
            res.sendStatus(400).send(err);
          }
          else if (result.matchedCount != 0) {
            db.close();
            res.status(200).send('successfullyUpdated');
          }
          else {
            // If we dont find a result
            db.close();
            res.sendStatus(204);
          }
        })
      } catch (ex) {
        res.status(400).send(ex)
      }
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
          newFile.name = req.file.filename
          newFile.fileType = req.file.contentType
          newFile.message = req.file.originalname
          res.status(200).json(newFile)
        } else {
          res.status(404).send("The file does not exists.")
        }
      } else {
        res.status(404).send("The profile does not exist.")
      }
    })
  })

  /*****************************************************************************
   * 
   *                                Notifications
   *                 
   *****************************************************************************/

  router.post('/notification', (req, res) => {
    try {
      let notification = req.body
      notification.date = new Date()
      notification.fromUser = req.user
      notification.seen = false

      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);
        dbo.collection('notifications').insertOne(notification, (err, result) => {
          if (err) {
            db.close();
            res.status(400).send("Error in adding new record")
          }
          else if (result != null) {
            db.close();
          res.sendStatus(200)
          }
          else {
            db.close();
            res.status(400).send("Error in adding new record")
          }
        })
      })
    } catch (ex) {
      res.status(500).send("Server error while adding new notificaiton")
    }
  })

  router.patch("/notification", (req, res) => {
    try {
      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);
        dbo.collection('notifications').updateMany(
          { 'toUser': req.user },
          { $set: { 'seen': true }}, function (err, result) {
            if (err) {
              db.close();
              res.status(400).send("Error in updating notifications' seen status")
            }
            else if (result.matchedCount != 0) {
              db.close();
              res.status(200).send("Notifications marked as seen");
            }
            else {
              db.close();
              res.status(204).send("No matching notifications found")
            }
          }
        )
      })
    } catch (ex) {
      res.status(500).send("Error while updating notifications");
      console.log(ex)
    }
  })

  router.get("/notifications/byUser", (req, res) => {
    try {
      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);  // { "toUser": req.user }
        const notifications = dbo.collection("notifications").find({ "toUser": req.user }).toArray(function (err, result) {
          if (err) {
            db.close();
            res.status(400).send("Error in retrieving notifications")
          }
          else if (result != null) {
            db.close();
            res.status(200).send(result)
          }
          else {
            db.close();
            res.status(204).send("No matching notifications found")
          }
        })
      })
    } catch (ex) {
      res.status(500).send({ error: 'Error while fetching notifications' })
      console.log(ex)
    }
  });

  /*****************************************************************************
   * 
   *                                Cart
   *                 
   *****************************************************************************/

  router.post('/cart', (req, res) => {
    const cartItem = req.body;
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
        else
        {
          db.close();
          res.sendStatus(200);
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
    const id = req.params.id;
    const query = { cartOwner: req.user, id:id };
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
      dbo.collection('carts').deleteOne(query, function (err, result) {
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
      return res.status(400).send("There is no user with this email.")
    }

    const token = (await promisify(crypto.randomBytes)(20)).toString('hex');

    const query = {
      $set: {
        resetPasswordToken: token,
        resetPasswordExpires: Date.now() + 3600000
      }
    }
    updateUser(user, query)
    if (email_enabled) {
      const templateData = {
        FRONTEND_URL: `${FRONTEND_URL}`,
        token: `${token}`
      }
      const templates = await getTemplatesForEmail('PasswordSubject', 'PasswordBody', true, templateData)

      const response = await sendEmail(templates['body'], user.email, templates['subject'])
      return res.status(200).send("Email successfully sent")
    }
  }) 

  router.post('/reset/:token', async (req, res) => {

    const user = await getUser({ "resetPasswordToken": req.params.token })
  
    if (!user || !user.resetPasswordExpires > Date.now() || !crypto.timingSafeEqual(Buffer.from(user.resetPasswordToken), Buffer.from(req.params.token))) {
      return res.status(401).send("Password reset token is invalid or has expired.")
    }

    const newPassword = await encryptPassword(req.body.newpass)
    console.log(req.body.newpass, newPassword)
    const query = {
      $set: {
        password: newPassword,
        resetPasswordToken: null,
        resetPasswordExpires: null
      }
    }

    updateUser(user, query)
  
    if (email_enabled) {
      const templateData = {
        accountName: `${user.profile.accountName}`
      }
      const templates = await getTemplatesForEmail('PasswordResetConfirmationSubject', 'PasswordResetConfirmationBody', true, templateData)

      const response = await sendEmail(templates['body'], user.email, templates['subject'])
      return res.status(200).send("Email successfully sent")
    }
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
            let newEvent = req.body
      // let newEvent = {
      //  ...data
      // }
      
      const user = await getUser({'profile.accountName': req.user})
      if (user) {
        newEvent.userId = user._id

      
      // for ttl index in posts
      //if ('end-date' in newArticle) {
      //newArticle['end-date'] = new Date(newArticle['end-date']);
      //}
        const db = await MongoClient.connect(dbUrl)
        const dbo = db.db(dbFolder);
        dbo.collection("events").insertOne(newEvent, (err, result)=>{
          if (err) {
            console.log(err)
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
      } else {
        res.status(500).send("User not found")
      }
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
  router.get("/event/all", (req, res) => {
    try {
      MongoClient.connect(dbUrl, async (err, db) => {
        let dbo = db.db(dbFolder);
        const events = await dbo.collection("events").find({}).toArray()
        res.status(200).send(events)
      })
    } catch (ex) {
      res.status(400).send({ error: 'Error while fetching events data' })
      console.log(ex)
    }
  });


  /*****************************************************************************
  * 
  *                                Categories
  *                 
  *****************************************************************************/


  router.post('/categories', upload.array('file', 5), (req, res) => {
    let images = req.files.map(obj => obj.filename);
    let coverImg = images[req.body.coverImgInd];
    images = images.filter((img) => { return img !== coverImg })
    let categories = {
      name: req.body.name,
      defaultImage: images,
      defaultMainImage: coverImg,
      isActive: true
    }
    try {
      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);
        dbo.collection("category").insertOne(categories, (err, result) => {
          if (err) {
            res.status(500).send({ error: err })
            db.close()
          }
          else if (result != null) {
            res.status(200).send("Category added");
            db.close()
          }
        })
      })
    }
    catch (ex) {
      res.status(500).send({ exception: ex });
    }
  })

  router.get("/categories", cacheMiddleware(1), async (req, res) => {
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);
    const result = await dbo.collection("category").find({isActive: true});
    
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

  router.post('/updateCategoryStatus', upload.none(),  (req, res) => {
    console.log(req.body.id)
    try {
      let bodyData = req.body;
      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);
        const query = { "_id": ObjectId(bodyData.id) };
         dbo.collection("category").updateOne(query , {$set: {'isActive': bodyData.isActive.toString() == "true"?true:false}}, (err, result) => {
          if (err) {
            res.status(400).json(false)
            db.close()
          }
          else {
            res.status(200).json(true)
            db.close()
          } 
         })
      })
    }
    catch (ex) {
      console.log(ex)
      res.status(500).send({ exception: ex });
    }
  })

  router.post('/editCategory', upload.array('file', 5), (req, res) => {
    let images = req.files.map(obj => obj.filename);
    let coverImg = images[req.body.coverImgInd];
    images = images.filter((img) => { return img !== coverImg })
    let category = {
      // _id: ObjectId(req.body.id),
      name: req.body.name,
      defaultImage: images,
      defaultMainImage: coverImg,
      isActive: true
    }
    console.log("Category data: ", category)
    try {
      let bodyData = req.body;
      MongoClient.connect(dbUrl, (err, db) => {
        let dbo = db.db(dbFolder);
        const query = { "_id": ObjectId(req.body.id) };
        dbo.collection("category").updateOne(query, { $set: category }, (err, result) => {
          if (err) {
            res.status(400).json(false)
            db.close()
          }
          else {
            res.status(200).json(true)
            db.close()
          }
        })
      })
    }
    catch (ex) {
      console.log(ex)
      res.status(500).send({ exception: ex });
    }
  })

  /*****************************************************************************
    * 
    *                                Places
    *                 
    *****************************************************************************/

  router.post('/places', (req, res) => {
    // Extract the list of names from the request body
    const names = req.body.places

    // Connect to MongoDB
    MongoClient.connect(dbUrl, { useNewUrlParser: true, useUnifiedTopology: true }, (err, db) => {
      if (err) {
        console.error('Error occurred while connecting to MongoDB', err);
        return res.status(500).json({ error: 'Internal Server Error' });
      }

      // Access the database
      const dbo = db.db(dbFolder);

      // Access the collection
      const placesCollection = dbo.collection('place');

      // Insert the names into the collection
      placesCollection.insertMany(names, (err, result) => {
        if (err) {
          console.error('Error occurred while inserting documents into MongoDB', err);
          db.close(); // Close the MongoDB connection
          return res.status(500).json({ error: 'Internal Server Error' });
        }

        if (result.insertedCount === 0) {
          console.error("No new places inserted into database");
          return res.status(400).json({ error: 'No place added to database'})
        }

        console.log('Places names inserted successfully: ', result.insertedCount);
        db.close(); // Close the MongoDB connection
        return res.status(201).json({ message: `${result.insertedCount} names inserted successfully` });
      });
    });
  });

  router.get("/places", cacheMiddleware(24), async (req, res) => {
    MongoClient.connect(dbUrl, { useNewUrlParser: true, useUnifiedTopology: true }, async (err, db) => {
      const dbo = db.db(dbFolder)

      try {
        const results = await dbo.collection("place").find({}).toArray();
        return res.status(200).json(results)
      }
      catch (err) {
        return res.sendStatus(400)
      }
    })
  });
  
    router.post('/deletePlace', (req, res) => {
      // Extract the list of names from the request body
      const placeToDelete = req.body.id

      // Connect to MongoDB
      MongoClient.connect(dbUrl, { useNewUrlParser: true, useUnifiedTopology: true }, (err, db) => {
        if (err) {
          console.error('Error occurred while connecting to MongoDB', err);
          return res.status(500).json({ error: 'Internal Server Error' });
        }

        // Access the database
        const dbo = db.db(dbFolder);

        // Access the collection
        const placesCollection = dbo.collection('place');

        // Delete the names from the collection
        placesCollection.deleteOne({ _id: ObjectId(placeToDelete) }, (err, result) => {
          if (err) {
            console.error('Error occurred while deleting documents from MongoDB', err);
            client.close(); // Close the MongoDB connection
            return res.status(500).json({ error: 'Internal Server Error' });
          }

          if (result.deletedCount === 0) {
            console.log("No place found with the provided ID: ", placeToDelete);
            db.close();
            return res.status(404).json({ error: "Place not found." })
          }
          console.log('Names deleted successfully:', result.deletedCount);
          db.close(); // Close the MongoDB connection
          return res.status(200).json({ message: `Place deleted successfully` });
        });
      });
    });


    /*****************************************************************************
    * 
    *                                Cart
    *                 
    *****************************************************************************/

    router.get("/cart/byUser", (req, res) => {
      try {
        MongoClient.connect(dbUrl, async (err, db) => {
          let dbo = db.db(dbFolder);
          const carts = await dbo.collection("carts").find({ "cartOwner": req.user }).toArray()
          res.status(200).send(carts)
        })
      } catch (ex) {
        res.status(400).send({ error: 'Error while fetching cart data' })
        console.log(ex)
      }
    });

    /*****************************************************************************
   * 
   *                                Transactions
   *                 
   *****************************************************************************/

    router.get("/transactions", (req, res) => {
      try {
        MongoClient.connect(dbUrl, async (err, db) => {
          let dbo = db.db(dbFolder);
          const allUsers = await dbo.collection("users").find({}).toArray(); // created this variables of users because the old implementation loops through all the transactions that it got from the cc-node and to map the users every time it called the get user api but now it gets it from this variable
          const user = await dbo.collection("users").findOne({ "profile.accountName": req.user })
          const userId = user._id.toString()
          let transactions = {
            requests: [],
            pendingPurchases: [],
            completedTransactions: [],
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
              }
            })
            let users = {}
            let entries = response.data.data || []
            for (const entry of entries) {
              if ((entry.entries[0].payee !== undefined) && !(entry.entries[0].payee in users)) {
                const payee = allUsers.filter(us => us["_id"].toString() == entry.entries[0].payee)[0]
                // const payee = await getUser({ '_id': ObjectId(entry.entries[0].payee) })
                users[entry.entries[0].payee] = payee.profile.accountName
              }
              if ((entry.entries[0].payer !== undefined) && !(entry.entries[0].payer in users)) {
                const payer = allUsers.filter(us => us["_id"].toString() == entry.entries[0].payer)[0]
                // const payer = await getUser({ '_id': ObjectId(entry.entries[0].payer) })
                users[entry.entries[0].payer] = payer.profile.accountName
              }
              if ((entry.entries[0].author !== undefined) && !(entry.entries[0].author in users)) {
                const author = allUsers.filter(us => us["_id"].toString() == entry.entries[0].author)[0]
                // const author = await getUser({ '_id': ObjectId(entry.entries[0].author) })
                users[entry.entries[0].author] = author.profile.accountName
              }
              entry.entries[0].payee = users[entry.entries[0].payee]
              entry.entries[0].payer = users[entry.entries[0].payer]
              entry.entries[0].author = users[entry.entries[0].author]
              if (entry.state === 'completed') {
                // this is the new cc-server returns quantity with display format so removing the display format is done below to send integer value to the front end 
                entry.entries[0].quant = cleanQuantityData(entry.entries[0].quant)
                transactions.completedTransactions.push(entry)
              } else if (entry.state === 'pending') {
                entry.entries[0].quant = cleanQuantityData(entry.entries[0].quant)
                transactions.requests.push(entry)
              }
            }
          } catch (error) {
            console.error(error)
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
              }
            })
            let users = {}
            let entries = response.data.data || []
            for (const entry of entries) {
              if ((entry.entries[0].payee !== undefined) && !(entry.entries[0].payee in users)) {
                const payee = allUsers.filter(us => us["_id"].toString() == entry.entries[0].payee)[0]
                // const payee = await getUser({ '_id': ObjectId(entry.entries[0].payee) })
                users[entry.entries[0].payee] = payee.profile.accountName
              }
              if ((entry.entries[0].payer !== undefined) && !(entry.entries[0].payer in users)) {
                const payer = allUsers.filter(us => us["_id"].toString() == entry.entries[0].payer)[0]
                // const payer = await getUser({ '_id': ObjectId(entry.entries[0].payer) })
                users[entry.entries[0].payer] = payer.profile.accountName
              }
              if ((entry.entries[0].author !== undefined) && !(entry.entries[0].author in users)) {
                const author = allUsers.filter(us => us["_id"].toString() == entry.entries[0].author)[0]
                // const author = await getUser({ '_id': ObjectId(entry.entries[0].author) })
                users[entry.entries[0].author] = author.profile.accountName
              }
              entry.entries[0].payee = users[entry.entries[0].payee]
              entry.entries[0].payer = users[entry.entries[0].payer]
              entry.entries[0].author = users[entry.entries[0].author]

              if (entry.state === 'completed') {
                entry.entries[0].quant = cleanQuantityData(entry.entries[0].quant)
                transactions.completedTransactions.push(entry)
              } else if (entry.state === 'pending') {
                entry.entries[0].quant = cleanQuantityData(entry.entries[0].quant)
                transactions.pendingPurchases.push(entry)
              }
            }

          } catch (error) {
            console.error(error)
          }
          res.status(200).send(transactions)
        })
      } catch (ex) {
        res.status(400).send({ error: 'Error while fetching Transactions' })
        console.log(ex)
      }
    });

  
    /*****************************************************************************
    * 
    *                                Article
    *                 
    *****************************************************************************/

    router.get("/articles/all", async (req, res) => {
      try {
        MongoClient.connect(dbUrl, async (err, db) => {
          let dbo = db.db(dbFolder);
          let data = {}
          let userId;
          let user = await dbo.collection("users").findOne({ "profile.accountName": req.user })
          if (user) {
            userId = user._id.toString()
          }
          // get article data
          let articles = await dbo.collection("posts").find({}).toArray()
          const myArticles = []
          const allArticles = []
          for (let article of articles) {
            const articleUser = await dbo.collection("users").findOne({ '_id': article.userId })
            if (articleUser) {
              article.userUploader = articleUser.profile.accountName

              if (userId && article.userId.toString() === userId.toString()) {
                myArticles.push(article)
              }
            }
            const now = new Date()
            const chosenDate = article["end-date"]
            if (now.getTime() < Date.parse(chosenDate)) {
              allArticles.push(article)
            }
          }
          myArticles.sort((a, b) => new Date(b.uploadDate) - new Date(a.uploadDate))
          allArticles.sort((a, b) => new Date(b.uploadDate) - new Date(a.uploadDate))
          data.myArticles = myArticles
          data.allArticles = allArticles
          res.status(200).send(data);
        })
      } catch (ex) {
        console.log(ex.response.data)
        res.status(400).send({ error: 'Error while fetching notifications' })
      }
    });

    router.get("/testemail", async (req, res) => {
      // sending test email api
      try {
        const templates = await getTemplatesForEmail('TestSubject', 'TestBody', false, {})

        const response = await sendEmail(templates['body'], 'yonasbek4@gmail.com', templates['subject'])
      } catch (error) {
        console.log(error)
        res.status(400).send(error)
        return
      }
    });

    router.get("/user/balance", (req, res) => {
      try {
        MongoClient.connect(dbUrl, async (err, db) => {
          let dbo = db.db(dbFolder);
          let user = await dbo.collection("users").findOne({ "profile.accountName": req.user })
          let data = {
            balance: 0,
            creditLimit: 0,
            creditLine: user.min_limit * -1
          }
          if (DISABLE_CC_NODE) {
            return res.status(200).json(balance)
          }
          if (user) {
            const response = await axios.get(CC_NODE_URL + '/account/summary', {
              headers: {
                'cc-user': user._id.toString(),
                'cc-auth': '1'
              }
            })
  
            let user_data = response.data.data[user._id.toString()]
            data.balance = user_data.completed.balance
            data.creditLimit = data.creditLine
            if (data.balance < 0) {
              // reduce credit line *only* if negative balance
              data.creditLine += data.balance
            }
          }
          res.status(200).send(data)
        })
      } catch (ex) {
        res.status(400).send({ error: 'Error while fetching user balance information' })
        console.log(ex)
      }
    });
    router.put('/event/:id', async (req, res) => {
      try {
        const eventId = req.params.id;
        const eventUpdates = req.body;
        MongoClient.connect(dbUrl, async (err, db) => {
          let dbo = db.db(dbFolder);
          const result = await dbo.collection('events').updateOne({ _id: ObjectId(eventId) }, { $set: eventUpdates });
          if (result.modifiedCount === 1) {
            res.status(200).send({ message: 'Event updated successfully' });
          } else {
            res.status(400).send({ error: 'Failed to update event' });
          }
        });
      } catch (ex) {
        console.log(ex);
        res.status(500).send({ error: ex });
      }
    });


    return { 'router': router, 'conn': conn }
  };
