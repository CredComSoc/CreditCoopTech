
const dotenv = require('dotenv-flow')

// Load config from .env file or an environment variable 
//
// Forr this to work we need *all* of the following files 
// .env                    # loaded in all cases
// .env.local              # loaded in all cases, ignored by git
// .env.production         # only loaded in specified mode
// .env.production.local   # only loaded in specified mode, ignored by git
// .env.staging            # only loaded in specified mode
// .env.staging.local     # only loaded in specified mode, ignored by git
//
// The values in .local can be set up as environment variables in the shell also (eg in netlify admin)

dotenv.config()
const dbUri = process.env.MONGODB_URI
    .replace('<username>', process.env.MONGODB_USERNAME) // read these from the environment or .local file
    .replace('<password>', process.env.MONGODB_PASSWORD) // read these from the environment or .local file

const dbFolder = process.env.MONGODB_DB_FOLDER
const FRONT_END_URL =  process.env.FRONT_END_URL
const CC_NODE_URL = process.env.CC_NODE_URL

const mongoURL = dbUri + dbFolder + '?retryWrites=true&w=majority'

// regex that a domain starts with "http://localhost" or ends with ".mutualcredit.services
CORS_WHITE_LIST = [/^http:\/\/localhost/, /\.mutualcredit\.services$/]


module.exports = { mongoURL, FRONT_END_URL, CC_NODE_URL, CORS_WHITE_LIST}
