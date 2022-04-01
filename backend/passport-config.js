const LocalStrategy = require('passport-local')
const {MongoClient} = require('mongodb');
const  url = "mongodb://localhost:27017/"

let asdf;

function initialize (passport) {
  passport.use (new LocalStrategy ({ usernameField: 'username' }, authenticateUser))
  passport.serializeUser((user, done) => {
    asdf = user
    return done(null, user)
  })
  passport.deserializeUser((id, done) => {
    return done(null, asdf)
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
        return done(null, result.userID)
      } 
      else {
        //If we dont find a result
        return done(null, false, {message: 'User Not Found'})
        db.close();
      }
    })
  })
}

module.exports = initialize
