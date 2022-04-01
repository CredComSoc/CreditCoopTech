const express = require('express');
var router = express.Router();
const mongoose = require('mongoose');
const Grid = require('gridfs-stream');
const {MongoClient} = require('mongodb');
/**
 * vvvvv CHANGE URL AND DB FOLDER NAME HERE vvvvv
 */
//let url = "mongodb://localhost:27017/"
//let dbFolder = "Test"
//let userFolder = "Users"
let url = "mongodb+srv://sb:sb-password@cluster0.i2vzq.mongodb.net/myFirstDatabase?retryWrites=true&w=majority"
let dbFolder = "tvitter"
let userFolder = "users"

const mongoURI = url;
const conn = mongoose.createConnection(mongoURI).useDb(dbFolder);

// Init gfs
let gfs;
conn.once('open', () => {
  // Init stream
  gfs = Grid(conn.db, mongoose.mongo);
  gfs.collection('uploads');
});

// filename: req.params.filename

router.get('/image/:filename', (req, res) => {
  gfs.files.findOne({}, (err, file) => {
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

router.post('/getAllListings/', (req, res) => {
    // fetch all metadata about listing from mongoDB
    let searchword = req.body.searchword.split(' ')
    let destinations = req.body.destinations;
    let categories = req.body.categories;
    let articles = req.body.articles;

    MongoClient.connect(url, (err, db) => {
        let dbo = db.db(dbFolder)
        let productsAllListingsArray = []
        let servicesAllListingsArray = []

        searchword = searchword.filter(function(value, index, arr) {
          return value !== "";
        })

        dbo.collection(userFolder).find({}).toArray(function (err, users) {

          if (err) {
            res.sendStatus(500)
            db.close();
          }
          else {
            users.forEach(user => {
              user.posts.forEach(listing => {
                
                //Om ARTIKEL
                if (articles.length !== 0) {
                  if (!articles.includes(listing.article)) {
                    return
                  }
                }

                //OM DESTINATION
                if (destinations.length !== 0) {
                  if (!destinations.includes(listing.destination)) {
                    return
                  }
                } 

                //OM CATEGORY
                if (categories.length !== 0) {
                  if (!categories.includes(listing.category)) {
                    return
                  }
                } 

                foundSearchword = true
                if( searchword.length !== 0 ) {
                  for (let i = 0; i < searchword.length; i++) {
                    if (!listing.title.match(new RegExp(searchword[i], "i"))) {
                      foundSearchword = false
                      break
                    } 
                  }
                  if (!foundSearchword) {
                    return
                  }
                }
                
                //TILLDELA TJÄNST ELLER PRODUKT
                if(listing.article === "product") {
                  productsAllListingsArray.push(listing)
                } else if (listing.article === "service") {
                  servicesAllListingsArray.push(listing)
                } else {
                  res.sendStatus(304).send('No tag for listing')
                }
              })
            })
            res.send({allProducts: productsAllListingsArray, allServices: servicesAllListingsArray})
            db.close();
          }
        })
    })
})



module.exports = router;



