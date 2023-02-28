const LocalStrategy = require('passport-local')
const mongoose = require('mongoose');
const config = require('./config')
const { MongoClient } = require("mongodb");

  

  const mongoURL = config.mongoURL;
  
  async function authenticateUser (email, password, done) {
    const client = new MongoClient(mongoURL);
    try {

      await client.connect();
      const result = await client.db().collection("users").findOne({ email, password });

      if (result != null) {
        return done(null, result);
      } else {
        return done(null, false, { message: 'User not found' });
      }

    } catch (err) {
      return done(err);
    } finally {
      await client.close();
    }
  }
  
  async function getUser(id) {
    const client = new MongoClient(mongoURL);
    try {
      await client.connect();
      const user = await client.db().collection("users").findOne({ _id: id });
      return user
    } finally {
      await client.close();
    }
  }

  function initialize (passport) {
    passport.use (new LocalStrategy ({usernameField: 'email'}, authenticateUser))
    passport.serializeUser((user, done) => {
      return done(null, user._id)
    })
    passport.deserializeUser((id, done) => {
      getUser(mongoose.Types.ObjectId(id)).then((user) => {
        return done(null, user.profile.accountName)
      }).catch((err) => {
        return done(err)
      })
    }) 
  }

module.exports = { initialize }


