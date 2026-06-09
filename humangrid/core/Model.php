<?php
/**
 * Base Model Class
 * HumanGrid - Anti-AI Social Media Platform
 */

class Model {
    protected $pdo;

    public function __construct() {
        $this->pdo = require __DIR__ . '/../config/database.php';
    }

    /**
     * Execute a prepared statement with parameters
     */
    protected function query($sql, $params = []) {
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt;
    }

    /**
     * Fetch all results
     */
    protected function fetchAll($sql, $params = []) {
        return $this->query($sql, $params)->fetchAll();
    }

    /**
     * Fetch single result
     */
    protected function fetchOne($sql, $params = []) {
        return $this->query($sql, $params)->fetch();
    }

    /**
     * Get last insert ID
     */
    protected function lastInsertId() {
        return $this->pdo->lastInsertId();
    }
}
