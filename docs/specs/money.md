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
Each economy has a list of banking accounts.
A bank account might be an IBAN number or a PayPal reference.

It is required to add bank accounts for some payment services.

When adding a manual payment option, an IBAN that should be used must be specified.  
When adding a PayPal payment option, an PayPal reference that should be used must be specified.

Banking accounts aren't visible to regular users, unless it must be shown to the user because a payment is made.

#### Bank account model
- `id`: index
- `economy_id`: reference to an economy
- `name`: display name
- `type`: account type
    - 1: `IBAN`: an IBAN account
- `created_at`: time the account was created at
- `updated_at`: time the account was last updated at

#### IBAN account model
- `id`: index
- `bank_account_id`: reference to a bank account
- `iban`: full IBAN number
- `bic`: BIC number
- `account_holder_name`: full name of the account holder

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

#### Supported payment services model
- `id`: index
- `economy_id`: reference to an economy
- `service_type`:
    - 2: manual IBAN transfer: a manual IBAN transfer, that must be approved by authorized users
    - 3: bunq request: a payment request through bunq
    - 4: bunq automated IBAN transfer: IBAN transfer that is automatically processed
    
- `can_withdraw`: true if money can be withdrawn through this service
- `enabled`: true if enabled
- `archived`: false if available, true if archived and hidden
- `created_at`: the date this service was created at
- `updated_at`: the date this service was last updated at

## Payment services
TODO

### Manual IBAN transfer
TODO

### Bunq request
TODO

### bunq automated IBAN transfer
TODO
