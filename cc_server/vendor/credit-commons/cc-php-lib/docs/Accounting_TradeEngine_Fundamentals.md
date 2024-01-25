# Credit Commons Protocol - fundamentals

### The physics of the credit commons.

The Credit Commons is based in a simple and rigid protocol. 

This provides robustness, verifiability, reliability, flexibility and clarity.

The protocol has social, legal and technical dimensions.

This document describes the minimal required elements of the 'Protocol System'.

The Protocol System would be extensible.

This document uses some technical terms appropriate to a software implementation. 

## Fundamental Concepts

### Nodes

A node is a web service which implements the Credit Commons Ledger API for creating and validating transactions. Nodes have accounts and register transactions between accounts. Nodes connect through accounts they register with each other. For example when node A has an account on Node B and node B has an account on node A they are connected. The two accounts provably contain the same transactions, albeit denominated in different units. Each node is in principle politically and technically independent of all other nodes - node accounts are provably valid to their peers without their internal data being exposed.

### The tree

The Credit Commons tree is a structure of nodes which potentially connects all nodes and facilitates universal exchange.

In any tree, peer nodes do not correct directly bilaterally, but multilaterally via a 3rd shared node. The language we use to describe relationships within the tree is as follows:
*The relationship between any pair of connected nodes is a 'trunkward/leafwards relationship - there are no direct peer relationships.
*A node must have at least two accounts, and no more than one trunkward account.
*A node with no trunkward node is the trunk node.
*An account which doesn't connect to a leafwards node, is a 'leaf' account, for end-users.
A transaction which traverses more than node is called 'transversal'. The transaction is recorded on every intermediate node. Each version of the transaction is between two accounts on that node. Trunkwards nodes do not (need to) store account names on remote transactions (but they do need to relay them).
To accommodate the world's population might take about nine levels with an average of 12.5 accounts per node. Governance of the nodes is supposed to be done by and for members, and indeed it affects only members. So a regional node might select one or two of its members to represent it at the national level, those members themselves having been chosen to represent their districts, and so on.
It should be possible to create an automated routine to convert a member account into a remote node, but other processes like moving accounts between nodes, or creating new trunkward nodes, require politics.

### Transactions

A transaction consists of some header information and a series of entries. The first or main entry contains the payer and payee, quantity and description, while other entries can be added by any node the transaction crosses. Once written to the database, the entries do not change, but as the transaction moves along its workflow path, the header information does change.

A Transaction consists of:

- a UUID, 
- the timestamp it was last written
- a 'type' indicating which workflow path it is on.
- the current workflow state
- the version number.
- one or more 'entries', consisting of:
  - the payer
  - the payee
  - a quantity expressed in the local accounting unit, 
  - a description,

### Workflows

A workflow describes all the possible paths a transaction can take in its journey from creation to finalisation, or sometimes deletion. It is comprised of transaction states, and transitions, and it defines which account can perform which transitions on transactions in each state. A json format to for workflows is provided. For nodes to share transactions they MUST have a common idea of the workflow. To ensure that nodes always have a workflow in commons, each node can read the workflows of all trunkward nodes.
The current workflow format is slightly limited for simplicity's sake, in that it does not allow multiple account's approval before moving to another transaction state. This means if a transaction created by a third party requires approval of the payer and the payee, in any order, then two intermediate states would have to be created i.e payer_approved and payee_approved and two pathways to state_completed.

### Account holders

The policy service manages the accounts which can authenticate and use the ledger. The unique id is what it written in the ledger to assocate transactions with that account. Accounts can be either local or remote, meaning that it refers not to a person but to another node at another url. Local accounts are shown as black and remote accounts are shown as grey on the barcharts in this reference implementation.

Accounts have permissions over transactions which are defined in the workflow. At the moment all transactions are visible to all accounts except the trunkward account which can see nothing by default.

This reference implementation maintains the following attributes in respect of all account holders:

- An ID unique to that ledger, such as a login name. Users are globally addressable with the ledger address and this ID, and this is ID is used in the transaction table.
- Min and max balance limits for that account
- Url if that account is remote.
- Status of the account where zero means the account is disabled. Note that EU Law requires that all user info must be deletable, so it is a good idea to use anonymised account names so that the ledger can remain intact when an account is deleted.

### Accounting Units

Accounting units are used to calculate the value of things in relation to other things. Goods or services must be priced in a common accounting unit in order to particpate in the exchange. The balance of an account it the sum of the value of the transactions in which that account was payee minus the sum of the value of the transactions in which it was the payer. Thus new accounts with no transactions in the ledger have balance of zero, by definition.

A unit doesn't have to correspond to anything in the real world any more than a dollar does. It only expresses value on one ledger numerically so it can easily be expressed as a quantity of value on another ledger.

Each leafward node has a conversion rate with respect to its trunkward node and when sending transactions trunkward, multipies up the quantities in each entry of the transaction. The trunkward node is not aware of the exchange rates of all its member accounts. Leafward nodes can change their exchange rates at will. More work needs to be done to see whether that mechanism encourages good or bad behaviour.

