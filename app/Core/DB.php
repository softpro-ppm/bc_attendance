<?php

namespace App\Core;

use PDO;
use PDOException;

class DB
{
    private static $instance = null;
    private $connection;
    private $config;

    private function __construct($config = null)
    {
        if ($config === null) {
            $this->config = require dirname(__DIR__, 2) . '/config/config.php';
        } else {
            $this->config = $config;
        }
        
        // Debug: check what we got
        if (!$this->config || !is_array($this->config)) {
            throw new \Exception('Config not loaded properly. Type: ' . gettype($this->config));
        }
        
        $this->connect();
    }

    public static function getInstance($config = null)
    {
        if (self::$instance === null) {
            self::$instance = new self($config);
        }
        return self::$instance;
    }

    private function connect()
    {
        try {
            // Validate config
            if (!$this->config || !is_array($this->config) || !isset($this->config['database'])) {
                throw new \Exception('Invalid configuration: database config not found');
            }
            
            // Only support SQLite for now
            if ($this->config['database']['driver'] !== 'sqlite') {
                throw new \Exception('Only SQLite driver is supported');
            }
            
            // SQLite connection
            $dbPath = $this->config['database']['database'];
            $dbDir = dirname($dbPath);
            
            // Create database directory if it doesn't exist
            if (!is_dir($dbDir)) {
                mkdir($dbDir, 0755, true);
            }
            
            $this->connection = new PDO(
                'sqlite:' . $dbPath,
                null,
                null,
                $this->config['database']['options']
            );
            
            // Enable foreign keys for SQLite
            $this->connection->exec('PRAGMA foreign_keys = ON');
            
        } catch (PDOException $e) {
            throw new \Exception('Database connection failed: ' . $e->getMessage());
        }
    }

    public function getConnection()
    {
        return $this->connection;
    }

    public function query($sql, $params = [])
    {
        try {
            $stmt = $this->connection->prepare($sql);
            $stmt->execute($params);
            return $stmt;
        } catch (PDOException $e) {
            throw new \Exception('Query execution failed: ' . $e->getMessage());
        }
    }

    public function fetch($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetch();
    }

    public function fetchAll($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchAll();
    }

    public function fetchColumn($sql, $params = [], $column = 0)
    {
        $stmt = $this->query($sql, $params);
        return $stmt->fetchColumn($column);
    }

    public function execute($sql, $params = [])
    {
        $stmt = $this->query($sql, $params);
        return $stmt->rowCount();
    }

    public function lastInsertId()
    {
        return $this->connection->lastInsertId();
    }

    public function beginTransaction()
    {
        return $this->connection->beginTransaction();
    }

    public function commit()
    {
        return $this->connection->commit();
    }

    public function rollback()
    {
        return $this->connection->rollback();
    }

    public function inTransaction()
    {
        return $this->connection->inTransaction();
    }

    public function quote($value)
    {
        return $this->connection->quote($value);
    }

    public function close()
    {
        $this->connection = null;
    }
}
