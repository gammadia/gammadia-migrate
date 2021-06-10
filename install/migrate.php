#!/usr/bin/php
<?php
/**
 * Set these variables according to your own configuration
 * You'll probably want to include your own configuration file and use it here. No problem.
 * You can change this file the way you want... You can also rename $config to another name if it
 * conflicts with your own config file.
 *
 * @var array{autoload_path: string, migrations_path: string, database: array{adapter: string, dbname: string, host: string, port: string, user: string, pass: string}} $config
 */
$config = [
    // Path from this file to your composer autoload file
    'autoload_path' => 'vendor/autoload.php',

    // Path where your migration files stands
    'migrations_path' => __DIR__ . '/migrations/', // with end slash

    // Your database credentials and configs
    'database' => [
        'adapter' => 'mysql',
        'dbname' => 'mydatabase',
        'host' => 'localhost',
        'port' => '3306',
        'user' => 'root',
        'pass' => ''
    ]
];

if (count($argv) > 1 && in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
    ?>

    This is a command line with one optional param

    Usage :
    <?php echo $argv[0]; ?>
    <option>

    <option> [int] version where you want to update (if none provided, will update to the latest version).
        With --install, -install, it will run the install script to add the control table in your database.
        With --help, -help, -h, and -? options, you'll get this (useless) help.

<?php
} elseif (count($argv) > 1 && in_array($argv[1], array('--install', '-install'))) {
    require_once($config['autoload_path']);

    $migrate = \Voilab\Migrate\Migrate::getInstance($config);
    if ($migrate->install()) {
        echo "Success: database correctly configured.\n";
    }
} else {
    require_once($config['autoload_path']);

    $version = null;
    if (isset($argv[1]) && $argv[1]) {
        $version = $argv[1];
    }

    $migrate = \Voilab\Migrate\Migrate::getInstance($config);
    $migrate->migrateTo($version);
}
