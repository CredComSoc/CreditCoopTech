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
