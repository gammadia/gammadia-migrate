<?php
namespace voilab\migrate;


class Migrate {

    private static $instance;
    private $config;

    /**
     * @var \PDO
     */
    private $dblol;

    /**
     * Shortcut for executing the migrations
     *
     * @param array $config
     * @param int $version Max version of the migration to run
     */
    public static function execute($config, $version = null) {
        if (!self::$instance instanceof Migrate) {
            self::$instance = new self($config, $version);
        }

        self::$instance->migrateTo($version);
    }

    private function __construct($config, $maxVersion) {
        $this->config = $config;

        // connect to the database
        $this->database();
    }

    /**
     * Execute the migration
     * @param int $version
     */
    public function migrateTo($version) {

    }


/* -------------- private methods ------------------------------------------- */

    private function database() {
        if ($this->dblol)
            return;

        try {
            $this->dblol = new \PDO($this->config['adapter'] . ':host=' . $this->config['host'] . ';dbname=' . $this->config['dbname'], $this->config['user'], $this->config['pass']);
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

    private function getUpcomingMigrations() {
        $start = $this->getStart();

        $files = glob($this->config['migrations_path'] . '(*.php|*.sql)');
        echo '<pre>';
        print_r($files);
        echo '</pre>';
    }

/* ------------ / private methods ------------------------------------------- */
}