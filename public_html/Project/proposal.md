# Project Name: Simple Bank

## Project Summary
This project will create a bank simulation for users. They’ll be able to have various accounts, do standard bank functions like deposit, withdraw, internal (user’s accounts)/external(other user’s accounts) transfers, and creating/closing accounts.

## Github Link
https://github.com/Modulations/IT202-003/tree/prod/public_html/Project

## Project Board
https://github.com/Modulations/IT202-003/projects/1

## Website Link:
https://lad5-prod.herokuapp.com/Project/

## Your Name
Luke DaSilva


<!--
### Line item / Feature template (use this for each bullet point)
#### Don't delete this

- [ ] \(mm/dd/yyyy of completion) Feature Title (from the proposal bullet point, if it's a sub-point indent it properly)
  -  List of Evidence of Feature Completion
    - Status: Pending (Completed, Partially working, Incomplete, Pending)
    - Direct Link: (Direct link to the file or files in heroku prod for quick testing (even if it's a protected page))
    - Pull Requests
      - PR link #1 (repeat as necessary)
    - Screenshots
      - Screenshot #1 (paste the image so it uploads to github) (repeat as necessary)
        - Screenshot #1 description explaining what you're trying to show
### End Line item / Feature Template
--> 
### Proposal Checklist and Evidence
https://docs.google.com/document/d/1e5xIGBrQflycZfNOabpNzGQ0rYx7pJv2nA6-BG-YSnA/view

- Milestone 1:
  - User will be able to register a new account
    - Form Fields
      - Username, email, password, confirm password (other fields optional)
      - Email is required and must be validated
      - Username is required
      - Confirm password’s match
    - Users Table
      - Id, username, email, password (60 characters), created, modified
    - Password must be hashed (plain text passwords will lose points)
    - Email should be unique
    - Username should be unique
    - System should let user know if username or email is taken and allow the user to correct the error without wiping/clearing the form
      - The only fields that may be cleared are the password fields
  - User will be able to login to their account (given they enter the correct credentials)
    - Form
      - User can login with email or username
        - This can be done as a single field or as two separate fields
      - Password is required
    - User should see friendly error messages when an account either doesn’t exist or if passwords don’t match
    - Logging in should fetch the user’s details (and roles) and save them into the session.
    - User will be directed to a landing page upon login
      - This is a protected page (non-logged in users shouldn’t have access)
      - This can be home, profile, a dashboard, etc
  - User will be able to logout
    - Logging out will redirect to login page
    - User should see a message that they’ve successfully logged out
    - Session should be destroyed (so the back button doesn’t allow them access back in)
  - Basic security rules implemented
    - Authentication:
      - Function to check if user is logged in
      - Function should be called on appropriate pages that only allow logged in users
    - Roles/Authorization:
      - Have a roles table (see below)
    - Basic Roles implemented
      - Have a Roles table (id, name, description, is_active, modified, created)
      - Have a User Roles table (id, user_id, role_id, is_active, created, modified)
      - Include a function to check if a user has a specific role (we won’t use it for this milestone but it should be usable in the future)
    - Site should have basic styles/theme applied; everything should be styled
      - I.e., forms/input, navigation bar, etc
    - Any output messages/errors should be “user friendly”
      - Any technical errors or debug output displayed will result in a loss of points
    - User will be able to see their profile
      - Email, username, etc
    - User will be able to edit their profile
      - Changing username/email should properly check to see if it’s available before allowing  - the change
      - Any other fields should be properly validated
      - Allow password reset (only if the existing correct password is provided)
        - Hint: logic for the password check would be similar to login

- Milestone 2
  - Create the Accounts table (id, account_number [unique, always 12 characters], user_id, balance (default 0), account_type, created, modified)
  - Project setup steps:
    - Create these as initial setup scripts in the sql folder
      - Create a system user if they don’t exist (this will never be logged into, it’s just to keep things working per system requirements)
      - Create a world account in the Accounts table created below (if it doesn’t exist)
        - Account_number must be “000000000000”
        - User_id must be the id of the system user
        - Account type must be “world”
  - Create the Transactions table (see reference below)
  - Dashboard page
    - Will have links for Create Account, My Accounts, Deposit, Withdraw Transfer, Profile
      - Links that don’t have pages yet should just have href=”#”, you’ll update them later
  - User will be able to create a checking account
    - System will generate a unique 12 digit account number
      - Options (strike out the option you won’t do):
        - Option 1: Generate a random 12 digit/character value; must regenerate if a duplicate - collision occurs
        - ~~Option 2: Generate the number based on the id column; requires inserting a null first to - get the last insert id, then update the record immediately after~~
    - System will associate the account to the user
    - Account type will be set as checking
    - Will require a minimum deposit of $5 (from the world account)
      - Entry will be recorded in the Transaction table as a transaction pair (per notes below)
      - Account Balance will be updated based on SUM of BalanceChange of AccountSrc
    - User will see user-friendly error messages when appropriate
    - User will see user-friendly success message when account is created successfully
      - Redirect user to their Accounts page
  - User will be able to list their accounts
    - Limit results to 5 for now
    - Show account number, account type and balance
  - User will be able to click an account for more information (a.ka. Transaction History page)
    - Show account number, account type, balance, opened/created date
    - Show transaction history (from Transactions table)
      - For now limit results to 10 latest
  - User will be able to deposit/withdraw from their account(s)
    - Form should have a dropdown of their accounts to pick from
      - World account should not be in the dropdown
    - Form should have a field to enter a positive numeric value
      - For now, allow any deposit value (0 - inf)
    - For withdraw, add a check to make sure they can’t withdraw more money than the account has
    - Form should allow the user to record a memo for the transaction
    - Each transaction is recorded as a transaction pair in the Transaction table per the - details below
      - These will reflect on the transaction history page (Account page’s “more info”)
      - After each transaction pair, make sure to update the Account Balance by SUMing the BalanceChange for the AccountSrc
      - This will be done after the insert
      - Deposits will be from the “world account”
        - Must fetch the world account to get the id (do not hard code the id as it may change if the application migrates or gets rebuilt)
      - Withdraws will be to the “world account”
        - Must fetch the world account to get the id (do not hard code the id as it may change if the application migrates or gets rebuilt)
      - Transaction type should show accordingly (deposit/withdraw)
    - Show appropriate user-friendly error messages
    - Show user-friendly success messages

- Milestone 3
  - User will be able to transfer between their accounts
    - Form should include a dropdown first AccountSrc and a dropdown for AccountDest (only accounts the user owns; no world account)
    - Form should include a field for a positive numeric value
    - System shouldn’t allow the user to transfer more funds than what’s available in     AccountSrc
    - Form should allow the user to record a memo for the transaction
    - Each transaction is recorded as a transaction pair in the Transaction table
    - These will reflect in the transaction history page
    - Show appropriate user-friendly error messages
    - Show user-friendly success messages
  - Transaction History page
    - Will show the latest 10 transactions by default
    - User will be able to filter transactions between two dates
    - User will be able to filter transactions by type (deposit, withdraw, transfer)
    - Transactions should paginate results after the initial 10
  - User’s profile page should record/show First and Last name
  - User will be able to transfer funds to another user’s account
    - Form should include a dropdown of the current user’s accounts (as AccountSrc)
    - Form should include a field for the destination user’s last name
    - Form should include a field for the last 4 digits of the destination user’s account number (to lookup AccountDest)
    - Form should include a field for a positive numerical value
    - Form should allow the user to record a memo for the transaction
    - System shouldn’t let the user transfer more than the balance of their account
    - System will lookup appropriate account based on destination user’s last name and the last 4 digits of the account number
    - Show appropriate user-friendly error messages
    - Show user-friendly success messages
    - Transaction will be recorded with the type as “ext-transfer”
    - Each transaction is recorded as a transaction pair in the Transaction table
      - These will reflect in the transaction history page

- Milestone 4
  - User can set their profile to be public or private (will need another column in Users table)
    - If public, hide email address from other users
  - User will be able open a savings account
    - System will generate a 12 digit/character account number per the existing rules (see Checking Account above)
    - System will associate the account to the user
    - Account type will be set as savings
    - Will require a minimum deposit of $5 (from the world account)
      - Entry will be recorded in the Transaction table in a transaction pair (per notes below)
      - Account Balance will be updated based on SUM of BalanceChange of AccountSrc
    - System sets an APY that’ll be used to calculate monthly interest based on the balance of the account
      - Recommended to create a table for “system properties” and have this value stored there and fetched when needed, this will allow you to have an admin account change the value in the future)
    - User will see user-friendly error messages when appropriate
    - User will see user-friendly success message when account is created successfully
      - Redirect user to their Accounts page
  - User will be able to take out a loan
    - System will generate a 12 digit/character account number per the existing rules (see Checking Account above)
    - Account type will be set as loan
    - Will require a minimum value of $500
    - System will show an APY (before the user submits the form)
      - This will be used to add interest to the loan account
      - Recommended to create a table for “system properties” and have this value stored there and fetched when needed, this will allow you to have an admin account change the value in the future)
    - Form will have a dropdown of the user’s accounts of which to deposit the money into
    - Special Case for Loans:
      - Loans will show with a positive balance of what’s required to pay off (although it is a negative since the user owes it)
    - User will transfer funds to the loan account to pay it off
    - Transfers will continue to be recorded in the Transactions table
    - Loan account’s balance will be the balance minus any transfers to this account
    - Interest will be applied to the current loan balance and add to it (causing the user to owe more)
    - A loan with 0 balance will be considered paid off and will not accrue interest and will be eligible to be marked as closed
    - User can’t transfer more money from a loan once it’s been opened and a loan account should not appear in the Account Source dropdowns
  - User will see user-friendly error messages when appropriate
  - User will see user-friendly success message when account is created successfully
    - Redirect user to their Accounts page
  - Listing accounts and/or viewing Account Details should show any applicable APY or “-” if none is set for the particular account (may alternatively just hide the display for these types)
  - User will be able to close an account
    - User must transfer or withdraw all funds out of the account before doing so
    - Account should have a column “active” that will get set as false.
      - All queries for Accounts should be updated to pull only “active” = true accounts (i.e., dropdowns, My Accounts, etc)
      - Do not delete the record, this is a soft delete so it doesn’t break transactions
    - Closed accounts don’t show up anymore
    - If the account is a loan, it must be paid off in full first
  - Admin role (leave this section for last)
    - Will be able to search for users by firstname and/or lastname
    - Will be able to look-up specific account numbers (partial match).
    - Will be able to see the transaction history of an account
    - Will be able to freeze an account (this is similar to disable/delete but it’s a different column)
      - Frozen accounts still show in results, but they can’t be interacted with.
      - [Dev note]: Will want to add a column to Accounts table called frozen and default it to false
        - Update transactions logic to not allow frozen accounts to be used for a transaction
    - Will be able to open accounts for specific users
    - Will be able to deactivate a user
      - Requires a new column on the Users table (i.e., is_active)
      - Deactivated users will be restricted from logging in
        - “Sorry your account is no longer active”


### Intructions
#### Don't delete this
1. Pick one project type
2. Create a proposal.md file in the root of your project directory of your GitHub repository
3. Copy the contents of the Google Doc into this readme file
4. Convert the list items to markdown checkboxes (apply any other markdown for organizational purposes)
5. Create a new Project Board on GitHub
   - Choose the Automated Kanban Board Template
   - For each major line item (or sub line item if applicable) create a GitHub issue
   - The title should be the line item text
   - The first comment should be the acceptance criteria (i.e., what you need to accomplish for it to be "complete")
   - Leave these in "to do" status until you start working on them
   - Assign each issue to your Project Board (the right-side panel)
   - Assign each issue to yourself (the right-side panel)
6. As you work
  1. As you work on features, create separate branches for the code in the style of Feature-ShortDescription (using the Milestone branch as the source)
  2. Add, commit, push the related file changes to this branch
  3. Add evidence to the PR (Feat to Milestone) conversation view comments showing the feature being implemented
     - Screenshot(s) of the site view (make sure they clearly show the feature)
     - Screenshot of the database data if applicable
     - Describe each screenshot to specify exactly what's being shown
     - A code snippet screenshot or reference via GitHub markdown may be used as an alternative for evidence that can't be captured on the screen
  4. Update the checklist of the proposal.md file for each feature this is completing (ideally should be 1 branch/pull request per feature, but some cases may have multiple)
    - Basically add an x to the checkbox markdown along with a date after
      - (i.e.,   - [x] (mm/dd/yy) ....) See Template above
    - Add the pull request link as a new indented line for each line item being completed
    - Attach any related issue items on the right-side panel
  5. Merge the Feature Branch into your Milestone branch (this should close the pull request and the attached issues)
    - Merge the Milestone branch into dev, then dev into prod as needed
    - Last two steps are mostly for getting it to prod for delivery of the assignment 
  7. If the attached issues don't close wait until the next step
  8. Merge the updated dev branch into your production branch via a pull request
  9. Close any related issues that didn't auto close
    - You can edit the dropdown on the issue or drag/drop it to the proper column on the project board