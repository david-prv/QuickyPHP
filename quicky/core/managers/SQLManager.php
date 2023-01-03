<?php
/**
 * QuickyPHP - A handmade php micro-framework
 *
 * @author David Dewes <hello@david-dewes.de>
 *
 * Copyright - David Dewes (c) 2022
 */

declare(strict_types=1);

/**
 * Class SQLManager
 */
class SQLManager implements DispatchingInterface, ManagingInterface
{

    /**
     * The client
     *
     * @var SQLClient|null
     */
    private ?SQLClient $client;

    /**
     * Dispatched methods
     *
     * @var array
     */
    private array $dispatching;

    /**
     * Client data
     *
     * @var array
     */
    private array $sqlClientData;

    /**
     * Set a permanent tableName
     *
     * @var string|null
     */
    private ?string $permanentTable;

    /**
     * SQLManager constructor.
     */
    public function __construct()
    {
        $this->dispatching = array("sql");
        $this->sqlClientData = array();
        $this->client = null;
        $this->permanentTable = null;
    }

    /**
     * Add general info
     *
     * @param string $host
     * @param string $username
     * @param string $password
     */
    public function prepareConnection(string $host, string $username, string $password): void
    {
        array_push($this->sqlClientData, $host);
        array_push($this->sqlClientData, $username);
        array_push($this->sqlClientData, $password);
    }

    /**
     * Set database name
     *
     * @param string $dbName
     */
    public function useDB(string $dbName): void
    {
        // prepare connection first!
        if (count($this->sqlClientData) !== 3) return;
        array_push($this->sqlClientData, $dbName);
    }

    /**
     * Optional: Set permanent tableName
     *
     * @param string $tableName
     */
    public function useTable(string $tableName): void
    {
        // prepare connection and define database first!
        if (count($this->sqlClientData) !== 4) return;
        $this->permanentTable = $tableName;
    }

    /**
     * Establish connection
     *
     * @return bool
     */
    public function connect(): bool
    {
        // connection is not prepared or database is not defined!
        if (count($this->sqlClientData) !== 4) return false;

        // connection already open!
        if (!is_null($this->client)) return false;

        try {
            $this->client = new SQLClient(...$this->sqlClientData);
            return true;
        } catch (MySQLConnException $e) {
            $this->client = null;
            return false;
        }
    }

    /**
     * Disconnect from DB
     *
     * @return bool
     */
    public function disconnect(): bool
    {
        if (is_null($this->client)) return false;
        $this->client = null;
        return true;
    }

    /**
     * Returns SQLManager instance
     *
     * @return object|View|null
     * @throws CoreException
     */
    public static function sql()
    {
        $instance = DynamicLoader::getLoader()->getInstance(SQLManager::class);

        if ($instance instanceof SQLManager) return $instance;
        else throw new CoreException();
    }

    /**
     * Checks whether method is dispatched
     *
     * @param string $method
     * @return bool
     */
    public function dispatches(string $method): bool
    {
        return in_array($method, $this->dispatching);
    }

    /**
     * Get first row
     *
     * @param string|null $tableName
     * @param string $columns
     * @param string $where
     * @return string|null
     */
    public function get(string $tableName = null, string $columns = "*", string $where = ""): ?string
    {
        return $this->getAll($tableName, $columns, $where)[0];
    }

    /**
     * Get all rows
     *
     * @param string|null $tableName
     * @param string $columns
     * @param string $where
     * @return array|null
     */
    public function getAll(string $tableName = null, string $columns = "*", string $where = ""): ?array
    {
        if (is_null($tableName) && is_null($this->permanentTable)) return null;
        if (is_null($tableName) && !is_null($this->permanentTable)) $tableName = $this->permanentTable;

        return $this->client->select($tableName, $columns, $where);
    }

    /**
     * Checks if an entry exists
     *
     * @param string|null $tableName
     * @param string $where
     * @return bool
     */
    public function exists(string $tableName = null, string $where = ""): bool
    {
        $res = $this->getAll($tableName, "*", $where);
        return !is_null($res) && count($res) >= 1;
    }

    /**
     * Set or update an entry
     *
     * @param string $column
     * @param string $where
     * @param array $assoc
     * @param string|null $tableName
     */
    public function set(string $column, string $where, array $assoc = [], string $tableName = null): void
    {
        if (is_null($tableName) && is_null($this->permanentTable)) return;
        if (is_null($tableName) && !is_null($this->permanentTable)) $tableName = $this->permanentTable;
        if ($this->exists($tableName, $where)) {
            $this->client->update($tableName, $assoc, $where);
        } else {
            $this->client->insert($tableName, $assoc);
        }
    }

    /**
     * Set a range of entries
     *
     * @param array $assoc
     * @param string|null $column
     * @param string|null $where
     * @param string|null $tableName
     */
    public function setRange(array $assoc, string $column = null, string $where = null, string $tableName = null): void
    {
        if (is_null($column) || is_null($where)) return;
        if (is_null($tableName) && is_null($this->permanentTable)) return;
        if (is_null($tableName) && !is_null($this->permanentTable)) $tableName = $this->permanentTable;

        foreach ($assoc as $key => $val) {
            $this->set($column, $where, array($key => $val), $tableName);
        }
    }

    /**
     * Delete an entry
     *
     * @param string $where
     * @param null $tableName
     */
    public function unset(string $where, $tableName = null): void
    {
        if (is_null($tableName) && is_null($this->permanentTable)) return;
        if (is_null($tableName) && !is_null($this->permanentTable)) $tableName = $this->permanentTable;

        $this->client->delete($tableName, $where);
    }
}