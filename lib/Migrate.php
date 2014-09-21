<?php
namespace voilab\migrate;


class Migrate {

    private static $instance;
    private $config;

    /**
     * @var \PDO
     */
    private $dblol;



/* -------------- static methods -------------------------------------------- */

    /**
     * Get instance
     *
     * @param array $config
     * @return Migrate
     */
    public static function getInstance($config) {
        if (!self::$instance instanceof Migrate) {
            self::$instance = new self($config);
        }

        return self::$instance;
    }

/* ------------ / static methods -------------------------------------------- */




    private function __construct($config) {
        $this->config = $config;

        // connect to the database
        $this->database();
    }

    /**
     * Execute the migration
     * @param int $version
     */
    public function migrateTo($version) {
        $files = $this->getUpcomingMigrations($version);

        foreach ($files as $file) {
            $extension = substr(strrchr($file, '.'), 1);
            $version = $this->getVersionFromFilename($file);

            $result = false;
            if ($extension == 'sql') {
                $result = $this->runSqlMigration($file);
            }
            if ($extension == 'php') {
                $result = $this->runPhpMigration($file);
            }

            if (!$result) {
                echo sprintf("Error: migration %s failed.\n", $version);
                echo sprintf("Migration process stopped. Database is at version %s.\n", $version-1);
                die;
            }

            $this->updateDatabaseVersion($version);
        }
    }

    /**
     * Execute the installation script
     */
    public function install() {
        $sql = file_get_contents(__DIR__ . '/install/install.sql');
        return $this->prepare($sql);
    }




/* -------------- private methods ------------------------------------------- */

    private function database() {
        if ($this->dblol)
            return;

        try {
            $db = $this->config['database'];
            $this->dblol = new \PDO($db['adapter'] . ':host=' . $db['host'] . ';dbname=' . $db['dbname'], $db['user'], $db['pass']);
            $this->dblol->setAttribute(\PDO::ATTR_ERRMODE, \PDO::ERRMODE_EXCEPTION);
        } catch (\Exception $e) {
            throw new \Exception('Could not connect to database. Message: ' . $e->getMessage());
        }
    }

    /**
     * @param $sql
     * @param array $params
     * @return \PDOStatement
     * @throws \Exception
     */
    private function prepare($sql, $params = array()) {
        try {
            $sth = $this->dblol->prepare($sql, array(\PDO::ATTR_CURSOR => \PDO::CURSOR_FWDONLY));
            $sth->execute($params);
            return $sth;
        } catch (\PDOException $e) {
            throw new \Exception("Query error: {$e->getMessage()} - {$sql}");
        }
    }

    /**
     * Get the version from which to start the migrations
     *
     * @return int
     * @throws \Exception
     */
    private function getStart() {
        $sql = "SELECT version FROM migration_version";
        $sth = $this->prepare($sql);
        $line = $sth->fetch(\PDO::FETCH_ASSOC);
        return $line['version'] + 1;
    }

    /**
     * Retrieve the migration that will be run, ordered by version.
     *
     * @param int $maxVersion
     * @return array
     */
    private function getUpcomingMigrations($maxVersion = null) {
        $start = $this->getStart();

        $files = glob($this->config['migrations_path'] . '*_*.{php,sql}', GLOB_BRACE);

        $files = array_filter($files, function ($item) use ($start, $maxVersion) {
            $version = $this->getVersionFromFilename($item);
            if ($version < $start) {
                return false;
            }
            if ($maxVersion && $version > $maxVersion) {
                return false;
            }
            return true;
        });

        usort($files, function ($a, $b) {
            $version1 = $this->getVersionFromFilename($a);
            $version2 = $this->getVersionFromFilename($b);
            if ($version1 > $version2) {
                return 1;
            }
            if ($version2 < $version1) {
                return -1;
            }
            return 0;

        });

        return $files;
    }

    private function updateDatabaseVersion($version) {
        $sql = "UPDATE `migration_version` SET version =" . $version . " WHERE 1";
        return $this->prepare($sql);
    }

    /**
     * Run an SQL file
     *
     * @param string $file Migration filename
     * @return \PDOStatement
     */
    private function runSqlMigration($file) {
        try {
            $sql = file_get_contents($file);
            return $this->prepare($sql);
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Run a PHP file
     *
     * @param string $file Migration filename
     * @return bool
     */
    private function runPhpMigration($file) {
        include_once($file);
        $classname = 'Migration' . $this->getVersionFromFilename($file);
        if (!class_exists($classname)) {
            echo sprintf("Class %s does not exist. Please implement it in your file: %s", $classname, $file);
            return false;
        }

        $migration = new $classname();

        if (!method_exists($migration, 'go')) {
            echo sprintf("Method go does not exist in your class %s. Please implement it.", $classname);
            return false;
        }

        if (false === $migration->go()) {
            return false;
        }

        return true;
    }

    private function getVersionFromFilename($file) {
        $tmp = explode('_', $file);
        return strstr($tmp[1], '.', true);
    }

/* ------------ / private methods ------------------------------------------- */
}