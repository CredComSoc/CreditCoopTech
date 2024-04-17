const express = require('express');
const passport = require('passport')
const session = require('cookie-session');
const { CORS_WHITE_LIST } = require('./config');

// const { Socket } = require('socket.io');
// const path = require('path');
// const { v1: uuidv1, v4: uuidv4 } = require('uuid');

let indexRouter

function initApp(app) {
 
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
  
  app.use(passport.initialize())
  app.use(passport.session())
  require('./passport-config').initialize(passport)

  indexRouter = require('./routes/index')()
  const ccRequests = require('./routes/ccRequests')()
  const ccUserStore = require('./routes/ccUserStore')()
  const googleAuth = require('./routes/googleAuth')()

  app.use('/', indexRouter.router)
  app.use('/', ccRequests)
  app.use('/', ccUserStore)
  app.use('/', googleAuth)
}

function startServer(app, port) {
  let server = app.listen(port, () => {
    let host = server.address().address
    console.log(host);
    let port = server.address().port
    console.log(`Listening to http://${host}:${port}`)
  })
  return(server)
}

function stopServer(server) {
  indexRouter.conn.close()
  server.close()
  console.log(`Server stopped`)
}

function startChat(app) {
  const http = require('http').createServer(app);
  const io = require('socket.io')(http,{
    cors : {
      origin: CORS_WHITE_LIST
    }
  });
  
  // FIXME: have to do this in the apache conf for some reason?
  // io.use(cors)

  io.on('connection', (socket) => {
    console.log('a user connected');

    socket.on("join", (chatRoom) => {
      socket.join(chatRoom.chatID);
      const { markNotification } = require('./routes/chatFunctions');
      markNotification(chatRoom.chatID, chatRoom.user);
      console.log(`User with ID: ${socket.id} joined room: ${chatRoom.chatID}`)
    })

    socket.on('message', (msg) => {
      socket.to(msg.id).emit('message', msg);
      const { storeChatMsg } = require('./routes/chatFunctions');
      const chatID = msg.id;
      delete msg.id;
      if (io.sockets.adapter.rooms.get(chatID).size === 1) {
        const notification = {
          date: new Date(),
          type: 'chatMessage',
          toUser: msg.reciever,
          fromUser: msg.sender,
          seen: false,
          chatID: chatID
        } 
        const { storeNotification } = require('./routes/chatFunctions');
        storeNotification(notification);
      }
      
      storeChatMsg(chatID, msg)
    });

    socket.on('leave', (data) => {
      console.log(`User ${socket.id} disconnected.`)
      socket.leave(data);
    });

    socket.on('disconnect', () => {
      console.log('user disconnected');
    });
  });
  
  http.listen(3001, () => {
    console.log('listening on *:3001');
  });
}


module.exports = { initApp, startServer, stopServer, startChat}
