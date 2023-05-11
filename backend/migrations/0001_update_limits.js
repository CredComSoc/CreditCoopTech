const config = require('../config')
const {MongoClient} = require('mongodb');

min_limit_default = -1000;
max_limit_default = 1000;

// wrap in a closure to use await
(async () => {

    const client = await MongoClient.connect(config.mongoURL)
    try {
        const dbo = client.db();
        await dbo.collection("users").updateMany( {}, 
            { $set: { min_limit: min_limit_default } })
        
        await dbo.collection("users").updateMany( {}, 
                { $set: { max_limit: max_limit_default } })
    }
    finally {
        client.close()
    }

})()