### Account Limits 
Accounts can only trade within limits straddling zero. E.g. +100, -100. The limits or the algorithm determining the limits are set through a governance process. A transaction is only valid if the payee's resulting balance is less than the maximum limit and the payer's resulting balance is greater than the minimum limit. When a transaction fails validation, the LimitViolation specifies the ledger and the limits and the amount by which the limit would have been exceeded. It may be possible for a ledger to assign individual balance limits on the basis not of an algorithm but on the basis of a negotiation between the members. 
Transactions might be validated against either the main balance or the pending balance.

### Paths
-   Accounts and ledgers have names and addresses within the node tree. Each account has a name (every node is an account on its trunkward node) and is addressed relative to whereever you are.
Thus from anywhere in `london`, an address like this might suffice:
*brixton/hill/johndoe1*
But from anywhere in the `world` a longer address would be needed for the same account.
*europe/uk/southeast/london/brixton/hill/johndoe1*
Note that none of these nodes exists yet.
When a node doesn't recognise an address, it passes the transaction trunkward until the first part of the address corresponds to an account name. Then it is passed leafward as each part of the path corresponds to an account name on successive nodes.

### Balance-of-Trade account

The trunkward account of any node, if it has one, can be thought of as the Balance-of-Trade account with respect to the rest-of-the-world. This account stores the balance between all the node members and the rest of the tree. Typically, in order to connect to the trunkward node, the members must commit to restoring this account's balance to zero in the long run. This account corresponds to this node's account on the trunkward node, and its balance is the NEGATIVE of that account. Thus a payment from a local account to a remote one will debit the local account and credit the BoT account on the local node, but on the trunkward node the local node's account would be debited and the remote node's account credited. On the remote node the trunkward account would be debited and the recipient's account credited. The correspondance between the two nodes and their accounts on the trunkward node is easily checked with a hashchain.

### Hashchain

When a transaction is shared accross more than one ledger, the protocol ensures that both ledgers have the same record of the same transaction, moreover that the accounts that represent each legder to the other, are synchronised. This is done using a hash chain, and the ledgers authenticate with each other using the hash (there is also a 'handshake' method for use with cron if desired). The has is comprised of transaction header fields such as as the transaction id and workflow name, also the transaction version number which increments with every workflow progression, as well as the quantity and description from each entry. If the hashes between two nodes do not match for any reason, they cannot process shared transactions, and an administrator should be contacted immediately. Currently no provision is made to allow provisional transactions in this scenario.


## Fundamental Conditions
### Governance
A Ledger will be governed by its Account Holders through a governance process which may involve one many or all accounts on the ledger. The fundamental responsibility of Governance is to operate the systems which control policy. Policy means rules for membership, business logic, and trade validation - critically including Applicant Approval and Account Limit conditions. In practice it is considered that a variety of other issues will be addressed through Governance. 
Governance decisions are documented and maintained in a log, and the results of decisions constitute the Configuration of the Ledger.
### Applicant
Any entity which is capable of applying for Account Holder status.
Applicants are required to provide (at least) the following information:

-   Public name
-   Contact details

### Inheritance
Leafward ledgers inherit required characteristics (namespace) from trunkward ledgers.

### Privacy & Visibility
The protocol provides methods for child ledgers to retrieve all transaction data and balance limit settings from parent ledgers, but by default parents are not be able, and do not need to interrogate child ledgers. A transaction spanning many ledgers will not store the names or IDs of the traders in the parent ledgers, minimising data custody problems. It will only store the transaction as between two of its accounts e.g. brixton hill & the intertrading account or between Brixton and Lambeth.

## Use Cases - Account holder
### Filter
An Account Holder asks its Ledger (or any parent ledger) for a log of Transactions, filtered by various criteria.
Transaction histories information consists only of:
-   Transaction UUID
-   Transaction Date
-   Transaction Amount
-   Initiating Account Holder ID / Public Name
-   Counterparty Account Holder ID / Public Name.

### Transact 
An Account Holder initiates a Transaction by supplying:
-   name or path of the payer
-   the number of units,
-   A description of the trade.
-   a workflow name (or the ledger can supply a default)

The Account Holder will expect to be notified of the successful completion of the Transaction (in which case their Account Balance will have been updated and the Transaction written to the Ledger), or of its failure.

### Confirm Transaction
This is an example of a transaction operation.
An Account Holder is notified indicating that they have been identified as the counterparty in a new transaction.
They may respond to this transaction with a confirmation, or a denial. 
Failing to respond is the equivalent of a denial. Specifics as to time limits would be a Governance issue.
The Account Holder will expect to be notified of the successful completion of the Transaction (in which case their Account Balance will have been updated and the Transaction written to the Ledger), or of its failure.

### view account details
An Account Holder may request the account balance of any member of the same node or any trunkward node - as described under Privacy and Visibility.

### Limits Request
An account holder may request all information held an account (in accordance with GDPR law) including transaction statistics such as balance, balance limits, number of trades, volume of trade etc. This applies to all accounts on all trunkward nodes. Leafward nodes may choose to keep this info private.

## Use Cases - Governance
### Approve Applicant
New accounts can be created disabled by default, and then the node governance process holds the operational responsibility for enabling those accounts, on the basis of whatever conditions have been set.
### Validate Transaction 
Node governance holds the operational responsibility for validating pending transactions in respect of account limits which have been set through governance processes.
In practice, this will usually be delegated to software.
