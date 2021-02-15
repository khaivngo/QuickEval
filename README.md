## Description
### Psychometric image evaluation the quick way.

Paired comparison / Category judgement / Artifact marking /
Triplet comparison / Rank order

## Requisites
* Composer PHP package manager
* Node.js
* PHP
* MySQL

## Project installation

#### Edit app settings
```
Rename the .env.example file to .env and edit its content with your database credentials.
```

#### Create database
```
Create a MySQL database with a name matching the one under DB_DATABASE in your .env file.
```

#### cd into project folder from cmd/terminal
```
cd path/to/folder
```

#### Install database tables
```
php artisan migrate
```

#### Add some needed data to tables
```
php artisan db:seed
```

#### Generate keys for laravel passport
```
php artisan passport:install
```

#### Update passport client secret
```
- Copy/paste the bottom client_secret code from the php artisan passport:install command.
- Replace the code with the code after PASSPORT_CLIENT_SECRET in the .env file.
```

#### Build the frontend
```
npm run build
```

## Run for development
```
cd frontend
```

#### Compiles and hot-reloads for development
```
npm run serve
```

#### Compiles and minifies for production
```
npm run build
```

#### Run your tests
```
npm run test
```

#### Lints and fixes files
```
npm run lint
```

#### Run your end-to-end tests
```
npm run test:e2e
```

#### Run your unit tests
```
npm run test:unit
```

#### Customize configuration
See [Configuration Reference](https://cli.vuejs.org/config/).
