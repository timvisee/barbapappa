# Bank accounts
Version: 0.1-draft (2017-08-28)

The global application, economies and users may have a list of banking accounts.
A bank account might be an IBAN number, a PayPal reference or something else.

It is required to add bank accounts, because it's used for some payment services.

When adding a manual payment option, an IBAN that should be used must be specified.  
When adding a PayPal payment option, an PayPal reference that should be used must be specified.

Banking accounts aren't normaly visible to regular users, unless they've added the account themselves,
or if the banking information is required for a payment because a user has to transfer money.

A bank account may be part of the following contexts:  
- application: global application account, for global payment providers
    - `economy_id` and `user_id` are `null`.
- economy: accounts for payment services in an economy
    - `economy_id` is not `null`, `user_id` is `null`.
- user: accounts added by a user for payments
    - `economy_id` is `null`, `user_id` is not `null`.

## Bank account model
`bank_account`:  
- `id`: index
- `economy_id`: optional reference to an economy
- `user_id`: optional reference to a usk
- `name`: display name
- `type`: account type
    - 1: `IBAN`: an IBAN account
- `created_at`: time the account was created at
- `updated_at`: time the account was last updated at

## IBAN account model
`bank_account_iban`:  
- `id`: index
- `bank_account_id`: reference to a bank account
- `iban`: full IBAN number
- `bic`: optional BIC number
- `owner_name`: full name of the account holder
