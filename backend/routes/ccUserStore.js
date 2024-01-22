const express = require('express');
const ObjectId = require('mongodb').ObjectId
const config = require('../config')
const { MongoClient } = require("mongodb");
const cacheMiddleware = require('../middlewares/cacheMiddleware');


// Routes required by the Credits Common Node
// https://gitlab.com/credit-commons-software-stack/cc-node/-/blob/master/AccountStore/accountstore.openapi.yml


module.exports = function() {
  
  const mongoURL = config.mongoURL;
  const router = express.Router();

  router.head("/:acc_id", cacheMiddleware(24), async (req, res) => {

    const client = new MongoClient(mongoURL);
    try {
      
      await client.connect();
      let myquery = { '_id': ObjectId(req.params.acc_id)}
      const result = await client.db().collection("users").findOne(myquery);
      if (result != null) {
        res.status(200).send()
      }
      else {
        res.status(404).send()
      }

    }
    catch(err) {
      console.error("Error during account lookup:", res, err);
      // this used to be a 404, but that's not correct
      // .. unless cc-node expects a 404 for some reason?
      // FIXME Confirm with cc-node devs aka Mats
      res.status(500).send()
    }
    finally {
      client.close()
    }

  })


  router.get("/filter/full", async (req, res) => {
    const client = new MongoClient(mongoURL);
    try {

      await client.connect();
      const users = await client.db().collection("users").find({}).toArray();

      let userArray = []
      if (users != null) {
        for (user of users) {
          userArray.push({
            "id": user._id.toString(),
            "min": user.min_limit,
            "max": user.max_limit,
            "admin": user.is_admin
          });
        }
        res.status(200).send(userArray)
      }
      else {
        res.status(200).send([])
      }

    }
    catch (err) {
      console.error("Error during user lookup:", res, err);
      res.status(500).send(err)
    }
    finally {
      client.close()
    }

  }) 
    
  router.get("/filter", async (req, res) => {
    const client = new MongoClient(mongoURL);
    try {

      await client.connect();
      const result = await client.db().collection("users").find({}).toArray();
      let userArray = []
      if (result != null) {
        //FIXME this doesnt do a filter, wtf?
        result.forEach(user => userArray.push(user._id.toString()))
      }
      res.status(200).send(userArray)
  
    }
    catch (err) {
      console.error("Error during user lookup:", res, err);
      res.status(500).send()
    }
    finally {
      client.close()
    }
  })
  
  router.get("/creds/:name/:auth", (req, res) => {
    //FIXME not implemented
    res.status(200).send()
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
  
  router.get("/:acc_id", cacheMiddleware(24), async (req, res) => {

    const client = new MongoClient(mongoURL);
    try {
      
      await client.connect();
      let myquery = { '_id': ObjectId(req.params.acc_id)}
      const result = await client.db().collection("users").findOne(myquery);
      if (result != null) {
        let userData = {
          "id"      : result._id.toString(),
          "status"  : result.is_active,
          "min"     : result.min_limit,
          "max"     : result.max_limit,
          "admin"   : result.is_admin
        }
        // The CC-server verifies the user through this endpoint
        res.status(200).send(userData)     
      }
      else {
        res.status(404).send()
      }

    }
    catch(err) {
      console.error("Error during account lookup:", res, err);
      res.status(500).send(err)
    }
    finally {
      client.close()
    }
  })

  return router
}





