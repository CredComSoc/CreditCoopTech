
const dotenv = require('dotenv')

// Load config from .env file or an environment variable 
//
// When you call require('dotenv').config(), it loads the appropriate file -
// (.env.production or .env.staging based) on the value of the NODE_ENV environment variable.
// Or if no value is set for NODE_ENV, dotenv defaults to the .env file.
// We can check '.env' into git, but not .env.production or .env.staging (as these will contain secrets)

dotenv.config()
const dbUri = process.env.MONGODB_URI;
const dbFolder = process.env.MONGODB_DB_FOLDER
const FRONT_END_URL =  process.env.FRONT_END_URL
const CC_NODE_URL = process.env.CC_NODE_URL

const mongoURL = dbUri + dbFolder + '?retryWrites=true&w=majority'

// regex that a domain starts with "http://localhost" or ends with ".mutualcredit.services
// later we'll read this from .env but that is on another branch for now 
corsWhiteList = [/^http:\/\/localhost/, /\.mutualcredit\.services$/]

module.exports = { mongoURL, FRONT_END_URL, CC_NODE_URL, corsWhiteList}
