var express = require('express');
var path = require('path');
var logger = require('morgan');

const { 
    v1: uuidv1,
    v4: uuidv4,
  } = require('uuid');
  

var indexRouter = require('./routes/index');
var ccRouter = require('./routes/cc_routes')

var app = express();

const corsMiddleware = require('./cors');
app.options('*', corsMiddleware);
app.use(corsMiddleware);

// view engine setup
//app.set('views', path.join(__dirname, 'views'));
//app.set('view engine', 'jade');

app.use(logger('dev'));
app.use(express.json());
app.use(express.urlencoded({ extended: false }));
//app.use(express.static(path.join(__dirname, 'public')));

app.use('/', indexRouter);
app.use('/', ccRouter)


const {MongoClient} = require('mongodb');
let url = "mongodb://localhost:27017/";


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
