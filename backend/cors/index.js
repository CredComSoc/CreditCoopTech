const cors = require('cors');


const corsOptions = {
    origin: '*',
    optionsSuccessStatus: 200, 
    methods: ['GET', 'POST', 'PATCH']
};

module.exports = cors(corsOptions)
