const express = require('express');
const axios = require('axios').default;


const CC_NODE_URL = 'http://155.4.159.231/cc-node'


// Routes that make requests to the Credits Common Node on behalf of the user,
// in order to authenticate the user with Passport before any requests to the ccNode is made.

module.exports = function(dbUrl) {
  const router = express.Router();

  // PAYER MUST ALSO BE AUTHOR OF TRANSACTION
  router.get("/purchases", async (req, res) => { 
    try {
      const response = await axios.get(CC_NODE_URL + '/transaction/full', { 
      headers: {
      'cc-user': req.user,
      'cc-auth': '1'
      },
      params: {
        'payer': req.user
      }})
      res.status(200).send(response.data)
    } catch (error) {
      res.sendStatus(500)
    }
  })

  router.get("/requests", async (req, res) => { 
    try {
      const response = await axios.get(CC_NODE_URL + '/transaction/full', { 
      headers: {
       'cc-user': req.user,
       'cc-auth': '1'
      },
      params: {
        'payee': req.user
      }})
      res.status(200).send(response.data)
    } catch (error) {
      res.sendStatus(500)
    }
  })

  router.post("/createrequest", async (req, res) => {
    const article = req.body
    console.log(article)
    if (req.user == article.userUploader) {
      res.sendStatus(500)
      return
    }
    let response
    try {
      response = await axios.post(CC_NODE_URL + '/transaction', 
      {
        "payee"       : article.userUploader, 
        "payer"       : req.user,
        "quant"       : article.quantity * parseInt(article.price),
        "description" : article.article,
        "type"        : "credit",
        "metadata"    : {"id" : article.id, "quantity": article.quantity}
      }, 
      {
        headers: 
        {
          'cc-user': req.user,
          'cc-auth': '123'
        }
      })
    } catch (error) {
      console.log(error)
      res.sendStatus(500)
    }
    console.log(response.data)
    try {
      await axios.patch(CC_NODE_URL + '/transaction/' + response.data.uuid + '/pending', {}, { 
      headers: {
       'cc-user': req.user,
       'cc-auth': '1'
      }})
      res.sendStatus(200)
    } catch (error) {
      console.log(error)
      res.sendStatus(500)
    } 
  })

  router.post("/cancelrequest", async (req, res) => {
    const transactionId = req.body
    try {
      const response = await axios.patch(CC_NODE_URL + '/transaction/' + transactionId.uuid + '/erased', {}, { 
      headers: {
       'cc-user': req.user,
       'cc-auth': '1'
      }})
      res.status(200).send(response.data)
    } catch (error) {
      console.log(error)
      res.sendStatus(500)
    }
  })
  
  router.post("/acceptrequest", async (req, res) => {
    const transactionId = req.body
    try {
      const response = await axios.patch(CC_NODE_URL + '/transaction/' + transactionId.uuid + '/completed', {}, { 
      headers: {
       'cc-user': req.user,
       'cc-auth': '1'
      }})
      res.status(200).send(response.data)
    } catch (error) {
      console.log(error)
      res.sendStatus(500)
    }
  })
  
  
  router.get("/saldo", async (req, res) => { 
    try {
      const response = await axios.get(CC_NODE_URL + '/account/summary/' + req.user, { 
      headers: {
       'cc-user': req.user,
       'cc-auth': '1'
      }})
      res.status(200).send(response.data)
    } catch (error) {
      console.log(error)
      res.sendStatus(500)
    }
  })
    
  return router
}
