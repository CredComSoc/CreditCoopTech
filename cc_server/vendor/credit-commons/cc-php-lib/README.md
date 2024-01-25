# Credit Commons PHP library

This php library implements all the API functions to help accelerate development of credit commons apps and to provide a compatibility layer.

it is required by any PHP application which wants to read or write to a Credit commons node via REST. That includes [Reference Node](https://gitlab.com/credit-commons/cc-node) while addressing remote nodes and also the [Developers' client](https://gitlab.com/credit-commons/cc-dev-client) which provides a Full UI to a credit commons node at any URL via REST.

This repo also hosts the [OpenAPI specification](https://gitlab.com/credit-commons/cc-php-lib/-/blob/master/docs/credit-commons-openapi-3.0.yml).

## Installation
This is not a standalone app or a REST server, and is already included as needed in the other credit commons reference implementation repositories.
If you are making a client side application or a new node implementation, first add this repository to your composer.json

    "repositories": [
       {
           "type": "gitlab",
            "url": "git@gitlab.com:credit-commons/cc-php-lib.git"
       }
    ]

Then require it thus:

```$ composer require credit-commons/cc-php-lib```