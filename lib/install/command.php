#!/usr/bin/php
<?php
/**
 * Set these variables according to your own configuration
 *
 * @var string $autoload_path Path from this file to your composer autoload file
 * @var string $migrations_path Path where your migration files stands
 */
$config = [
    'autoload_path' => '/vendors/autoload.php',
    'migrations_path' => '/migrations/', // with end slash
    'database' => [
        'adapter' => 'mysql',
        'dbname' => 'my_database',
        'host' => 'localhost',
        'user' => 'root',
        'pass' => ''
    ]
];

if (in_array($argv[1], array('--help', '-help', '-h', '-?'))) {
    ?>

    This is a command line with one optional param

    Usage :
    <?php echo $argv[0]; ?> <option>

    <option> version where you want to update (if none provided, will update to the latest version).
        With --help, -help, -h,
        et -? options, you'll get this (useless) help.

<?php
} else {
    require_once($config['autoload_path']);

    $version = null;
    if (isset($argv[1]) && $argv[1]) {
        $version = $argv[1];
    }

    \voilab\migrate\Migrate::execute($config, $version);
}