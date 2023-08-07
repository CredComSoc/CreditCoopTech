
# Installation for local dev

There are three key components of this app.

- /backend - nodejs/express app
- /sb.web.app - vuejs front end 
- /cc-node - credit commons node 

There are two different databases 

- mongodb for the /backend express app - which stores user data and such
- mysql for the cc-node store - for the core ledger information


### Ensure you have node v16.14.0

On osx, I like to install via nvm

**Install nvm via brew**

```
brew install nvm
follow instructions regarding path 
```

**Now you can install node**
```
nvm install 16.14.0
nvm alias default 16.14.0
nvm use 16.14.0
npm i -g npm
```

```
> node --version
v16.14.0
```

```
> npm --version
8.3.1
```

## sb.web.app vuejs app 

**install packages into sb.web.app node_modules**
```
cd sb.web.app
npm install 
```

## backend - expressjs app 

**install pckages into backend node_modules**
````
cd ../backend
npm install 
````

## local mongodb 

I like to have this running locally. Needed to support the backend express app

1. Install mongodb server

    per instructions here..
    https://www.mongodb.com/docs/manual/tutorial/install-mongodb-on-os-x/

    don't forget to add to your brew services 
    ```
    brew services start mongodb/brew/mongodb-community
    ```

2. You may want to install mongo shell (mongosh) cli interface
    ```
    brew install mongocli
    ```

3. You may also want to install compass GUI from here 

   https://www.mongodb.com/try/download/compass

4. Optionally install mongodb from backup

    mongodb has to be kept in sync with cc-node 
    so potentially talk to @utunga about getting a copy of the mongodb db that is compatible with the development cc-node 

    ```
    brew reinstall mongodb-database-tools
    cd db_bak/dump
    mongorestore --dryRun .
    mongorestore .
    ```

## Running in local dev 

I start up two services - local express backend and vuejs front end. In this configuration the backend connects to cc-node in the cloud. 

**Start backend**

```
> cd backend 
> npm run start
```

```
backend@1.0.0 start
nodemon --exec babel-node app.js

[nodemon] 1.19.4
[nodemon] to restart at any time, enter `rs`
[nodemon] watching dir(s): *.*
[nodemon] watching extensions: js,mjs,json
[nodemon] starting `babel-node app.js`
Connecting to mongodb://127.0.0.1:27017/vt-web-app?retryWrites=true&w=majority
::
Listening to http://:::3000
listening on *:3001
```

**Start frontend**
```
➜ ✗ cd sb.web.app
➜ ✗ npm run serve
```

```
sb.web.app@0.1.0 serve
vue-cli-service serve

 INFO  Starting development server...
98% after emitting CopyPlugin

 DONE  Compiled successfully in 4765ms                                                                                                                                                12:14:28 AM


  App running at:
  - Local:   http://localhost:8080/ 
  - Network: http://192.168.100.161:8080/

  Note that the development build is not optimized.
  To create a production build, run npm run build.
```

**Creating test users**

In order to create a test user account that you can use to explore the app:
```
> cd backend
> node migrations/0000_init_users.js
```

Open the `0000_init_users.js` file and navigate to the end to view the credentials for the newly-created account.

<!-- 
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

Backend can also be run manually as described above in /var/www/sb-web-app/backend NOTE: You have to turn of the backend.service first, use "systemctl stop backend.service" -->
