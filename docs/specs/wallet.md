# Wallet
Version: 0.1-draft (2017-08-28)

A wallet is a virtual purse that is owned by a user.
Wallets are used to quickly make payments to buy products,
so the user doesn't have to initiate a bank transfer for each transaction.

A wallet is created for a specific economy, and the wallet may only be used for payments in that economy in the future.

Users should have the ability to deposit money to their virtual wallet with payment services or other methods.
The available options here are configured by the related bar the wallet is for.

Multiple named wallets may be created by a user in a single economy to allow better financial organization,
by using different wallets for different kind of transactions managed by the user.

## Wallet model
`wallet`:
- `id`: index
- `user_id`: reference to the user
- `economy_id`: reference to the economy
- `name`: name of the wallet decided by the user
- `balance`: current wallet balance
- `currency`: currency identifier
- `created_at`: time the wallet was created at
- `updated_at`: time the wallet was last updated at
