<?php

namespace Konnect\NayaFramework\Models;

use Konnect\NayaFramework\Database;
use PDO;
use PDOException;

abstract class Model
{
    protected $db;
    protected $table;

    public function __construct(Database $database) {
        $this->db = $database->getConnection();
    }

    public function all(): array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table}");
            $stmt->execute();
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function find(int $id): ?array
    {
        try {
            $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = :id");
            $stmt->execute(['id' => $id]);
            $result = $stmt->fetch(PDO::FETCH_ASSOC);
            return $result ?: null;
        } catch (PDOException $e) {
            return null; // Handle gracefully
        }
    }

    public function create(array $data): array
    {
        try {
            $columns = implode(', ', array_keys($data));
            $placeholders = implode(', ', array_map(fn($col) => ":$col", array_keys($data)));
            $query = "INSERT INTO {$this->table} ($columns) VALUES ($placeholders)";
            $stmt = $this->db->prepare($query);

            if ($stmt->execute($data)) {
                return ['status' => 'success', 'id' => $this->db->lastInsertId()];
            }
            return ['status' => 'error', 'message' => 'Failed to create record.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function update(int $id, array $data): array
    {
        try {
            $setClause = implode(', ', array_map(fn($col) => "$col = :$col", array_keys($data)));
            $query = "UPDATE {$this->table} SET $setClause WHERE id = :id";
            $stmt = $this->db->prepare($query);
            $data['id'] = $id;

            if ($stmt->execute($data)) {
                return ['status' => 'success', 'message' => 'Record updated successfully.'];
            }
            return ['status' => 'error', 'message' => 'Failed to update record.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function delete(int $id): array
    {
        try {
            $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = :id");
            if ($stmt->execute(['id' => $id])) {
                return ['status' => 'success', 'message' => 'Record deleted successfully.'];
            }
            return ['status' => 'error', 'message' => 'Failed to delete record.'];
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => $e->getMessage()];
        }
    }

    public function query(string $sql, array $params = []): array
    {
        try {
            $stmt = $this->db->prepare($sql);
            $stmt->execute($params);
            return $stmt->fetchAll(PDO::FETCH_ASSOC);
        } catch (PDOException $e) {
            return ['status' => 'error', 'message' => 'Query execution error: ' . $e->getMessage()];
        }
    }
}
