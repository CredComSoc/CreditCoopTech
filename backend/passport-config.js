const LocalStrategy = require('passport-local')
const mongoose = require('mongoose');
const bcrypt = require('bcrypt');
const config = require('./config')
const { MongoClient } = require("mongodb");

  

  const mongoURL = config.mongoURL;
  
  async function authenticateUser (email, password, done) {
    const client = new MongoClient(mongoURL);
    try {

      await client.connect();
      const emailLower = email.toLowerCase();
      const result = await client.db().collection("users").findOne({ emailLower, password });

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

  async function getUserByUsername(username) {
    const client = new MongoClient(mongoURL);
    try {
      await client.connect();
      const user = await client.db().collection("users").findOne({ email: username });
      return user
    } finally {
      await client.close();
    }
  }

  async function comparePassword(password, encryptedPassword) {
    try {
      const match = await bcrypt.compare(password, encryptedPassword);
      return match;
    } catch (error) {
      throw error;
    }
  }

  function initialize (passport) {
    passport.use(new LocalStrategy( {usernameField: 'email'},
      async (username, password, done) => {
        try {
          const email = username.toLowerCase();
          const user = await getUserByUsername(email);

          if (!user) {
            return done(null, false, { message: 'Incorrect username.' });
          }

          const passwordMatch = await bcrypt.compare(password, user.password);

          if (!passwordMatch) {
            return done(null, false, { message: 'Incorrect password.' });
          }

          return done(null, user);
        } catch (error) {
          console.log(error)
          return done(error);
        }
      }
    ))

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


