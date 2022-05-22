# Credit Commons reference implementation

Reference implementation of the [Credit Commons protocol](https://gitlab.com/credit-commons-software-stack/cc-php-lib/-/blob/master/docs/credit-commons-openapi-3.0.yml). [Documentation](https://gitlab.com/credit-commons-software-stack/credit-commons-documentation). This reference implementation is intended to be small and robust and federatable.

## Intent
Develop network software that can serve a fully differentiated world economy of federated Mutual Credit networks.
The inspiration for this is the [Credit Commons whitepaper](http://www.creditcommons.net/) by [Matthew Slater](https://matslats.net/) and [Tim Jenkin](https://en.wikipedia.org/wiki/Tim_Jenkin).

## Vision
The vision here serves [the overall Credit Commons vision, here](https://gitlab.com/credit-commons-software-stack/credit-commons-org/blob/master/README.md).

##About this reference implementation
PHP and Mysql were chosen because the developer knew them, and because of their widespread usage and durability. The latest PHP was preferred because it is better at data type management. It depends on a separate PHP repository containing a library of the API calls needed by both nodes and leaves (clients). So far it is all the work of one person and has developed an architectural approach which could be a template or starting point for future implementations.

This application could in theory be used as the backend for an app, but in practice it is too limited, and is intended to be a web service which does accounting.

###The AccountStore microservice.
This reference implementation includes a microservice which authenticates requests against accounts, and returns account names and balance limits. This is really just a placeholder, it stores info in a csv file and has no UI to edit the csv file. A proper deployment would require that the main users' repository implement a small [API](https://gitlab.com/credit-commons-software-stack/cc-node/-/blob/master/AccountStore/accountstore.openapi.yml) from which this application can read the account ids and limits. The url where the accountstore API is implemented is configured in node.ini.

###Authentication
The current version of the protocol requires that each incoming request includes headers with the account id and a key. Those credentials are then passed to the accountstore for authentication. The accountStore returns a simple authenticated user object or the client is assumed to be anonymous.

###Permissions
The reference implementation has a permission for every API call described, and there is currently no way to configure them. The user admin flag is enabled, the user has all permissions. Permission to manipulate transactions are controlled by the workflow system.

###The BLogic service
The protcocol allows for nodes to append sub-transactions (entries) such as fees or taxes, as a transaction is being built and validated. This is called Business logic, and is implemented here as a microservice with a single method. This means that deployments can easily write their own business logic in whatever language they please. The url of this microservice is also configured in node.ini. If the value is left empty, the business logic step is skipped.

###Account class hierarchy
In order to relay a transaction accross the tree, the node needs to know its position in the path from twig to twig. It does this by parsing the payer and payee paths and loading them as account objects of different classes.

    Account
      -Remote
        - Branch
          - DownstreamBranch
          - UpstreamBranch
        - Trunkward
          - DownstreamTrunkward
          - UpstreamTrunkward
