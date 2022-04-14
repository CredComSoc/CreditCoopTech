const LocalStrategy = require('passport-local')
const {MongoClient} = require('mongodb');
const mongoose = require('mongoose');
const  url = "mongodb+srv://sb:sb-password@cluster0.i2vzq.mongodb.net/myFirstDatabase?retryWrites=true&w=majority"

let asdf = 'TestAdmin';

function initialize (passport) {
  passport.use (new LocalStrategy (authenticateUser))
  passport.serializeUser((user, done) => {
    return done(null, user._id)
  })
  passport.deserializeUser((id, done) => {
    getUser(mongoose.Types.ObjectId(id)).then((user) => {
      return done(null, user.userID)
    })
    
  })
}

async function authenticateUser (username, password, done) {
  const  myquery = { userID: username, password: password}
  MongoClient.connect(url, (err, db) => {
    const  dbo = db.db("tvitter");
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
  const dbo = db.db("tvitter");
  const user = await dbo.collection("users").findOne(myquery)
  db.close();
  return user
}

module.exports = initialize
