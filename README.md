[![Pipeline status on GitLab CI][pipeline-badge]][pipeline-link]

# Barbapappa
Barbapappa bar management application.

## Installation
This application requires some installation steps.

### Initial setup
The project can be installed and configured by running the following commands:
```
cd webapp

# Install composer dependencies
composer install # or update

# Install npm dependencies
npm install
```

### Environment file
Create a file called `.env` based on the `.env.example` file and configure 
your database and mail credentials.

### Database
To initialize the database with tables, the database migration might be invoked:
`php ./artisan migrate`

If the project is updated, the same command should be invoked as well to update the database structure.

### Compiling resources
Some resources are compiled, and need to be recompiled before they're used in the public application.
Style sheets and client side JavaScript are such files.

To recompile resources, run the following command: `npm run dev`

### Worker
This project makes use of workers to process tasks in the background.
Emails are mostly being sent using these queues as it would otherwise drastically
increase the response time of the web application.
The worker for these queues needs to be started however.

#### Starting the worker
To start the worker, use the helper script: `./startWorker`  
Or start it directly (not prioritised): `php ./artisan queue:work`

#### Keep the worker running
The worker must be kept running at all times.
Check out the [Supervisor configuration](https://laravel.com/docs/5.4/queues#supervisor-configuration)
in the Laravel documentation for more information.

#### Restart the worker
When the project is updated, the worker must be restarted using the helper script: `./restartWorker`  
Or run the command directly: `php ./artisan queue:restart`

This destroy the worker process.
It should automatically restart again if the _Supervisor_ is configured correctly.

### Administrator user
To manage the Barbapappa instance, you must create an administrator user. Invoke
the following console command with your credentials to create a new user:

```bash
php ./artisan user:add example@example.com John Doe --password --administrator
```

After creation you may login at `/login`. The management pages can be reached at
`/manage`.

## Development
First make sure the project is successfully installed and configured.
See the section above.

When you've made changes to the project, make sure to:
- Migrate the database
- Recompile the resources
- Restart the worker

#### Compiled resources
Some resources have to be compiled, see the _Compiled resources_ section above.

To watch the resources and automatically recompile on change, use: `npm run watch`

## Builds
This project is currently automatically tested for each commit using CI services.

|Service|Platforms|PHP|Branch|Build Status| |
|---:|:---|:---|:---|:---:|:---|
|GitLab CI|Linux|8.0|master|[![Pipeline status on GitLab CI on master][pipeline-badge]][pipeline-link]|[View Status][pipeline-link]|

Note: Tests are currently fairly basic, and should be improved at a later time.

## About
This project is currently developed and maintained by [Tim Visée](https://github.com/timvisee), [www.timvisee.com](https://timvisee.com/).

## License
This project is released under the GNU AGPL-3.0 licence.
Check out the [LICENSE](LICENSE) file for more information.

[pipeline-badge]: https://gitlab.com/timvisee/barbapappa/badges/master/pipeline.svg
[pipeline-link]: https://gitlab.com/timvisee/barbapappa/pipelines
