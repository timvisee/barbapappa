# Payment
Version: 0.1-draft (2017-08-28)

A payment defines a single transaction that is made through a third party payment service.
This could be through PayPal, through iDeal, it could be a manual bank transfer or whatever is available for the relevant bank.

This payment is attached to a payment mutation for a transaction.

Additional data might be attached for some payment methods when relevant.
Think of PayPal specific transaction IDs or an IBAN a user might be sending money from.

When the state of the transaction is changed by the payment service, the state of this payment should be changed accordingly.  
The state of any related payment mutations should be updated along with it.

### Payment model
`payment`:
- `id`: index
- `state`: state of the payment
    - 1: `pending`: waiting for the payment to be started
    - 2: `processing`: waiting on the payment service to finish processing
    - 3: `completed`: the payment has been completed
    - 4: `revoked`: the payment has been revoked by Barbapappa, possibly by a user through the Barbapappa platform
    - 5: `rejected`: the payment was rejected at the payment service, possibly by the user through the payment platform
    - 6: `failed`: the payment failed due to an error or another issue
- `payment_service_id`: reference to the payment service that was used
- `reference`: an unique ID/token used for reference
- `money`: the money receiving by this payment
- `currency`: currency identifier
- `created_at`: the time this payment was created at
- `updated_at`: the time this payment was last updated at

Removed from table data:  
> - `payment_type`: payment type
>     - 1: manual IBAN transfer
>     - 2: bunq payment request
>     - 3: bunq automated IBAN transfer

### Manual IBAN transfer payment
`payment_manual_iban`:
- `id`: index
- `payment_id`: reference to the payment
- `to_bank_account_iban_id`: reference to the receiving IBAN account
- `from_bank_account_iban_id`: reference to the sending IBAN account
- `approved_user_id`: reference to the user that approved this payment
- `approved_at`: the time the payment was approved at

### bunq payment request payment
`payment_manual_bunq_request`:
- `id`: index
- `payment_id`: reference to the payment
- TODO: bunq payment request identifier

### bunq automated IBAN transfer payment
`payment_manual_bunq_automated`:
- `id`: index
- `payment_id`: reference to the payment
- `user_bank_account_iban_id`: reference to the IBAN account of the user
- TODO: bunq banking account identifier
