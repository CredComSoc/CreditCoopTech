const express = require('express');
const server = require('./server');

const port = 3000

const app = express()

server.initApp(app, dbFolder="tvitter")

server_instance = server.startServer(app, port)


const cleanup = (event) => { // SIGINT is sent for example when you Ctrl+C a running process from the command line.
    console.log("CLEANUP TEST")
    server.stopServer(server_instance) // close db connections
    process.exit(); // Exit with default success-code '0'.
}
  
process.on('SIGINT', cleanup);
process.on('SIGTERM', cleanup);

