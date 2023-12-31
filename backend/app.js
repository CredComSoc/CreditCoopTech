const express = require('express');
const server = require('./server');
const cors = require('./cors')  

const port = 3000

let app = express()

app.use(cors)

server.initApp(app)

server_instance = server.startServer(app, port)
server.startChat(app)

const cleanup = (event) => { // SIGINT is sent for example when you Ctrl+C a running process from the command line.
    console.log("\nCLEANUP TEST")
    server.stopServer(server_instance) // close db connections
    process.exit(); // Exit with default success-code '0'.
}
  
process.on('SIGINT', cleanup);
process.on('SIGTERM', cleanup);

