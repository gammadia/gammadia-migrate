# Voilab Migrate

SQL and PHP migration tool for your web application.
Put your migration files (.sql or .php) in a migration directory and voilab-migrate tool will execute them
and keep your database up to date.

## How to Install

#### using [Composer](http://getcomposer.org/)

Create a composer.json file in your project root:

```json
{
    "require": {
        "voilab/migrate": "0.1.*"
    }
}
```

Then run the following composer command:

```bash
$ php composer.phar install
```

#### Prerequisites
- You need composer (at least its autoload system) to run that library.

#### Installation
- create a 'migrations' directory in your project
- copy the 'command.php' file from /lib/install to the root path of your project and rename it the way you want
- run the install.sql script in your database (it will create a small table that keep track of your current migration version).

#### Usage

When you have migrations to pass, simply run your (maybe renamed) 'command.php' script in command-line mode.

## Authors

[Joel Poulin](http://www.voilab.org)

## License

MIT Public License
