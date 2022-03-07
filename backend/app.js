const express = require('express');
const bodyParser = require('body-parser');
const path = require('path');
const crypto = require('crypto');
const mongoose = require('mongoose');
const multer = require('multer');
const {GridFsStorage} = require('multer-gridfs-storage');
const Grid = require('gridfs-stream');
const methodOverride = require('method-override');

let indexRouter = require('./routes/index')

const app = express();


// Middleware
app.use(bodyParser.json());
app.use(methodOverride('_method'));
app.set('view engine', 'jade');

app.use('/', indexRouter)

const port = 5000;

app.listen(port, () => console.log(`Server started on port ${port}`));
