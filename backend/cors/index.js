const cors = require('cors');
const config = require('../mongoDB-config')

const corsWhiteList = config.corsWhiteList 
       

const corsOptions = {
    origin: corsWhiteList,
    optionsSuccessStatus: 200, 
    methods: ['GET', 'POST', 'PATCH', 'HEAD', 'OPTIONS'],
    credentials: true
};

module.exports = cors(corsOptions)
