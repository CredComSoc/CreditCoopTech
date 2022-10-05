
// const mongoURL = "mongodb://localhost:27017/"

const dbFolder = "sb-web-app"
//const dbFolder = "tvitter"
const mongoURL = function(folder, localUrl = true) {
    localUrl = false;
    if (localUrl) {
        return "mongodb://localhost:27017/" + folder + "?retryWrites=true&w=majority"
    } else {
        return "mongodb+srv://sb:sb-password@cluster0.3e0zakq.mongodb.net/" + folder + "?retryWrites=true&w=majority"
    }
    
}


module.exports = { mongoURL, dbFolder }