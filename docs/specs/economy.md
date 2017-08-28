# Economy (for communities & bars)
Version: 0.1-draft (2017-08-27)

A economy is part of a community, and may be linked to bars inside that community.
The economy specifies various money related properties for the bars it is attached to.

## Economy model
`economy`:
- `id`: index
- `community_id`: reference to a community
- `name`: name of the economy, by community owners
- `created_at`: time the economy was created at
- `updated_at`: time the economy was last updated at

What is configured inside an economy:  
- [Supported currencies](/docs/specs/currencies.md#supported-currencies)
- [Bank accounts](/docs/specs/bank_accounts.md#bank-accounts)
- [Economy payment services](/docs/specs/payment_services.md#payment-services)
