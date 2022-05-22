# Cooperative Barter Software e d

Cooperative Barter Software is an online trading post for entrepreneurs. Created with Vue.js and Express.js, and uses a [Credit Commons node](https://gitlab.com/credit-commons-software-stack/cc-node).  

## Requirements

- A web server that meet the cc-node requirements, listed in cc-node/INSTALL.md
- [Node.js](https://nodejs.org)
- A [MongoDB](https://www.mongodb.com/) database.


## Installation

### Install cc-node

Follow the instructions in cc-node/INSTALL.md


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

## License

[GNU Affero General Public License (AGPL)](https://www.gnu.org/licenses/agpl-3.0.html)
