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
 * Class SQLConnectionHelper
 */
class SQLConnectionHelper
{
    /**
     * Connection info
     */
    private string $host;
    private string $user;
    private string $password;
    private string $dbname;
    private object $conn;

    /**
     * SQLConnectionHelper constructor.
     *
     * @param string $host
     * @param string $user
     * @param string $password
     * @param string $dbname
     * @throws MySQLConnException
     */
    public function __construct(string $host, string $user, string $password, string $dbname) {
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
     * @param $table
     * @param string $columns
     * @param string $where
     * @return array|bool
     */
    public function select($table, $columns = '*', $where = '') {
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
            return false;
        }
    }

    /**
     * Insert Operation
     *
     * @param $table
     * @param $data
     * @return bool
     */
    public function insert($table, $data) {
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
    public function update($table, $data, $where) {
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
    public function delete($table, $where) {
        $query = "DELETE FROM $table WHERE $where";
        $stmt = mysqli_prepare($this->conn, $query);
        return mysqli_stmt_execute($stmt);
    }
}