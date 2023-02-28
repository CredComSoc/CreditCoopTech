const cors = require('cors');
const config = require('../config')

const corsOptions = {
    origin: config.CORS_WHITE_LIST,
    optionsSuccessStatus: 200, 
    methods: ['GET', 'POST', 'PATCH', 'HEAD', 'OPTIONS'],
    credentials: true
};

module.exports = cors(corsOptions)
