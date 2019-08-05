# Payment services
Version: 0.1-draft (2017-08-28)

Payment services may be linked to an economy to allow deposits and payments though external services.
Required information for payment platforms should be supplied here.

For each payment service, various properties might be configured. 

Each payment service can be `enabled`.
By default, payment services are enabled. This allows users to use them.
If a payment service might temporarily need to be disabled, this property should be flipped.

Payment services preferably never be deleted, to ensure transaction history remains valid.
That's what the `archived` property is useful for on a payment service.
The default value is `false`. If set to `true`, the service will be hidden from public and can't be used anymore.

The list of available service types will grow when more payment services are added to the application.

Some services can be configured globally to provide it's service to communities for easy payment integration.
Communities will then be able to add the provided service for payment support with minimal configuration requirements.

For example, the bunq service may be used to generate payment requests.
The use of bunq requires an economy to have a premium bunq account and access to their API.
Administrators of the Barbapappa platform may add credentials for a general bunq account.
Economy users would then be able to use this service if _provided_.
The economy would only have to configure their IBAN account number.
When the payment is processed through bunq, it is redirected to the proper IBAN of the economy.

## Payment services model
`payment_service`:
- `id`: index
- `economy_id`: optional reference to an economy, null to add the service globally
- `service_type`:
    - 2: manual IBAN service: a manual IBAN transfer, that must be approved by authorized users
    - 3: bunq service: a payment through bunq, a payment request or an automated bank transfer
    - 4: provided bunq service: a provided payment through bunq, a payment request or an automated bank transfer
- `deposit`: true if money can be deposited through this service, or false
- `withdraw`: true if money can be withdrawn through this service, or false
- `enabled`: true if enabled, false if not
- `archived`: false if available, true if archived and hidden
- `created_at`: the date this service was created at
- `updated_at`: the date this service was last updated at

## Manual IBAN service
A manual transfer to an IBAN account owned by the community.

The user using this payment service would need to manually transfer to a given IBAN account.
Authorized people in the community then have to manually confirm the transaction was successful days later.

### Manual IBAN service model
`payment_service_manual_iban`:  
- `id`: index
- `payment_service_id`: reference to the payment service
- `iban_account_id`: reference to the IBAN banking account to transfer money to
- `created_at`: the date this service was created at
- `updated_at`: the date this service was last updated at

## bunq service
A payment using the bunq service.

This might be using a payment request, or a manual transfer by the user which is automatically checked and validated.

### bunq service model
`payment_service_bunq`:
- `id`: index
- `payment_service_id`: reference to the payment service
- `share`: true to share and provide this service to others.
- `enable_requests`: enable payment request support
- `enable_auto_transfers`: enable automated transfer check support
- `created_at`: the date this service was created at
- `updated_at`: the date this service was last updated at

## Provided bunq service
This is similar to the regular bunq service as the payment goes through bunq.
However, the bunq service (API configuration, premium account subscription and so on) are provided by another configured service.
The service that provides is possibly configured globally in the application, or in a different economy.

The economy that uses the provided service, only has to specify a target IBAN account all succeede transactions will be sent to.
The rest is done through the providing service.

### Provided bunq service model
`payment_service_bunq_provided`:
- `id`: index
- `payment_service_id`: reference to the payment service
- `custom_bunq_transfer_id`: reference to a custom bunq transfer
- `enable_requests`: enable payment request support
- `enable_auto_transfers`: enable automated transfer check support
- `bank_account_iban_id`: reference to an IBAN banking account to send the money to.
- `created_at`: the date this service was created at
- `updated_at`: the date this service was last updated at
