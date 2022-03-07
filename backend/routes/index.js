const express = require('express');
let app = express();
var router = express.Router();
const bodyParser = require('body-parser');
const path = require('path');
const crypto = require('crypto');
const mongoose = require('mongoose');
const multer = require('multer');
const {GridFsStorage} = require('multer-gridfs-storage');
const Grid = require('gridfs-stream');
const methodOverride = require('method-override');

// Mongo URI
const mongoURI = 'mongodb://localhost:27017/Test';

// Create mongo connection
const conn = mongoose.createConnection(mongoURI);

// Init gfs
let gfs;

conn.once('open', () => {
  // Init stream
  gfs = Grid(conn.db, mongoose.mongo);
  gfs.collection('uploads');
});

// Create storage engine
const storage = new GridFsStorage({
  url: mongoURI,
  file: (req, file) => {
    return new Promise((resolve, reject) => {
      crypto.randomBytes(16, (err, buf) => {
        if (err) {
          return reject(err);
        }
        const filename = buf.toString('hex') + path.extname(file.originalname);
        const fileInfo = {
          filename: filename,
          bucketName: 'uploads'
        };
        resolve(fileInfo);
      });
    });
  }
});
const upload = multer({ storage });

// @route GET /
// @desc Loads form
router.get('/', (req, res) => {
  gfs.files.find().toArray((err, files) => {
    // Check if files
    if (!files || files.length === 0) {
      res.render('index', { files: false });
    } else {
      files.map(file => {
        if (
          file.contentType === 'image/jpeg' ||
          file.contentType === 'image/png'
        ) {
          file.isImage = true;
        } else {
          file.isImage = false;
        }
      });
      res.render('index', { files: files });
    }
  });
});

// @route POST /upload
// @desc  Uploads file to DB
router.post('/upload', upload.single('file'), (req, res) => {
  // res.json({ file: req.file });

  res.redirect('/');
});

// @route GET /files
// @desc  Display all files in JSON
router.get('/files', (req, res) => {
  gfs.files.find().toArray((err, files) => {
    // Check if files
    if (!files || files.length === 0) {
      return res.status(404).json({
        err: 'No files exist'
      });
    }

    // Files exist
    return res.json(files);
  });
});

// @route GET /files/:filename
// @desc  Display single file object
router.get('/files/:filename', (req, res) => {
  gfs.files.findOne({ filename: req.params.filename }, (err, file) => {
    // Check if file
    if (!file || file.length === 0) {
      return res.status(404).json({
        err: 'No file exists'
      });
    }
    // File exists
    return res.json(file);
  });
});

// @route GET /image/:filename
// @desc Display Image
router.get('/image/:filename', (req, res) => {
  gfs.files.findOne({ filename: req.params.filename }, (err, file) => {
    // Check if file
    if (!file || file.length === 0) {
      return res.status(404).json({
        err: 'No file exists'
      });
    }

    // Check if image
    if (file.contentType === 'image/jpeg' || file.contentType === 'image/png') {
      // Read output to browser
      const readstream = gfs.createReadStream(file.filename);
      readstream.pipe(res);
    } else {
      res.status(404).json({
        err: 'Not an image'
      });
    }
  });
});

// @route DELETE /files/:id
// @desc  Delete file
router.delete('/files/:id', (req, res) => {
  gfs.remove({ _id: req.params.id, root: 'uploads' }, (err, gridStore) => {
    if (err) {
      return res.status(404).json({ err: err });
    }

    res.redirect('/');
  });
});

module.exports = router;

// // Init gfs
// let gfs;
// const mongoURI = 'mongodb://localhost:27017/Test';


// const conn = mongoose.createConnection(mongoURI, { useUnifiedTopology: true });

// conn.once('open', () => {
//   // Init stream
//   gfs = Grid(conn.db, mongoose.mongo);
//   gfs.collection('uploads');
// });


// // @route GET /
// // @desc Loads form
// app.get('/', (req, res) => {
//     gfs.files.find().toArray((err, files) => {
//       // Check if files
//       if (!files || files.length === 0) {
//         res.render('index', { files: false });
//       } else {
//         files.map(file => {
//           if (
//             file.contentType === 'image/jpeg' ||
//             file.contentType === 'image/png'
//           ) {
//             file.isImage = true;
//           } else {
//             file.isImage = false;
//           }
//         });
//         res.render('index', { files: files });
//       }
//     });
//   });

