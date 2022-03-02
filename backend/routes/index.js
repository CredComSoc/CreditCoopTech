var express = require('express');
const app = express();
var router = express.Router();
const {MongoClient} = require('mongodb');
const { 
    v1: uuidv1,
    v4: uuidv4,
  } = require('uuid');  

let url = "mongodb://localhost:27017/"
app.use(express.json());
app.use(express.urlencoded({ extended: false }));


// Test Route
router.get("/", (req, res) => {
  res.status(200).send("Hello There ;)")
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
          let newUser = {userID: username, password: pw, email: mail, sessionID: null, min_limit: min, max_limit: max, is_active: active, is_admin: admin, posts: []}
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
