TODO: allow users to create a temporary custom product, for items that aren't on the list

# Products specification
Version: 0.1-draft (2017-08-27)

This document describes the basics of products models BARbapAPPa uses.
It is technical and intended for developers.

## Product
TODO

## Product price
TODO

## Product bag
A product bag is a virtual bag or container products are stored in.

The product bag model can be attached to various other models,
to keep track of inventory or to specify the number of products that were bought.
So, a product bag may be attached to a wallet mutation, to a bar inventory and what not.

### Bag lifetime
The lifetime of a bag is inherited from the model that references the bag.
The bag should never outlive the using model, and should never become a zombie.

When a bag is used to track inventory, the bag must be deleted when the inventory is deleted for invalidated.

It is of course allowed to create an empty bag without any containing stacks.

### Bag garbage collection
Garbage collection for zombie bags isn't currently documented in this specification.
This might be added in the future to clean up zombie bags.
This task is however expensive, and might cause trouble with undocumented references to a bag.

The availability of garbage collection wouldn't mean product bags shouldn't be deleted anymore after use.

### Product bag model
- `id`: index
- `created_at`: time the bag was created at
- `updated_at`: time the bag was last updated at

### Product stack
The product bag contains stacks of products.
Each stack is for a single kind of product, which is referenced.
The quantity of the products is stored along with it.

#### Stack with a static price
Each stack contains a static `price` field, which is `null` by default.
This field may be set to specify the price of the total stack when
the stack was last updated.
The field value won't change when the price of a referenced product is changed.
This ensures that the price or cost of the stack will remain the same from the
time it was set, whether the product price might change afterwards or not.

The money field is used for wallet mutations,
as it would be used to define the price of the stack at time of making the mutation
even though the price of the actual product might change later on.
This ensures that the money balance of a transaction doesn't change later on.

#### Stacking semantics
Products of the same type should always be summed on a single stack.
Creating multiple stacks for a product is unnecessary.

However, if the same type of product is added to a bag with a different price,
two separate stacks should be used to account for that.
Or else, static price information about the products would be lost,
and the unit price for a stack would not be calculable anymore.

The unit price of a product stack can be calculated using the formula: `unit price = price / quantity`

Stacks that have a quantity of zero should be deleted from the containing bag.
This does invalidate the `created_at` time for stacks,
because it would be changed to the new date when an product of that type is added to the bag again.
Keep that in mind.

Quantities below zero are allowed.
If a product bag with stacks is used to keep track of bar inventory,
it might happen the stack reaches a quantity below zero.
This could be because the bars inventory is unbalanced,
because the quantity got poisoned by users not claiming purchases correctly,
or whatever else.

Make sure to check whether a product bag contains stacks that have a negative quantity,
if that isn't allowed in some use case. For example, when buying products.

#### Product stack model
- `id`: index
- `product_id`: reference to a product
- `quantity`: number of products in this stack
- `price`: optional total price of the products when the stack was created
- `currency`: optional currency identifier the price is in
- `created_at`: time the stack was created at
- `updated_at`: time the stack was last updated at
