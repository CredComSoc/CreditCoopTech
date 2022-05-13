const express = require('express');
const passport = require('passport')
const session = require('cookie-session')
const dbConfig = require('./mongoDB-config');
//const { Socket } = require('socket.io');
// const path = require('path');
// const { v1: uuidv1, v4: uuidv4 } = require('uuid');

function initApp(app, dbUrl = dbConfig.mongoURL, dbFolder=dbConfig.dbFolder) {
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
  
  const initializePassport = require('./passport-config') // (dbUrl, dbFolder)
  initializePassport(passport)
  app.use(passport.initialize())
  app.use(passport.session())

  const indexRouter = require('./routes/index')(dbUrl, dbFolder)
  const ccRequests = require('./routes/ccRequests')(dbUrl)
  const ccUserStore = require('./routes/ccUserStore')(dbUrl, dbFolder)
  app.use('/', indexRouter)
  app.use('/', ccRequests)
  app.use('/', ccUserStore)
}

function startServer(app, port) {
  let server = app.listen(port, () => {
    let host = server.address().address
    let port = server.address().port
    console.log(`Listening to http://${host}:${port}`)
   })
  return(server)
}

function stopServer(server) {
  server.close()
  console.log(`Server stopped`)
}

function startChat(app) {
  const http = require('http').createServer(app);

  const io = require('socket.io')(http, {
    cors: {
      origins: ['http://localhost:8080']
    }
  });

  io.on('connection', (socket) => {
    console.log('a user connected');

    socket.on("join", (roomId) => {
      socket.join(roomId);
      console.log(`User with ID: ${socket.id} joined room: ${roomId}`)
      
    })

    socket.on('message', (msg) => {
      socket.to(msg.id).emit('message', msg);
      const { storeChatMsg } = require('./routes/chatFunctions');
      const chatID = msg.id;
      delete msg.id;
      storeChatMsg(chatID, msg)
    });

    socket.on('disconnect', () => {
      console.log('user disconnected');
    });

    /*

    socket.on('message', (msg) => {
        console.log('message: ' + msg.sender);
        socket.broadcast.emit('broadcast', `server: ${msg.message}`);
      });*/

  });
  
  http.listen(3001, () => {
    console.log('listening on *:3001');
  });
}


module.exports = { initApp, startServer, stopServer, startChat}
