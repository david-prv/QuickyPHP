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
 * Class SQLClient
 */
class SQLClient
{
    /**
     * Connection info
     */
    private string $host;
    private string $user;
    private string $password;
    private string $dbname;
    private $conn;

    /**
     * SQLClient constructor.
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @throws MySQLConnException
     */
    public function __construct(string $host, string $user, string $password, string $dbname)
    {
        $this->host = $host;
        $this->user = $user;
        $this->password = $password;
        $this->dbname = $dbname;

        $this->conn = mysqli_connect($this->host, $this->user, $this->password, $this->dbname);
        if (mysqli_connect_error()) {
            throw new MySQLConnException();
        }
    }

    /**
     * Select Operation
     *
     * @param string $table
     * @param string $columns
     * @param string $where
     * @return array|bool
     */
    public function select(string $table, string $columns = '*', string $where = ''): ?array
    {
        $query = "SELECT $columns FROM $table";
        if ($where != '') $query .= " WHERE $where";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_execute($stmt);
        mysqli_stmt_store_result($stmt);
        if (mysqli_stmt_num_rows($stmt) > 0) {
            mysqli_stmt_bind_result($stmt, ...$result);
            $results = [];
            while (mysqli_stmt_fetch($stmt)) {
                $results[] = $result;
            }
            return $results;
        } else {
            return null;
        }
    }

    /**
     * Insert Operation
     *
     * @param string $table
     * @param array $data
     * @return bool
     */
    public function insert(string $table, array $data): bool
    {
        $columns = implode(',', array_keys($data));
        $placeholders = implode(',', array_fill(0, count($data), '?'));
        $query = "INSERT INTO $table ($columns) VALUES ($placeholders)";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($data)), ...array_values($data));
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Update Operation
     *
     * @param $table
     * @param $data
     * @param $where
     * @return bool
     */
    public function update(string $table, array $data, string $where): bool
    {
        $set = '';
        $values = [];
        foreach ($data as $column => $value) {
            $set .= "$column=?,";
            $values[] = $value;
        }
        $set = rtrim($set, ',');
        $query = "UPDATE $table SET $set WHERE $where";
        $stmt = mysqli_prepare($this->conn, $query);
        mysqli_stmt_bind_param($stmt, str_repeat('s', count($values)), ...$values);
        return mysqli_stmt_execute($stmt);
    }

    /**
     * Delete Operation
     *
     * @param $table
     * @param $where
     * @return bool
     */
    public function delete($table, $where): bool
    {
        $query = "DELETE FROM $table WHERE $where";
        $stmt = mysqli_prepare($this->conn, $query);
        return mysqli_stmt_execute($stmt);
    }
}