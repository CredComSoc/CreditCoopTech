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


module.exports.chatExists = async (user, chatter) => {
    const chatExist = await this.checkChatStatus(chatter);
    if (!chatExist) {
        this.initChat(user, chatter);
    }
    else {
        const chatID = await this.getChatID(user, chatter);
        if (chatID === false) {
            console.log("Kan inte hämta chatten");
        }   
        else {
            const chatHistory =  await this.getChatHistory(chatID);
            if (chatHistory === false) {
                console.log("Kan inte hämta chattens historia");
            }
            else {
                console.log(chatHistory);
                return chatHistory;
            }
        }
    }
}


module.exports.getChatHistory = async (chatID) => {
    return new Promise(async (resolve, reject) => {
        const db = await MongoClient.connect(mongoURL);
        const dbo = db.db(dbFolder);
        dbo.collection('chats').findOne({[chatID]: {$exists: true}}, (err, res) => {
            if (err) {
                console.log(err);
                db.close();
                resolve(false);
            } else if (res.matchedCount > 0) {
                console.log(res);
                db.close();
                resolve(res[chatID]);
            } else {
                db.close();
                resolve(false);
            }
        });
    });
}


module.exports.getChatID = async (user, chatter) => {
    return new Promise(async (resolve, reject) => {
        const db = await MongoClient.connect(mongoURL);
        const dbo = db.db(dbFolder);
        const key = user + '.chats.' + chatter;
        dbo.collection('users').findOne({$and:[ { [key] : {$exists : true} }, { 'profile.accountName': user } ]}, (err, res) => {
            if (err) {
                console.log(err);
                db.close();
                resolve(false);
            } else if (res.matchedCount > 0) {
                console.log(res);
                db.close();
                resolve(res[key]);
            }
            else {
                db.close();
                resolve(false);
            }
        });
    });
}


module.exports.checkChatStatus = async (chatter) => {
    return new Promise(async (resolve, reject) => {
        const db = await MongoClient.connect(mongoURL);
        const dbo = db.db(dbFolder);
        const key = 'chats.' + chatter;
        dbo.collection('users').findOne({[key] : {$exists : true} }, (err, res) => {
            if (err) {
                console.log(err);
                db.close();
                resolve(false);
            } else if (res.matchedCount > 0) {
                console.log(res);
                db.close();
                resolve(true);
            } else {
                db.close();
                resolve(false);
            }
        });
    });
}


module.exports.sendChatMsg = async (chatID, msg) => {
    const db = await MongoClient.connect(mongoURL);
    const dbo = db.db(dbFolder);
    dbo.collection('chats').updateOne({[chatID]: {$exists: true}}, { $push: { [chatID]: msg } }, (err, res) => {
        if (err) {
            console.log(err);
            db.close();
        } else if (res.matchedCount > 0) {
            console.log(res);
            db.close();
        } else {
            console.log(res);
            db.close();
        }
    });
}
