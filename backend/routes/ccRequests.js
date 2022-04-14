const express = require('express');
const app = express();
const router = express.Router();
const axios = require('axios').default;


const CC_NODE_URL = 'http://155.4.159.231/cc-node'


// Routes that make requests to the Credits Common Node on behalf of the user,
// in order to authenticate the user with Passport before any requests to the ccNode is made.


router.get("/transactions", async (req, res) => { 
  try {
    const response = await axios.get(CC_NODE_URL + '/transaction/full', { 
    headers: {
     'cc-user': req.user,
     'cc-auth': '1'
    }})
    res.status(200).send(response.data)
  } catch (error) {
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
  


module.exports = router;