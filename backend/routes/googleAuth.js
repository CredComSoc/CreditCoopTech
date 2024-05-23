// index.js
const express = require('express');
const  { OAuth2Client  }  = require('google-auth-library');
const axios = require('axios');
// require('dotenv').config();
const config = require('../config')

const FRONTEND_URI = config.FRONT_END_URL + '/add/article';
const client = new OAuth2Client(
  config.GOOGLE_CLIENT_ID,
  config.GOOGLE_CLIENT_SECRET,
  `http://localhost:3000/auth/google/callback`
);


module.exports = function () {

  const router = express.Router();
  // Redirect to Google's OAuth 2.0 server
 
  router.get('/auth/google', (req, res) => {
    try {
      const url = client.generateAuthUrl({
        access_type: 'offline',
        scope: 'https://www.googleapis.com/auth/photoslibrary.readonly',
      });
      const resp = {'url': url}
      return res.json(resp)
      // console.log(url)
      //  res.redirect(url);
    }
    catch(ex) {
      console.log(ex)
    }
  
  });

  // Handle the OAuth 2.0 server response
  router.get('/auth/google/callback', async (req, res) => {
    const { tokens } = await client.getToken(req.query.code);
    client.setCredentials(tokens);
    console.log(tokens.access_token)
    req.session.token = tokens.access_token;

    res.redirect(FRONTEND_URI);
  });
  router.get('/google/token', async (req, res) => {
    console.log('here')
    const googleAuthToken = req.session.token;
    if(googleAuthToken == null) {
      
      // console.log('null here')
      // refresh_token = client.credentials.refresh_token;
      // console.log('referesh token',refresh_token)
      // await client.refreshAccessToken(refresh_token);
    }
    console.log(googleAuthToken, 'google auth token')
    
    res.status(200).send({token:googleAuthToken})
  });
return router;
}