const express = require('express');
const passport = require('passport')
const session = require('cookie-session')
// const path = require('path');
// const { v1: uuidv1, v4: uuidv4 } = require('uuid');

const app = express();

const corsMiddleware = require('./cors');
app.options('*', corsMiddleware);
app.use(corsMiddleware);

const logger = require('morgan');
app.use(logger('dev'));

app.use(express.json());
app.use(express.urlencoded({ extended: false }));

const cookieParser = require("cookie-parser");
app.use(cookieParser())

app.use(session({
  name: 'session',
  secret: 'asf',  // BYT UT
  secure: false,

  // Cookie Options
  maxAge: 24 * 60 * 60 * 1000 // 24 hours
}))

const initializePassport = require('./passport-config')
initializePassport(passport)
app.use(passport.initialize())
app.use(passport.session())

const indexRouter = require('./routes/index')
const ccRequests = require('./routes/ccRequests')
const ccUserStore = require('./routes/ccUserStore')
app.use('/', indexRouter)
app.use('/', ccRequests)
app.use('/', ccUserStore)

function startServer(port) {
  let server = app.listen(3000, () => {
    let host = server.address().address
    let port = server.address().port
    console.log(`Listening to http://${host}:${port}`)
   })
  return(server)
}


startServer()

module.exports = startServer;
