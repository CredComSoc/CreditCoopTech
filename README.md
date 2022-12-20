# Cooperative Barter Software e d

Cooperative Barter Software is an online trading post for entrepreneurs. Created with Vue.js and Express.js, and uses a [Credit Commons node](https://gitlab.com/credit-commons-software-stack/cc-node).  

## Requirements

- A web server that meet the cc-node requirements, listed in cc-node/INSTALL.md
- [Node.js](https://nodejs.org)
- A [MongoDB](https://www.mongodb.com/) database.

## Local Installation

### Install cc-node -- Optional: If you want to test the cc-node functionallity while running on your computer. Otherwise test functionallity regarding cc-node on the droplet.

Follow the instructions in cc-node/INSTALL.md

__Note:__
The above instructions will probably result in a non-functional cc-node, as the latest (2022-08-29) version of cc-php-lib is not compatible with the cc-node version used in this project. The included cc-node.zip contains a pre-installed cc-node that can be used instead (PHP, MySQL, etc still needs to be installed).

### Install Express.js backend

- Start MongoDB
- Edit the MongoDB URI's in /backend/mongoDB-config.js
- Run "npm install" in /backend


### Install Vue.js Frontend

- Edit the EXPRESS_URL in /sb.web.app/src/serverFetch.js
- Run "npm install" in /sb.web.app


## Start dev server

- Run "npm run start" in /backend
- Run "npm run serve" in /sb.web.app

## Tests

- Run "npm run test" in /backend

## cc-node details

The included cc-node is [This](https://gitlab.com/credit-commons-software-stack/cc-node/-/tree/bfb5bafe0c840aa32b1124adfbb5b4bea094ee4e) cc-node commit, with the workflows adjusted to allow both the payer and the payee to erase a pending transaction.

The cc-node works through open api specs see [HERE](https://gitlab.com/credit-commons/cc-php-lib/-/blob/master/docs/credit-commons-v0.2.openapi3.yml)

The cc-node contains its own SQL server which hosts transactions etc. If users are removed from the mongoDB the transactions regarding the user has to be deleted from the SQL database. Otherwise this may cause errors.

Testing the cc-node to verify that it is running can be done by any http request program. One example is https://httpie.io/cli.
The command "http -v OPTIONS  http://dev-sb-ledger.mutualcredit.services/" can then be sent to verify anon users.
The commant "http -v GET http://dev-sb-ledger.mutualcredit.services/workflows cc-user:"User-id goes here" cc-auth:"user auth goes here"" can then be used to verify functionality for logged in users. 

## Droplet usage
To build the frontend run "npm run build in /var/www/sb-web-app/sb.web.app"

"apache2ctl restart" to restart the apache.

"systemctl restart backend.service" to restart the backend.

Backend can also be run manually as described above in /var/www/sb-web-app/backend NOTE: You have to turn of the backend.service first, use "systemctl stop backend.service"

## License

[GNU Affero General Public License (AGPL)](https://www.gnu.org/licenses/agpl-3.0.html)
