# Mutation
Version: 0.1-draft (2017-08-28)

Transactions contain _Mutations_ to describe the money flow and behaviour for a transaction.

Transactions have a virtual transaction sum value.
Money is added to xor subtracted from this value by mutations.
Transactions therefore define the money flow.

Mutation examples:  
- Money is subtracted by a user buying products (subtracts from the transaction sum)
- Money is added xor subtracted from a users wallet (adds to xor subtracts from the transaction sum)
- Money is added by a deposit through a payment service (adds to the transaction sum)

A valid transaction should also have a resulting sum value of zero when all mutations are considered,
because money that flows somewhere, has to get from somewhere.
Therefore, transactions that have a sum value other than zero are inherently invalid.

The transaction money sum is grouped by economy and currency.
When €10 is added, it must also be subtracted by another mutation to the same economy.  

_The following transaction mutations would be valid:_  
- `+€1, +€2, +$3, -€3, -$3 == €0, $0`
- `+€5(econ a), +€7(econ b), -€5(econ a), -€7(econ b) == €0(econ a), €0(econ b)`  

_The following transaction mutations would be invalid:_  
- `+€1, +$2, -€1 == €0, $2`
- `+€3, +$7, -€10 == €-7, $7`
- `+€4(econ a), +€3(econ b), -€7(econ a) == €-3(econ a), €3(econ b)`

Mutations only flow money in one direction.
To further clarify; mutations only add money to xor subtract money from the transaction sum.
If both directions are desired, two separate mutations are used.
Because of this, a transaction must have 2 or more mutations to be valid.

It is entirely possible to have two mutations for adding money to the transaction,
and a single mutation subtracting money from the transaction.
This would occur if a user buys a product for €10,
where two wallets were used for the payment both adding €5 euros.

To visualize an example case, this shows the money flow for a user buying a product for €10 with their virtual wallet.
```
              +-----------------------+       +-------------+
* Mutation 1: | User wallet           | ====> | Transaction |
              | - Payment for product |   |   | (Money sum) |
              +-----------------------+   |   +-------------+
                                          |          |
                                          |   +-------------+       +-----------------+
* Mutation 2:                             |   | Transaction | ====> | Bought products |
                                          |   | (Money sum) |   |   | - Costs money   |
                                          |   +-------------+   |   +-----------------+
                                          |                     |
                                          |                     |
                                        €10,-                 €10,-
                                    Wallet mutation      Product mutation
```

## Mutation constraints
For all mutations, some constraints apply regarding possibilities with transferring money between multiple accounts.

It is not possible to create a transaction to transfer money from one wallet to another,
where the two wallets are used for different bars (and thus, different banking accounts).

If mutations in a transaction add €10 to the transaction money sum,
that €10 must also be subtracted by other mutations related to the same bar (and banking account).

Transactions with mutations to multiple bars are possible if their bar specific money sums are all equal zero.

## Mutation dependency
A mutation may depend on another mutation.
The mutation that depends will wait until the dependent mutation has been successfully processed.

## Mutation mutability
The properties of a mutation may be changed, when it's in the `pending` or `processing` state.
In any different state, the mutation is frozen.

## Mutation model
`mutation`:  
- `id`: index
- `transaction_id`: reference to a owning transaction
- `type`: type of mutation
    - 1: magic mutation
    - 2: wallet mutation
    - 3: product mutation
    - 4: payment mutation
- `money`: money this mutation processed
- `currency`: currency identifier
- `state`: mutation processing state
    - 1: `pending`: waiting on the system or on a dependency for processing
    - 2: `processing`: waiting on the mutation to complete
    - 3: `success`: mutation successfully applied
    - 4: `failed`: mutation failed
- `depend_on`: optional reference to mutation which must complete first
- `created_at`: time this mutation was created at
- `updated_at`: time this mutation was last updated at

Note: the `updated_at` value must be changed when the child mutation state is changed.

