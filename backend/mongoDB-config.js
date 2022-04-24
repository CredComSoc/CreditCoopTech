
// const mongoURL = "mongodb://localhost:27017/"

const dbFolder = "tvitter"
const mongoURL = "mongodb+srv://sb:sb-password@cluster0.i2vzq.mongodb.net/" + dbFolder +"?retryWrites=true&w=majority"


module.exports = { mongoURL, dbFolder }