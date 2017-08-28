# Money specification
Version: 0.1-draft (2017-08-27)

This document describes the basics of money models BARbapAPPa uses.
It is technical and intended for developers.

## Currencies
Multiple currencies are supported in this application.
Administrators are able to manage and configure which currencies are available and which are not.
Currencies can be added at application runtime, and don't have to be configured beforehand.

Bars can choose which of the available currencies are supported.

### Custom currencies
Custom currencies are currently not supported.
This feature, might be added later on in a future update however.

In that case, custom currencies will have an unique identifier,
and must not cause issues with existing money handling logic.
Transactions and wallets inherently support these custom currencies.
The custom currencies do have to be excluded from payment methods however. 

## Money
Money or balance is stored in a decimal field,
along with an additional field to specify the currency.

## Economy (communities & bars)
A economy is part of a community, and may be linked to bars inside that community.
The economy specifies various money related properties for the bars it is attached to.

### Economy model
- `id`: index
- `community_id`: reference to a community
- `name`: name of the economy, by community owners
- `created_at`: time the economy was created at
- `updated_at`: time the economy was last updated at

What is configured inside an economy:  
- [Supported currencies](#supported-currencies)
- [Bank accounts](#bank-accounts)
- [Economy payment services](#economy-payment-services)

### Supported currencies
All supported currencies must be explicitly added to the economy.
Products inside the economy may specify a price in each currency from then on.

The `allow_wallet` property defines whether users in a bar using this economy are allowed to create a wallet with this currency.

The `product_price_default` field specifies what price defaults should be for products in this economy.
The actual price configuration for the currency on a product can be changed on the product edit page.
This just suggests defaults.

To better understand the product price default values, see the list below:  
- **Specify**: the product doesn't have a price in this currency by default.
  Community owners are prompted however to enter a price when viewing the product edit page.
- **No price**: the product doesn't have a price in this currency by default.
- **Convert**: the price of this product is automatically determined by default,
  by converting prices from other currencies that are set manually.
  If no manual prices are set on the product, the product doesn't have a price in this currency.
  
The supported currency model also has an `enabled` field to define whether the currency may be used.
If the field is set to `false` (and thus is disabled), the currency should be hidden from users and product prices.

#### Supported currency model
- `id`: index
- `economy_id`: reference to an economy
- `enabled`: true if enabled and visible, false if disabled and not visible
- `currency`: the currency identifier
- `allow_wallet`: true to allow wallet creation by users in this currency
- `product_price_default`:
    - 1: Specify
    - 2: No price
    - 3: Convert
- `created_at`: the time this model was created at
- `updated_at`: the time this model was last updated at

### Bank accounts
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

#### Bank account model
`bank_account`:  
- `id`: index
- `economy_id`: optional reference to an economy
- `user_id`: optional reference to a usk
- `name`: display name
- `type`: account type
    - 1: `IBAN`: an IBAN account
- `created_at`: time the account was created at
- `updated_at`: time the account was last updated at

#### IBAN account model
`bank_account_iban`:  
- `id`: index
- `bank_account_id`: reference to a bank account
- `iban`: full IBAN number
- `bic`: optional BIC number
- `owner_name`: full name of the account holder

### Economy payment services
Bank accounts may be linked to an economy to allow deposits and payments though external services.
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
Administrators of the BARbapAPPa platform may add credentials for a general bunq account.
Economy users would then be able to use this service if _provided_.
The economy would only have to configure their IBAN account number.
When the payment is processed through bunq, it is redirected to the proper IBAN of the economy.

#### Payment services model
- `id`: index
- `economy_id`: optional reference to an economy, null to add the service globally
- `service_type`:
    - 2: manual IBAN service: a manual IBAN transfer, that must be approved by authorized users
    - 3: bunq service: a payment through bunq, a payment request or an automated bank transfer
    - 4: provided bunq service: a provided payment through bunq, a payment request or an automated bank transfer
- `can_withdraw`: true if money can be withdrawn through this service
- `enabled`: true if enabled, false if not
- `archived`: false if available, true if archived and hidden
- `created_at`: the date this service was created at
- `updated_at`: the date this service was last updated at

### Manual IBAN service
A manual transfer to an IBAN account owned by the community.

The user using this payment service would need to manually transfer to a given IBAN account.
Authorized people in the community then have to manually confirm the transaction was successful days later.

#### Manual IBAN service model
`service_manual_iban`:  
- `id`: index
- `payment_service_id`: reference to the payment service
- `iban_account_id`: reference to the IBAN banking account to transfer money to
- `created_at`: the date this service was created at
- `updated_at`: the date this service was last updated at

### bunq service
A payment using the bunq service.

This might be using a payment request, or a manual transfer by the user which is automatically checked and validated.

#### bunq service model
`service_bunq`:
- `id`: index
- `payment_service_id`: reference to the payment service
- `share`: true to share and provide this service to others.
- `enable_requests`: enable payment request support
- `enable_auto_transfers`: enable automated transfer check support
- `created_at`: the date this service was created at
- `updated_at`: the date this service was last updated at

### Provided bunq service
This is similar to the regular bunq service as the payment goes through bunq.
However, the bunq service (API configuration, premium account subscription and so on) are provided by another configured service.
The service that provides is possibly configured globally in the application, or in a different economy.

The economy that uses the provided service, only has to specify a target IBAN account all succeede transactions will be sent to.
The rest is done through the providing service.

`service_bunq_provided`:
- `id`: index
- `payment_service_id`: reference to the payment service
- `custom_bunq_transfer_id`: reference to a custom bunq transfer
- `enable_requests`: enable payment request support
- `enable_auto_transfers`: enable automated transfer check support
- `bank_account_iban_id`: reference to an IBAN banking account to send the money to.
- `created_at`: the date this service was created at
- `updated_at`: the date this service was last updated at