## Mutation types
There are various types of mutations that are attached to a transaction.
One mutation might modify the balance in a wallet,
while another mutation might define a user has bought products.

Types:  
- [Wallet mutation](#wallet-mutation)
- [Payment mutation](#payment-mutation)
- [Product mutation](#product-mutation)
- [Magic mutation](#magic-mutation)
- _Additional mutation types may be added in the future to support more transaction types._

Each type is further documented in it's own paragraph below.

### Wallet mutation
A wallet mutation adds money to xor subtracts money from a users wallet.

The wallet that is _mutated_ is attached to this mutation instance.

If money is subtracted from a users wallet, the mutation `money` field will be positive because money is added to the transaction sum.
Therefore if money is added to a users wallet, the mutation `money` field will be negative.

#### Wallet mutation model
`mutation_wallet`:  
- `id`: index
- `mutation_id`: reference to the super mutation
- `wallet_id`: reference to the related wallet
- `balance_before`: balance at the moment of completing this mutation, just before the money change is applied

### Payment mutation
A payment mutation defines a money transfer using a supported payment service.

The payment (model) instance for this payment service transaction is attached to this mutation.
This attached model further defines the service that was actually used, the state of the payment,
and possibly some other relevant parameters.

This mutation would be used for a transaction of a user depositing money to a wallet.
If a user would deposit this money though PayPal or a similar service, 
this mutation defines the money flow for PayPal.

The success state of this mutation must be updated accordingly when the state of the attached payment changes.

If €10 would be deposited to a wallet, the payment mutation would have a `money` field value above zero,
as the mutation adds money to the transaction money sum.
Therefore if money was deposited to a user's payment service account (possibly a cashback to a bank account, because a payment failed)
the `money` field value would be below zero.

#### Payment mutation model
`mutation_payment`:  
- `id`: index
- `mutation_id`: reference to the super mutation
- `payment_id`: reference to the payment

### Product mutation
A product mutation defines money being consumed because a user buys any number of products.

Of course, the bought products are attached to this mutation state along with their individual price at time of buying and their quantities.

If a user would buy products for €10, the `money` field value would be €-10 because money is subtracted from the transaction sum.

Product mutations with a `money` field value of zero or above are currently not supported,
as these would define a mutation that a user receives money for supplying a product.
Support for this might be added in the future if there's a proper use case that requires this state.

#### Product mutation model
`mutation_product`:  
- `id`: index
- `mutation_id`: reference to the super mutation
- `bar_id`: reference to the bar the products were bought from
- `product_bag_id`: reference to a product bag defining the products

### Magic mutation
A magic mutation is a special kind of mutation, and it allows many special cases to be handled.
It is used for mutations that don't fit in the other mutation types available.

This kind of mutation doesn't explicitly specify what the mutation is for, with for example a attached payment or wallet.
Except for the fact that a `description` field is available on this mutation.
This should be filled in by the initiating user when possible to further describe what this magical mutation is about.
The `description` field is purely for accounting purposes and isn't interpreted by BARbapAPPa itself.

Additionally, a magic mutation isn't tied to a specific bar or banking account.
Because of this, a single magic mutation may be used to add money to two wallets on different bars or banking accounts.

To give a better picture of what this magic mutation might be used for, take a look at the following use case:  
In a given BARbapAPPa setup, users have a default wallet balance of €10,-.
The balance of an initialized wallet is zero. To get to default balance specified, a transaction must be used.
Of course, this transaction would contain a wallet mutation to add €10 to the user's wallet.
Because the money sum of a transaction must be zero, the money has to flow from somewhere.
For this specific case, the second mutation might be a magic mutation.
This because it doesn't fit the other mutation types,
and because from the systems perspective the money would appear to be _spawned from thin air_.

#### Magic mutation model
`mutation_magic`:  
- `id`: index
- `mutation_id`: reference to the super mutation
- `description`: description by the user
