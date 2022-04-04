const LocalStrategy = require('passport-local')
const {MongoClient} = require('mongodb');
const  url = "mongodb://localhost:27017/"

let asdf = 'TestAdmin';

function initialize (passport) {
  passport.use (new LocalStrategy (authenticateUser))
  passport.serializeUser((user, done) => {
    console.log(user.userID)
    // asdf = user.userID
    return done(null, asdf)
  })
  passport.deserializeUser((id, done) => {
    console.log("sdfsdf")
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
        return done(null, result)
      } 
      else {
        return done(null, false, {message: 'User Not Found'})
        db.close();
      }
    })
  })
}

module.exports = initialize
