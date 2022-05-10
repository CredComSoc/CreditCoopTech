const { MongoClient, ObjectId } = require('mongodb');
const { mongoURL, dbFolder } = require('../mongoDB-config');
const uuid = require('uuid');


module.exports.initChat = async (sender, receiver) => {
    const chatID = uuid.v4();
    const res1 = await this.createChat(sender, receiver, chatID);
    console.log("RES1:", res1); 
    if (res1) {
       const res2 = await this.createChat(receiver, sender, chatID);
       console.log("RES2:", res2);
       if (!res2) {
            this.deleteChat(sender, receiver);
            console.log("Kan inte skapa chatten just nu");
       }
       else {
            const db = await MongoClient.connect(mongoURL);
            const dbo = db.db(dbFolder);
            dbo.collection('chats').insertOne({ [chatID] : [] }, (err, res) => {
                if (err) {
                    console.log(err);
                    db.close();
                } else {
                    console.log(res);
                    db.close();
                }
            });
       }     
    }

    // if (sen && rec) {
    //     return true;
    // } else {
    //     return false;
    // }
}

module.exports.deleteChat = async (user, chatter) => {
    console.log(mongoURL);
    console.log(dbFolder);
    const db = await MongoClient.connect(mongoURL);
    const dbo = db.db(dbFolder);
    const key = 'chats.' + chatter;
    dbo.collection('users').updateOne({'profile.accountName': user}, { $unset: { [key]: 1 } }, (err, res) => {
        if (err) {
            console.log(err);
            db.close(); 
        }
        else {
            db.close();
        }
    }); 
}

module.exports.createChat = (user, chatter, chatID) => {
    return new Promise( async (resolve, reject) => {
        const db = await MongoClient.connect(mongoURL);
        const dbo = db.db(dbFolder);
        const key = 'chats.' + chatter;
        dbo.collection('users').updateOne({'profile.accountName': user}, { $set: { [key]:  chatID } }, (err, res) => {
            if (err) {
                console.log(err);
                db.close();
                resolve(false);
            } else if (res.matchedCount > 0) {
                console.log(res);
                db.close();
                resolve(true);
            }
            else {
                db.close();
                resolve(false);
            }
        });
    });
}

module.exports.checkChatStatus = async (chatter) => {
    const db = await MongoClient.connect(mongoURL);
    const dbo = db.db(dbFolder);
    const key = 'chats.' + chatter;
    const res = await dbo.collection('users').findOne({[key] : {$exists : true} }, (err, res) => {
        if (err) {
            console.log(err);
            db.close();
            return false;
        } else if (res.matchedCount > 0) {
            console.log(res);
            db.close();
            return true;
        } else {
            db.close();
            return false;
        }
    });

    return res;
}


const sendChatMsg = async (msg) => {
    const db = await MongoClient.connect(mongoURL);
    const dbo = db.db(dbFolder);

    db.close();

}
