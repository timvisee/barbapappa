# Transactions
Version: 0.1-draft (2017-08-28)

This document describes the basics of the transactions model Barbapappa uses.
It is technical and intended for developers.

## Transaction
A transaction defines, as the term suggests, a transaction.
Transactions are used to define and describe each type of money transfer,
whether that is a user buying a product, or a deposit.

Transaction examples:  
- A user buys products.
- A user deposits money.
- A user transfers money to another user.
- A refund for a failed transaction.

### Mutability
The properties of a transaction that has been created may be changed at any time when the state is still in `pending` or `processing`.
When the transaction reaches a different state, the properties are frozen.

[Mutations](#mutation) may also be added, removed or changed when the transaction is the state described above.

### Transaction model
`transaction`:
- `id`: index
- `description`: optional description
- `state`: transaction processing state
    - 1: `pending`: still pending, waiting for the system to start processing
    - 2: `processing`: currently processing, waiting for all mutations to complete
    - 3: `success`: transaction succeeded, all mutations succeeded, all constraints are met
    - 4: `failed`: transaction failed because of failed mutation
- `reference_to`: optional reference to a previous transaction
- `created_at`: time the transaction was created at
- `updated_at`: time the transaction was last updated at

