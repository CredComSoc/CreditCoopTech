const express = require('express');
const axios = require('axios').default;
const {MongoClient} = require('mongodb');
const ObjectId = require('mongodb').ObjectId

const CC_NODE_URL = 'http://155.4.159.231/cc-node'


// Routes that make requests to the Credits Common Node on behalf of the user,
// in order to authenticate the user with Passport before any requests to the ccNode is made.

module.exports = async function(dbUrl, dbFolder) {
  const router = express.Router();

  async function getUser(user_query) {
    const db = await MongoClient.connect(dbUrl)
    const dbo = db.db(dbFolder);
    const result = await dbo.collection("users").findOne(user_query)
    db.close()
    return result
  }

  // PAYER MUST ALSO BE AUTHOR OF TRANSACTION
  router.get("/purchases", async (req, res) => {
    const user = await getUser({'profile.accountName': req.user})
    console.log(user._id.toString()) 
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
      console.log(error)
      res.sendStatus(500)
    }
  })

  router.get("/requests", async (req, res) => { 
    const user = await getUser({'profile.accountName': req.user})
    try {
      const response = await axios.get(CC_NODE_URL + '/transactions', { 
      headers: {
       'cc-user': user._id.toString(),
       'cc-auth': '1'
      },
      params: {
        'payee': user._id.toString()
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
      res.sendStatus(500)
    }
  })

  router.post("/createrequest", async (req, res) => {
    const article = req.body
    console.log(article)
    const payer = await getUser({'profile.accountName': req.user})
    const payee = await getUser({'profile.accountName': article.userUploader})
    if (req.user === article.userUploader) {
      res.sendStatus(500)
      return
    }
    let response
    try {
      response = await axios.post(CC_NODE_URL + '/transaction', 
      {
        "payee"       : payee._id.toString(), 
        "payer"       : payer._id.toString(),
        "quant"       : article.quantity * parseInt(article.price),
        "description" : article.article,
        "type"        : "credit",
        "metadata"    : {"id" : article.id, "quantity": article.quantity}
      }, 
      {
        headers: 
        {
          'cc-user': payer._id.toString(),
          'cc-auth': '123'
        }
      })
    } catch (error) {
      console.log(error)
      res.sendStatus(500)
      return
    }
    try {
      await axios.patch(CC_NODE_URL + '/transaction/' + response.data.uuid + '/pending', {}, { 
      headers: {
       'cc-user': payer._id.toString(),
       'cc-auth': '1'
      }})
      res.sendStatus(200)
    } catch (error) {
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
      console.log(error)
      res.sendStatus(500)
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
  
  
  router.get("/saldo", async (req, res) => {
    const user = await getUser({'profile.accountName': req.user}) 
    try {
      const response = await axios.get(CC_NODE_URL + '/account/summary', { 
      headers: {
       'cc-user': user._id.toString(),
       'cc-auth': '1'
      }})
      res.status(200).send(response.data[user._id.toString()])
    } catch (error) {
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
      res.status(200).send(response.data[user._id.toString()])
    } catch (error) {
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
