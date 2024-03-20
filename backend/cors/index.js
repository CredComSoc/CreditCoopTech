const cors = require('cors');
const config = require('../config')

const corsOptions = {
    origin: '*',
    optionsSuccessStatus: 200, 
    methods: ['GET', 'POST', 'PATCH', 'HEAD', 'OPTIONS', 'PUT'],
    credentials: true
};

module.exports = cors(corsOptions)
