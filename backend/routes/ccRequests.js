const express = require('express');
const axios = require('axios').default;
const { MongoClient } = require('mongodb');
const ObjectId = require('mongodb').ObjectId
const config = require('../config')
const CC_NODE_URL = config.CC_NODE_URL;

// Routes that make requests to the Credits Common Node on behalf of the user,
// in order to authenticate the user with Passport before any requests to the ccNode is made.

module.exports = function() {
  
  const router = express.Router();
  const mongoURL = config.mongoURL;
  
  async function getUser(user_query) {
    const client = await MongoClient.connect(mongoURL)
    try {
      const dbo = client.db();
      return await dbo.collection("users").findOne(user_query)
    }
    finally {
      client.close()
    }
  }

  // PAYER MUST ALSO BE AUTHOR OF TRANSACTION
  router.get("/purchases", async (req, res) => {
    const user = await getUser({'profile.accountName': req.user})
    try {
      const response = await axios.get(CC_NODE_URL + '/transactions', { 
      headers: {
      'cc-user': user._id.toString(),
      'cc-auth': '1'
      },
      params: {
        'payer': user._id.toString()
      }})
      let userNames = {}
      for (const entry of response.data) {
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
        entry.entries[0].payee = userNames[entry.entries[0].payee]
        entry.entries[0].payer = userNames[entry.entries[0].payer]
        entry.entries[0].author = userNames[entry.entries[0].author]
      }
      res.status(200).send(response.data)
    } catch (error) {
      console.error(error)
      res.sendStatus(500)
    }
  })

  router.post("/createrequest", async (req, res) => {
    
    const article = req.body
    const payer = await getUser({'profile.accountName': req.user})
    const payee = await getUser({'profile.accountName': article.userUploader})
    if (req.user === article.userUploader) {
      res.sendStatus(500)
      return
    }

    auth_header =   
    {
      'cc-user': payer._id.toString(),
      'cc-auth': '123' //FIXME1 auth is ignored anyway
    }

    // create transaction
    const transaction_url = CC_NODE_URL + '/transaction'
    let transaction_uuid = null
    try {      
      payload = {
        "payee"       : payee._id.toString(), 
        "payer"       : payer._id.toString(),
        "quant"       : article.quantity * parseInt(article.price),
        "description" : article.article,
        "type"        : "credit",
        "metadata"    : {"id" : article.id, "quantity": article.quantity}
      }
      const response = await axios.post(transaction_url, payload, { headers: auth_header })
      console.log(response)
      transaction_uuid = response.data.data.uuid 

    } catch (error) {
      console.error("Error sending transaction to " + transaction_url)
      console.error(error)
      res.sendStatus(500)
      return
    }
 
    // update status to 'pending'
    try {
      patch_url = CC_NODE_URL + '/transaction/' + transaction_uuid + '/pending'
      response = await axios.patch(patch_url, {}, { headers: auth_header})
      res.sendStatus(200)
    } catch (error) {
      console.error("Error connecting with " + patch_url)
      console.error(error)
      res.sendStatus(500)
    }
  })

  router.post("/cancelrequest", async (req, res) => {
    const user = await getUser({'profile.accountName': req.user})
    const transactionId = req.body
    try {
      const response = await axios.patch(CC_NODE_URL + '/transaction/' + transactionId.uuid + '/erased', {}, { 
      headers: {
       'cc-user': user._id.toString(),
       'cc-auth': '1'
      }})
      res.status(200).send(response.data)
    } catch (error) {
      console.error(error.response.data)
      res.status(400).send(error.response.data)
    }
  })
  
  router.post("/acceptrequest", async (req, res) => {
    const user = await getUser({'profile.accountName': req.user})
    const transactionId = req.body
    try {
      const response = await axios.patch(CC_NODE_URL + '/transaction/' + transactionId.uuid + '/completed', {}, { 
      headers: {
       'cc-user': user._id.toString(),
       'cc-auth': '1'
      }})
      res.status(200).send(response.data)
    } catch (error) {
      res.status(500).send(error)
    }
  })
  
  //uses axios.get with data cc-user set to current user and cc-auth =1 to get the saldo of the user from the cc-node.
  router.get("/saldo", async (req, res) => {
    const user = await getUser({'profile.accountName': req.user}) 
    try {
      const response = await axios.get(CC_NODE_URL + '/account/summary', { 
      headers: {
       'cc-user': user._id.toString(),
       'cc-auth': '1'
      }})
      res.status(200).send(response.data.data[user._id.toString()])
    } catch (error) {
      // TODO While catching an error shouldn't respond default values this will lead to miss calculations and error on the create transaction api of the cc-server
      res.status(200).send({
        completed: {
          balance: 0,
          trades: 0,
          entries: 0,
          gross_in: 0,
          gross_out: 0,
          partners: 0,
          volume: 0
        },
        pending: {
          balance: 0,
          trades: 0,
          entries: 0,
          gross_in: 0,
          gross_out: 0,
          partners: 0,
          volume: 0
        }
      })
    }
  })

  router.post("/saldo", async (req, res) => {
    const user = await getUser({'profile.accountName': req.body.user}) 
    try {
      const response = await axios.get(CC_NODE_URL + '/account/summary', { 
      headers: {
       'cc-user': user._id.toString(),
       'cc-auth': '1'
      }})
      res.status(200).send(response.data.data[user._id.toString()])
    } catch (error) {
      // TODO While catching an error shouldn't respond default values this will lead to miss calculations and error on the create transaction api of the cc-server
      res.status(200).send({
        completed: {
          balance: 0,
          trades: 0,
          entries: 0,
          gross_in: 0,
          gross_out: 0,
          partners: 0,
          volume: 0
        },
        pending: {
          balance: 0,
          trades: 0,
          entries: 0,
          gross_in: 0,
          gross_out: 0,
          partners: 0,
          volume: 0
        }
      })
    }
  })
    
  return router
}
