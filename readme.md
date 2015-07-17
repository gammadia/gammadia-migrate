# Voilab Migrate

SQL and PHP migration tool for your web application.
Put your migration files (.sql or .php) in a migration directory and voilab-migrate tool will execute them
and keep your database up-to-date.

## How to Install

#### using [Composer](http://getcomposer.org/)

Create a composer.json file in your project root:

```json
{
    "require": {
        "voilab/migrate": "0.2.*"
    }
}
```

Then run the following composer command:

```bash
$ php composer.phar update
```

#### Prerequisites
- You need composer (at least its autoload system) to run that library.

#### Installation
- create a 'migrations' directory in your project
- copy the 'migrate.php' file from /lib/install to the root path of your project, make it executable (chmod +x) and rename it if you want
- run the install.sql script in your database (it will create a small table that keep track of your current migration version).

#### Usage
For now, Voilab Migrate handle 2 types of migrations:
- SQL migration
- PHP migration

###### SQL migration
Simply create your SQL file with the good naming convention.
Name should be something like [custom name]_[version number].sql (i.e. 2014-10-21_14.sql)

###### PHP migration
Create a PHP file, with the same naming convention as the SQL file above.
Your file should look like this:

```php
<?php
class Migration14 {
    public function go(\Voilab\Migrate\Migrate $migrate) {

        try {
            if ($some_custom_condition_if_you_want) {
                $sql = "UPDATE mytable SET somefield = '" . $some_custom_value . "' WHERE some_other_field='hehehe'";
                $migrate->run($sql); // the mandatory go() method receive the Migrate instance as a parameter. So you can use it here without connecting again to the database.
                
                // make something on the filesystem
                if (file_exists(__DIR__ . '/../app/uploads/' . $some_custom_value . '.jpg')) {
                    unlink(__DIR__ . '/../app/uploads/' . $some_custom_value . '.jpg');
                }
                
                echo 'My migration 14 succeeded. Yipee !'; // this text will appear in the console. This is not mandatory...
            }
        } catch (Exception $e) {
            echo "Migration 14: An error occured during the update.";
            return false;
        }
    }
}
```
So you simply have to define a classe named after the migration number and define a go() method in it.
The go() method get the Migrate instance as its first parameter.

If you need other things from your application, just include them the way you want...

###### Run migrations
When you have migrations to pass, run your (maybe renamed) 'migrate.php' script in command-line mode.

```bash
$ php migrate.php
```


## Authors

[Joel Poulin](http://www.voilab.org)

## License

MIT Public License
