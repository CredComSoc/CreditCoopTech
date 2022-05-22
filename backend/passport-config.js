const LocalStrategy = require('passport-local')
const {MongoClient} = require('mongodb');
const mongoose = require('mongoose');
const dbConfig = require('./mongoDB-config')

module.exports = function(dbFolder, localUrl = false) {
  const url = dbConfig.mongoURL(dbFolder, localUrl)

  async function authenticateUser (email, password, done) {
    const  myquery = { email: email, password: password}
    MongoClient.connect(url, (err, db) => {
      const  dbo = db.db(dbFolder);
      dbo.collection("users").findOne(myquery, function(err, result) {
        if (err) {
          db.close()
          return done(err)
        } 
        else if (result != null) {
          db.close()
          return done(null, result)
        } 
        else {
          db.close();
          return done(null, false, {message: 'User Not Found'})
        }
      })
    })
  }
  
  async function getUser(id) {
    const myquery = { _id: id}
    const db = await MongoClient.connect(url)
    const dbo = db.db(dbFolder);
    const user = await dbo.collection("users").findOne(myquery)
    db.close();
    return user
  }

  return function initialize (passport) {
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
}


