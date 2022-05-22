const cors = require('cors');


const corsOptions = {
    origin: true,
    optionsSuccessStatus: 200, 
    methods: ['GET', 'POST', 'PATCH', 'HEAD', 'OPTIONS'],
    credentials: true
};

module.exports = cors(corsOptions)
