const config = require('../config')
const {MongoClient} = require('mongodb');

// NB to run this in production
// 
// 1. Get latest code on server
// 2. ssh to server
// 3. cd ~/sb-web-app-2.0/backend
// 4. run the following
//    > NODE_ENV=production node migrations/0001_update_limits.js 
//
// You should see the following (excepting that xxx is username/password)
// root@ubuntu-s-1vcpu-1gb-nyc1-01:~/sb-web-app-2.0/backend# NODE_ENV=production node migrations/0001_update_limits.js
// mongodb+srv://xxx-xxx:xxx-xx-xxxx@lcc-1.g59in.mongodb.net/vt-web-app?retryWrites=true&w=majority

// currently just sets limits for all users to the following

min_limit_default = -500;
max_limit_default = 500;

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
// wrap in a closure to use await
