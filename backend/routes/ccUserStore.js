const express = require('express');
const {MongoClient} = require('mongodb');

// Routes required by the Credits Common Node
// https://gitlab.com/credit-commons-software-stack/cc-node/-/blob/master/AccountStore/accountstore.openapi.yml


module.exports = function(dbUrl, dbFolder) {
  const router = express.Router();

  router.get("/filter/full", (req, res) => { 
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
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
              "admin"   : user.is_admin
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
  
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db("tvitter");
      dbo.collection(dbFolder).find({}).toArray(function(err, result) {
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
  
  router.get("/creds/:name/:auth", (req, res) => {
    res.sendStatus(200)
    /* let myquery = { userID : req.params.name, sessionID : req.params.auth}
    MongoClient.connect(url, (err, db) => {
      let dbo = db.db("tvitter");
      dbo.collection("users").findOne(myquery, function(err, result) {
        if (err) {
          res.sendStatus(500)
          db.close();
        }
        else if (result != null) {
          res.sendStatus(200)
          db.close();
        }
        else {
          // If we dont find a result
          res.status(400).send("The account doesn't exist.")
          db.close();      
        }
      })
    }) */
  })
  
  router.get("/:acc_id", (req, res) => {
    let myquery = { userID: req.params.acc_id}
  
    MongoClient.connect(dbUrl, (err, db) => {
      let dbo = db.db(dbFolder);
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
            "admin"   : result.is_admin
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

  return router
}





