# Irate Framework

The future of a new PHP Framework that allows you to start your application faster.

```
Currently in early stages of development, we do not recommend using in production yet.
```

# Discord

Join our discord server for real-time discussion on feature requests, updates, and more! [Join Now!](https://discord.gg/9Z6bXAr)

# Setup Irate Framework (CLI)
- From the root directory, run `composer install`
- Run the following for setting permissions:

```
sudo chmod -R 777 Application/Cache
sudo chmod -R 777 Application/ViewsCompiled
sudo chmod -R 777 Logs
sudo chmod -R 777 public/generated
cp .env.example .env
```

After, edit the `.env` file to your local setup.

# CSS Framework
[Bulma Documentation](https://bulma.io/documentation/)

# Template Engine
[Smarty Template Engine Documentation](https://www.smarty.net/docs/en/)

# Migrations

To create a migration:
- Create a file in `Application/Migrations/up`: example.sql
- Create a file in `Application/Migrations/down`: example.sql
- Make sure both up and down files are the same filename.

### To run a specific `up` migration:

```
php ir8 migrate up example
```

### To run a specific `down` migration:

```
php ir8 migrate down example
```

### To reset your migrations completely

*This deletes all tables, and re-creates the migration table*

```
php ir8 migrate reset
```

### To run all migrations:

For `up`: `php ir8 migrate all up`
For `down`: `php ir8 migrate all down`

# Documentation

Will be coming out with documentation soon! For now, study the `/Application` directory, it has a few
good examples of how things are supposed to work.