//   const storage = new GridFsStorage({
//     url: mongoURI,
//     file: (req, file) => {
//       return new Promise((resolve, reject) => {
//         crypto.randomBytes(16, (err, buf) => {
//           if (err) {
//             return reject(err);
//           }
//           const filename = buf.toString('hex') + path.extname(file.originalname);
//           const fileInfo = {
//             filename: filename,
//             bucketName: 'uploads'
//           };
//           resolve(fileInfo);
//         });
//       });
//     }
//   });
  
//   const upload = multer({ storage });
//   // @route POST /upload
//   // @desc  Uploads file to DB
//   app.post('/upload', upload.single('file'), (req, res) => {
//     // res.json({ file: req.file });
  
//     res.redirect('/');
//   });
  
//   // @route GET /files
//   // @desc  Display all files in JSON
//   app.get('/files', (req, res) => {
//     gfs.files.find().toArray((err, files) => {
//       // Check if files
//       if (!files || files.length === 0) {
//         return res.status(404).json({
//           err: 'No files exist'
//         });
//       }
  
//       // Files exist
//       return res.json(files);
//     });
//   });
  
//   // @route GET /files/:filename
//   // @desc  Display single file object
//   app.get('/files/:filename', (req, res) => {
//     gfs.files.findOne({ filename: req.params.filename }, (err, file) => {
//       // Check if file
//       if (!file || file.length === 0) {
//         return res.status(404).json({
//           err: 'No file exists'
//         });
//       }
//       // File exists
//       return res.json(file);
//     });
//   });
  
//   // @route GET /image/:filename
//   // @desc Display Image
//   app.get('/image/:filename', (req, res) => {
//     gfs.files.findOne({ filename: req.params.filename }, (err, file) => {
//       // Check if file
//       if (!file || file.length === 0) {
//         return res.status(404).json({
//           err: 'No file exists'
//         });
//       }
  
//       // Check if image
//       if (file.contentType === 'image/jpeg' || file.contentType === 'image/png') {
//         // Read output to browser
//         const readstream = gfs.createReadStream(file.filename);
//         readstream.pipe(res);
//       } else {
//         res.status(404).json({
//           err: 'Not an image'
//         });
//       }
//     });
//   });
  
//   // @route DELETE /files/:id
//   // @desc  Delete file
//   app.delete('/files/:id', (req, res) => {
//     gfs.remove({ _id: req.params.id, root: 'uploads' }, (err, gridStore) => {
//       if (err) {
//         return res.status(404).json({ err: err });
//       }
  
//       res.redirect('/');
//     });
//   });

// // var express = require('express');
// // const app = express();
// // var router = express.Router();
// // const {MongoClient} = require('mongodb');
// // const { 
// //     v1: uuidv1,
// //     v4: uuidv4,
// //   } = require('uuid');  

// // const mongoURI = 'mongodb://localhost:27017/Test';
// // app.use(express.json());
// // app.use(express.urlencoded({ extended: false }));

// // // Test Route
// // router.get("/", (req, res) => {
// //   res.status(200).send("Hello There ;)")
// // })

// // app.get('/image/:filename', (req, res) => {
// //     gfs.files.findOne({ filename: req.params.filename }, (err, file) => {
// //       // Check if file
// //       if (!file || file.length === 0) {
// //         return res.status(404).json({
// //           err: 'No file exists'
// //         });
// //       }
  
// //       // Check if image
// //       if (file.contentType === 'image/jpeg' || file.contentType === 'image/png') {
// //         // Read output to browser
// //         const readstream = gfs.createReadStream(file.filename);
// //         readstream.pipe(res);
// //       } else {
// //         res.status(404).json({
// //           err: 'Not an image'
// //         });
// //       }
// //     });
// //   });

// // // Om användaren loggar in,
// // // params = användarnamn, hashat lösenord
// // // kolla om användarnamn finns, om det finns, kolla om hashat lösenord matchar
// // // om det matchar, returnera ett nytt sessionID, lägg även till nytt sessionID
// // // i databasen.
// // // om inte finns returnera status, baserat på status skriv felmeddelane. 
// // // returnerna en session ID/Token

// // module.exports = router;