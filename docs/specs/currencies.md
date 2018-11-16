# Currencies
Version: 0.1-draft (2017-08-28)

Multiple currencies are supported in this application.
Administrators are able to manage and configure which currencies are available and which are not.
Currencies can be added at application runtime, and don't have to be configured beforehand.

Bars can choose which of the available currencies are supported, by adding them
as economy currency.

## Custom currencies
Custom currencies are currently not supported.
This feature, might be added later on in a future update however.

In that case, custom currencies will have an unique identifier,
and must not cause issues with existing money handling logic.
Transactions and wallets inherently support these custom currencies.
The custom currencies do have to be excluded from payment methods however. 

## Economy currencies
All economy currencies must be explicitly added to the economy.
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
  
The economy currency model also has an `enabled` field to define whether the currency may be used.
If the field is set to `false` (and thus is disabled), the currency should be hidden from users and product prices.

### Economy currency model
`economy_currencies`:
- `id`: index
- `economy_id`: reference to an economy
- `enabled`: true if enabled and visible, false if disabled and not visible
- `currency_id`: the ID of the currency that is used
- `allow_wallet`: true to allow wallet creation by users in this currency
- `product_price_default`:
    - 1: Specify
    - 2: No price
    - 3: Convert
- `created_at`: the time this model was created at
- `updated_at`: the time this model was last updated at
