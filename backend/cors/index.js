const cors = require('cors');


const corsOptions = {
    //origin: ['http://192.168.0.100:8080', 'http://localhost:8080', 'http://155.4.159.231:8080',
    //          'http://192.168.0.100:3000', 'http://localhost:3000', 'http://155.4.159.231:3000'
    //          'http://192.168.0.100:3000/login', 'http://localhost:3000/login', 'http://155.4.159.231:3000/login'],
    origin: true,
    optionsSuccessStatus: 200, 
    methods: ['GET', 'POST', 'PATCH'],
    credentials: true
};

module.exports = cors(corsOptions)
