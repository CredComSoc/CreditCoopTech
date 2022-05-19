const { MongoClient, ObjectId } = require('mongodb');
const dbConfig = require('../mongoDB-config');
const uuid = require('uuid');
const { query } = require('express');
const { model } = require('mongoose');

const dbFolder = dbConfig.dbFolder
const mongoURL = dbConfig.mongoURL(dbFolder)

module.exports.initChat = async (sender, receiver) => {
    return new Promise(async (resolve, reject) => {
        const chatID = uuid.v4();
        const res1 = await this.createChat(sender, receiver, chatID);
        if (res1) {
            const res2 = await this.createChat(receiver, sender, chatID);
            if (!res2) {
                this.deleteChat(sender, receiver, chatID);
                console.log("Kan inte skapa chatten just nu");
                resolve(false);
            }
            else {
                const db = await MongoClient.connect(mongoURL);
                const dbo = db.db(dbFolder);
                dbo.collection('chats').insertOne({ [chatID] : [] }, (err, res) => {
                    if (err) {
                        console.log(err);
                        db.close();
                        resolve(false);
                    } else {
                        db.close();
                        resolve(chatID);
                    }
                });
            }     
        }
        else {
            resolve(false);
        }
    });
}


module.exports.deleteChat = async (user, chatter, chatID) => {
    const db = await MongoClient.connect(mongoURL);
    const dbo = db.db(dbFolder);
    const key = 'chats.' + chatID;
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
        const key = 'chats.' + chatID;
        dbo.collection('users').updateOne({'profile.accountName': user}, { $set: { [key]: chatter } }, (err, res) => {
            if (err) {
                console.log(err);
                db.close();
                resolve(false);
            } else if (res.matchedCount > 0) {
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
    const chatExist = await this.checkChatStatus(user,chatter);
    if (!chatExist) {
        const chatID = await this.initChat(user, chatter);
        return chatID;
    }
    else {
        const chatID = await this.getChatID(user, chatter);
        if (chatID === false) {
            console.log("Kan inte hämta chatten");
            return chatID;
        }   
        else {
            return chatID;
        }
    }
}

module.exports.getAllChatIDs = async (user) => {
    return new Promise( async (resolve, reject) => {
        const db = await MongoClient.connect(mongoURL);
        const dbo = db.db(dbFolder);
        dbo.collection('users').findOne({'profile.accountName': user}, (err, res) => {
            if (err) {
                console.log(err);
                db.close();
                resolve(false);
            } else if (res.chats) {
                console.log(res.chats);
                db.close();
                resolve(res.chats);
            }
            else {
                db.close();
                resolve(false);
            }
        });
    });
}

module.exports.getAllChatHistories = async (user) => {
    return new Promise( async (resolve, reject) => {
        const chatIDs = await this.getAllChatIDs(user);
        if (chatIDs === false) {
            console.log("Kan inte hämta chattens historia");
            resolve(false);
        }
        else {
            resolve(chatIDs);
        }
    });
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
            } else if (res) {
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
        dbo.collection('users').findOne({ 'profile.accountName': user }, (err, res) => {
            if (err) {
                console.log(err);
                db.close();
                resolve(false);
            } else if (res.chats) {
                found_chatID = false;
                for (const [key, val] of Object.entries(res.chats)) {
                    if (val === chatter) {
                        db.close();
                        resolve(key);
                        found_chatID = true;
                    }
                }
                if (!found_chatID) {
                    db.close();
                    resolve(false);
                }
            }
            else {
                db.close();
                resolve(false);
            }
        });
    });
}


module.exports.checkChatStatus = async (user, chatter) => {
    return new Promise(async (resolve, reject) => {
        const db = await MongoClient.connect(mongoURL);
        const dbo = db.db(dbFolder);
        const key = 'chats.' + chatter;
        dbo.collection('users').findOne({ 'profile.accountName': user } , (err, res) => {
            if (err) {
                console.log(err);
                db.close();
                resolve(false);
            } else if (res) {
                if ('chats' in res) {
                    Object.values(res.chats).findIndex(val => val === chatter) > -1 ? resolve(true) : resolve(false);
                }
                else {
                    resolve(false);
                }
                db.close();
            } else {
                db.close();
                resolve(false);
            }
        });
    });
}


module.exports.storeChatMsg = async (chatID, msg) => {
    const db = await MongoClient.connect(mongoURL);
    const dbo = db.db(dbFolder);
    dbo.collection('chats').updateOne({[chatID]: {$exists: true}}, { $push: { [chatID]: msg } }, (err, res) => {
        if (err) {
            console.log(err);
            db.close();
        } else if (res) {
            db.close();
        } else {
            console.log(res);
            db.close();
        }
    });
}

module.exports.storeNotification = async (notification) => {
    const db = await MongoClient.connect(mongoURL)
    const dbo = db.db(dbFolder);
   
    dbo.collection('users').findOne({ 'profile.accountName': notification.toUser }, (err, user) => {
        if (err) {
            console.log(err);
            db.close();
        }  
        else if (user) {
          let notification_list = user.notifications
          for (let i = 0; i < notification_list.length; i++) {
            if (notification_list[i].fromUser === notification.fromUser && notification_list[i].type === notification.type) {
              notification_list.splice(i, 1);
              console.log(notification_list);
              break
            }
          }
          if (notification_list.length >= 4) {
            notification_list = [notification, notification_list[0], notification_list[1], notification_list[2]]
          } else {
            notification_list.push(notification)
          }
          dbo.collection('users').updateOne({ 'profile.accountName': notification.toUser }, { $set: { notifications: notification_list } }, (err, query) => {
            if (err) {
                console.log(err);
                db.close();
            }
            else if (query.acknowledged) {
                console.log("ADDED NOTIFICATION")
                db.close();
            } else {
                db.close();
            }
          });
        } else {
            db.close();
        }
    });
}

module.exports.markNotification = async (chatID, username) => {
    const db = await MongoClient.connect(mongoURL)
    const dbo = db.db(dbFolder);
   
    dbo.collection('users').findOne({ 'profile.accountName': username }, (err, user) => {
        if (err) {
            console.log(err);
            db.close();
        }
        else if (user) {
            let found_notice = false;
            let notification_list = user.notifications
            for (let i = 0; i < notification_list.length; i++) {
                if (notification_list[i].chatID === chatID && notification_list[i].type === "chatMessage" ) {
                    notification_list[i].seen = true;
                    found_notice = true;
                    break
                }
            }
            if (found_notice) {
                dbo.collection('users').updateOne({ 'profile.accountName': username }, { $set: { notifications: notification_list } }, (err, query) => {
                    if (err) {
                        console.log(err);
                        db.close();
                    }
                    else if (query.acknowledged) {
                        console.log("MARKED NOTIFICATION")
                        db.close();
                    } else {
                        db.close();
                    }
                });
            } else {
                db.close();
            }
        }
        else {
            db.close();
        }
    });
}