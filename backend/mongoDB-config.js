
// const mongoURL = "mongodb://localhost:27017/"

const dbFolder = "sb-web-app"
//const dbFolder = "tvitter"
const mongoURL = function(folder, localUrl = true) {
    localUrl = false; // this SHOULD not be permanent, fix plz.
    if (localUrl) {
        return "mongodb://localhost:27017/" + folder + "?retryWrites=true&w=majority"
    } else {
        return "mongodb+srv://MCS-SB-admin:4%25D3SgU3x%24gCJUFCQI@sb-dev.g59in.mongodb.net/" + folder + "?retryWrites=true&w=majority"
    }
    
}


module.exports = { mongoURL, dbFolder }
