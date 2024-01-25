DROP TABLE IF EXISTS transactions;
CREATE TABLE transactions (
  id int(11) NOT NULL comment 'Internal transaction id',
  uuid varchar(40) NOT NULL comment 'Universal transaction id',
  version int(2) NOT NULL DEFAULT 1 comment 'Transaction version number',
  type varchar(16) NOT NULL comment 'Workflow id',
  scribe varchar(64) NOT NULL comment 'User which wrote this version.',
  state enum('validated', 'pending', 'completed', 'erased', 'timedout') NOT NULL comment 'Other states exist but are never written',
  written DATETIME
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

ALTER TABLE `transactions`
  ADD PRIMARY KEY (`id`),
  ADD UNIQUE KEY `uuid version` (`uuid`,`version`);

ALTER TABLE transactions
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS entries;
CREATE TABLE entries (
  id int(11) NOT NULL,
  txid int(11) NOT NULL comment 'corresponds to transactions table id',
  payee varchar(64) NOT NULL,
  payer varchar(64) NOT NULL,
  description tinytext NOT NULL,
  quant BIGINT NOT NULL,
  trunkward_quant tinytext,
  author varchar(32) NOT NULL,
  is_primary int(1) NOT NULL DEFAULT 0 comment 'boolean',
  metadata text(1024) COMMENT 'serialised stdClass'
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT = 'These are written once and theoretically never change';

ALTER TABLE entries ADD PRIMARY KEY (id);
ALTER TABLE entries MODIFY id int(11) NOT NULL AUTO_INCREMENT;

DROP TABLE IF EXISTS hash_history;
CREATE TABLE `hash_history` (
  txid int NOT NULL,
  acc_id varchar(32) NOT NULL,
  hash varchar(32) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;
ALTER TABLE `hash_history` ADD UNIQUE KEY `unique` (`acc_id`,`txid`);

# This view of the ledger should be helpful for getting user-centric data.
DROP TABLE IF EXISTS transaction_index;
CREATE TABLE `transaction_index` (
  id int NOT NULL DEFAULT '0',
  uuid varchar(40) NOT NULL,
  uid1 varchar(64) NOT NULL,
  uid2 varchar(64) NOT NULL,
  type varchar(16) NOT NULL,
  state varchar(9) NOT NULL,
  income BIGINT(11)  NOT NULL COMMENT 'for aggregated queries: positive or zero for uid1',
  expenditure BIGINT(11) NOT NULL COMMENT 'for aggregated queries: positive or zero for uid1',
  diff BIGINT(11) NOT NULL COMMENT 'for aggregated queries: positive for payee, negative for payer',
  volume BIGINT(11) NOT NULL COMMENT 'for aggregated queries: always positive',
  written datetime NOT NULL,
  is_primary int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT = 'Pending and completed transactions';

DROP TABLE IF EXISTS log;
CREATE TABLE log (
  id int(11) NOT NULL,
  timestamp DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
  method tinytext NOT NULL,
  path text NOT NULL,
  request_headers text NOT NULL,
  request_body text NOT NULL,
  response_code int(3) DEFAULT NULL,
  response_body text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COMMENT 'all requests and responses';

ALTER TABLE log
  ADD PRIMARY KEY (id);

ALTER TABLE log
  MODIFY id int(11) NOT NULL AUTO_INCREMENT;