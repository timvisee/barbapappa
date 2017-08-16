# Development documentation
This document shows some development workflows and commands to use for development.

## Controllers
### Create
Controller with resource methods:  
`php artisan make:controller PostsController --resource`

Clean controller:  
`php artisan make:controller PostsController`

## Model
### Create
Create a model:  
`php artisan make:model Post`

This creates an empty model, along with a database migration file.
Please note that this database migration file must be configured,
and the proper fields must be added to it.

## Database
### Create migration
Make sure the name is as descriptive as possible.  
`php artisan make:migration add_user_id_to_posts`

After creating the migration,
the `up` and `down` methods must be filled in in the migration file.