# Credit Commons node (server)

This package provide a REST wrapper around the [Credit Commons node](cc-node). Use it if you want a standalone ledger or if your main application is not in PHP.

## About this reference implementation
PHP and Mysql are widely used and trusted over the long term, and the languages in which the developer is most proficient. The architecture presented some challenges and the solutions here should be considered provisional. The project is published as seven packages:

1. This packages which provides a REST wrapper around
1. The Credit Commons node - the reference implementation itself.
1. A library of php functions which call the Credit Commons API, in order to ease development. Used by the reference implementation to call other nodes and by
1. The developer client, a client side application that implements the whole API
1. A package that implements the accountStore API to provide some demo users.
1. A REST wrapper which implements the accountstore API, enabling you to connect your main social networking app to the credit commons even if it's not in php.
1. A REST server which acts as a wrapper around a business logic microservice, should you choose to use it. Business logic is a class with a single public function which appends entries to a transaction.

## Installation
### Prerequisites
1. PHP 8 with mysqli 
1. mariaDB / Mysql
1. composer
1. A web server (currently only apache2 is supported)
1. A REST Client which implements thte Credit Commons protocol
1. The web server may need write access to the root directory either to edit node.ini or if are using the default accountstore, to write to accountstore.json

### Options
Combinations of seven packages support several scenarios:

- Demo code and user is provided
- A Credit Commons node can be run as a REST service or incorporated into a php application
- The user store can also be incorporated into a PHP app or accessed by a non-PHP application via REST
- Business logic can be incorporated into a PHP app, accessed via REST or not used at all.

### Procedure to set up a standalone node.
To run a credit commons node as a web service:

- Create a VirtualHost and download this package to the web root.
- Go to that directory and run "composer update"
- navigate in the browser to the project root and you will land on the config page.
- create a database and enter the credentials on the config page.
- The node should now work with the default users.
- A standalone node like this will probably need to access the [accountStore] (and optionally, [the business logic]) as other REST services with custom code.

### Procedure to incorporate a Credit Commons node in your PHP application.

- In your main project's composer file "require credit-commons/cc-node"
- In your code, call the documented [API functions](https://gitlab.com/credit-commons/cc-php-lib/-/blob/master/docs/credit-commons-openapi-3.0.yml)
- Write a class which extends CCNode\AccountStoreDefault to convert your member account details into the objects that extend CCNode\User and declare it in node.ini.
- If you need business logic, write a class which extends [CCNode\CCBlogicInterface]() and declare it in node.ini.

### Procedure to install separate web services.

Your app needs to provide the Credit Commons node with basic info about your user accounts (wallet name, balance limits and admin status). The credit commons node can make requests to a microservice which implements the accountstore API. A (PHP) example of such a service is [cc-accountstore-server](blah) which comes bundled with [cc-demo-accountstore](blah)

The business logic, is optional but works in a similar way. You can set up a microservice which implements the one method API, example is [cc-blogic-server](blah)

## Installation
To install a credit commons server,

    $ composer create-project --stability dev credit-commons/cc-server --repository '{"type": "gitlab","url": "git@gitlab.com:credit-commons/cc-server"}' MY_CC_SERVER

Create the VirtualHost on your webserver with the web root pointing to MY_CC_SERVER and restart the server.
Navigate in your browser to the web root, and you will be redirected to the setup page.
Create a database and enter your credentials. Note that here you can also set the classes or urls for the accountstore and the business logic module.
Navigate again to the web root and if you see a holding page the server is ready to receive requests.
To authorise requests against users in the demo account store use headers cc-user: alice and cc-auth: 123
Run tests with:

    $ vendor/bin/phpunit tests/SingleNodeTest.php
