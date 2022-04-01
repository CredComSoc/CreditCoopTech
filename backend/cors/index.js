const cors = require('cors');


const corsOptions = {
    origin: ['http://192.168.0.100:8080', 'http://localhost:8080', 'http://155.4.159.231:8080'],
    optionsSuccessStatus: 200, 
    methods: ['GET', 'POST', 'PATCH'],
    credentials: true
};

module.exports = cors(corsOptions)
