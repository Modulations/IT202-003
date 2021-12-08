CREATE TABLE IF NOT EXISTS Accounts(
    id int AUTO_INCREMENT PRIMARY KEY,
    account varchar(12) unique,
    user_id int,
    account_id varchar(12) unique 0,
    created TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    modified TIMESTAMP DEFAULT CURRENT_TIMESTAMP on update CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES Users(id),
    FOREIGN KEY (account_id) REFERENCES Accounts(account),
    check LENGTH(account) = 12
)
-- aka james bond: drop table 007