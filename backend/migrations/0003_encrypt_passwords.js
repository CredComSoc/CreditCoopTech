const config = require('../config')
const {MongoClient} = require('mongodb');
const bcrypt = require('bcrypt');

async function encryptPassword(password) {
  try {
    const salt = await bcrypt.genSalt(10);
    const hash = await bcrypt.hash(password, salt);
    return hash;
  } catch (error) {
    console.log(error)
    throw error;
  }
}

(async () => {
  const client = await MongoClient.connect(config.mongoURL)
  try {
    const dbo = client.db();
    const usersCollection = await dbo.collection("users");
    const allUsers = await usersCollection.find({}).toArray();

    for (const user of allUsers) {
      const hashedPassword = await encryptPassword(user.password);

      await usersCollection.updateOne(
        { _id: user._id },
        { $set: { password: hashedPassword } }
      );
    }
  }
  finally {
    client.close()
  }
})()
