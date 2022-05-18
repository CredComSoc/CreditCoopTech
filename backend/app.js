const express = require('express');
const server = require('./server');

const port = 3000

let app = express()

server.initApp(app)

app = server.startServer(app, port)

server.startChat(app)

