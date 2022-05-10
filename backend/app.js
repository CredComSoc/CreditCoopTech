const express = require('express');
const server = require('./server');

const port = 3000

const app = express()

server.initApp(app)

server.startChat(app)

server.startServer(app, port)



