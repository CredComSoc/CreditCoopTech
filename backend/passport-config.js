const LocalStrategy = require('passport-local')
const {MongoClient} = require('mongodb');
const  url = "mongodb+srv://sb:sb-password@cluster0.i2vzq.mongodb.net/myFirstDatabase?retryWrites=true&w=majority"

function initialize (passport) {
  passport.use (new LocalStrategy (authenticateUser))
  passport.serializeUser((user, done) => {
    return done(null, user._id)
  })
  passport.deserializeUser((id, done) => {
    return done(null, getUser(id))
  })
}

async function authenticateUser (username, password, done) {
  const  myquery = { userID: username, password: password}
  MongoClient.connect(url, (err, db) => {
    const  dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        return done(err)
      } 
      else if (result != null) {
        
        db.close()
        return done(null, result)
      } 
      else {
        return done(null, false, {message: 'User Not Found'})
        db.close();
      }
    })
  })
}

async function getUser(id) {
  const  myquery = { _id: id}
  MongoClient.connect(url, (err, db) => {
    const  dbo = db.db("tvitter");
    dbo.collection("users").findOne(myquery, function(err, result) {
      if (err) {
        return false
      } 
      else if (result != null) {
        
        db.close()
        return result.userID
      } 
      else {
        return false
        db.close();
      }
    })
  })
}

module.exports = initialize
