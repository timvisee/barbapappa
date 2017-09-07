# Permissions
Version: 0.1-draft (2017-08-28)

In BARbapAPPa permissions are used to define what a given user is authorised to do.

There is a predefined list of permissions, which are identified by a dot-separated string.  
For example; `user.product.create` would define whether a user is able to create a product in a community.

There's always a boolean value attached to a permission, defining whether the user does or doesn't is allowed or denied to use the related permission.

By default, all permission nodes are `false`.

## Nodes
Here follows a list of each permission node available.
Each node has an unique ID which may be used in a permission entry.

List of nodes: (IDs yet undefined)
- `economy.create`
- `economy.permission.manage`
- `bar.create`
- `bar.join`
- `bar.permission.manage`

## Permission group
Permission groups are groups of users with an additional set of permissions.

There might be an _administrators_ and _normal users_ group where different users are part of.
The _administrators_ would have different permissions to allow economy and bar management.

### Permission group model
`permission_groups`:
- `id`: index
- `name`: name of the permission group
- `enabled`: true if enabled, false if fully ignored
- `community_id`: optional reference to a community this group is for
- `bar_id`: optional reference to a bar this group is for
- `inherit_permission_group_id`: optional reference to a permission group to inherit from
- `created_at`: time the group was created at
- `updated_at`: time the group was last updated at

### Permission entry
A permission entry is a model to define a permission node and it's state for a permission group.

A permission node is referenced by it's permission ID.

#### Permission entry model
`permission_entries`:
- `id`: index
- `permission_group_id`: reference to a permission group this is part of
- `node_id`: ID of a permission node
- `state`: true to allow, false to deny
- `created_at`: time the node was created at
- `updated_at`: time the node was last updated on

### Permission user
The permission group user specifies a user that is part of a permission group.
Of course, multiple users may be added with multiple permission group user instances.

When a user is in multiple groups,
permission properties might override each other depending on the order the groups are evaluated in.

#### Permission user model
`permission_users`:
- `id`: index
- `permission_group_id`: reference to a group
- `user_id`: reference to a user
- `created_at`: the time this model was created at
- `updated_at`: the time this model was last updated at

### Permission selector
A user selector, selects users with some constraint, to automatically add to a permission group.
These selectors may be added to a permission group to eliminate the need of adding users to 

A selector might be used to add all users that have not verified any email address to a group.

A user selector has the following properties to define a constraint:
- `authenticated`: users that are currently authenticated
- `verified`: users that have verified their email address
- `in_community`: users that are in the _current_ community

#### Permission selector model
`permission_selectors`:
- `id`: index
- `permission_group_id`: reference to a permission group
- `is_authenticated`: optional, for authenticated users
- `is_verified`: optional, for verified users
- `is_community`: optional, for users in the community
- `created_at`: time this model was created at
- `updated_at`: time this model was last updated at

## Permission layers
Permission groups exist in a layer, depending on what context the group is created in.

Layers:  
1. `application`:
    - `community_id` and `bar_id` not set.
    - Defines permissions for the whole application.
    - Useful for application administrators.
2. `community`:
    - `community_id` set, `bar_id` not set.
    - Defines permissions inside a community.
    - Useful for community owners.
3. `bar`:
    - `community_id` not set, `bar_id` set.
    - Defines permissions inside a bar.
    - Useful for specific bar management>

Permission groups are evaluated in order of layers.

## Permission context
The context of a user is important for permissions.
It defines the layer, and user selector properties and must be accurate to ensure the proper permissions are evaluated for the user.

Layers:
- In what community is the user currently?
- In what bar is the user currently?

Selectors:
- Is the user authenticated?
- Is the user verified?
- Is the user part of the current community?
