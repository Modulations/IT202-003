CREATE TABLE IF NOT EXISTS Transactions(
    id int AUTO_INCREMENT PRIMARY KEY,
    account_src int,
    account_dest int,
    balance_change int,
    transaction_type varchar(12),
    memo varchar(50),
    expected_total int,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (account_src) REFERENCES Accounts(id),
    FOREIGN KEY (account_dest) REFERENCES Accounts(id)
)