
// const mongoURL = "mongodb://localhost:27017/"

const dbFolder = "tvitter"
const mongoURL = function(folder, localUrl = false) {
    if (localUrl) {
        return "mongodb://localhost:27017/"
    }
    return "mongodb+srv://sb:sb-password@cluster0.i2vzq.mongodb.net/" + folder + "?retryWrites=true&w=majority"
}


module.exports = { mongoURL, dbFolder }