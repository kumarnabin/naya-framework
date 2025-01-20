<?php

namespace Konnect\NayaFramework\Models;

use Konnect\NayaFramework\Database\Connection;
use PDO;

abstract class Model
{
    protected PDO $con;
    protected string $table;
    protected array $fillable = [];
    protected array $relations = [];

    public function __construct()
    {
        $connection = new Connection();
        $this->con = $connection->getConnection();
    }

    public function getTableName(): string
    {
        return $this->table;
    }

    public function all(): array
    {
        $stmt = $this->con->query("SELECT * FROM {$this->table}");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function find(int $id): ?array
    {
        $stmt = $this->con->prepare("SELECT * FROM {$this->table} WHERE id = :id");
        $stmt->execute(['id' => $id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result ?: null;
    }

    public function create(array $data): array
    {
        $columns = implode(', ', array_keys($data));
        $placeholders = implode(', ', array_map(fn($key) => ":$key", array_keys($data)));
        $stmt = $this->con->prepare("INSERT INTO {$this->table} ($columns) VALUES ($placeholders)");

        if ($stmt->execute($data)) {
            return ['status' => 'success', 'id' => $this->con->lastInsertId()];
        }

        return ['status' => 'error', 'message' => 'Failed to create record.'];
    }

    public function update(int $id, array $data): array
    {
        $setClause = implode(', ', array_map(fn($key) => "$key = :$key", array_keys($data)));
        $data['id'] = $id;
        $stmt = $this->con->prepare("UPDATE {$this->table} SET $setClause WHERE id = :id");

        if ($stmt->execute($data)) {
            return ['status' => 'success', 'message' => 'Record updated successfully.'];
        }

        return ['status' => 'error', 'message' => 'Failed to update record.'];
    }

    public function delete(int $id): array
    {
        $stmt = $this->con->prepare("DELETE FROM {$this->table} WHERE id = :id");
        if ($stmt->execute(['id' => $id])) {
            return ['status' => 'success', 'message' => 'Record deleted successfully.'];
        }
        return ['status' => 'error', 'message' => 'Failed to delete record.'];
    }

    public function hasMany(string $relatedModel, string $foreignKey, string $localKey = 'id'): array
    {
        $related = new $relatedModel(new Connection());
        $stmt = $this->con->prepare("SELECT * FROM {$related->table} WHERE $foreignKey = :localKey");
        $stmt->execute(['localKey' => $this->{$localKey}]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function belongsTo(string $relatedModel, string $foreignKey, string $ownerKey = 'id'): ?array
    {
        $related = new $relatedModel(new Connection());
        $stmt = $this->con->prepare("SELECT * FROM {$related->table} WHERE $ownerKey = :foreignKey");
        $stmt->execute(['foreignKey' => $this->{$foreignKey}]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function hasOne(string $relatedModel, string $foreignKey, string $localKey = 'id'): ?array
    {
        $related = new $relatedModel(new Connection());
        $stmt = $this->con->prepare("SELECT * FROM {$related->table} WHERE $foreignKey = :localKey LIMIT 1");
        $stmt->execute(['localKey' => $this->{$localKey}]);
        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

    public function belongsToMany(string $relatedModel, string $pivotTable, string $foreignKey, string $relatedKey, string $localKey = 'id'): array
    {
        $related = new $relatedModel(new Connection());

        // Retrieve the related data through the pivot table
        $stmt = $this->con->prepare(
            "SELECT {$related->table}.* FROM {$related->table} 
        INNER JOIN {$pivotTable} ON {$pivotTable}.{$relatedKey} = {$related->table}.id 
        WHERE {$pivotTable}.{$foreignKey} = :localKey"
        );
        $stmt->execute(['localKey' => $this->{$localKey}]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function hasManyThrough(string $relatedModel, string $throughModel, string $foreignKey, string $localKey = 'id'): array
    {
        $related = new $relatedModel(new Connection());
        $through = new $throughModel(new Connection());

        // Retrieve data through the intermediary model (e.g., Users)
        $stmt = $this->con->prepare(
            "SELECT {$related->table}.* FROM {$related->table} 
        INNER JOIN {$through->table} ON {$through->table}.id = {$related->table}.user_id 
        WHERE {$through->table}.country_id = :localKey"
        );
        $stmt->execute(['localKey' => $this->{$localKey}]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function morphMany(string $relatedModel, string $morphType, string $morphId, string $localKey = 'id'): array
    {
        $related = new $relatedModel(new Connection());
        $stmt = $this->con->prepare(
            "SELECT * FROM {$related->table} WHERE {$morphType}_type = :type AND {$morphType}_id = :localKey"
        );
        $stmt->execute(['type' => get_class($this), 'localKey' => $this->{$localKey}]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function morphTo(string $relatedModel, string $morphType, string $morphId): ?array
    {
        $related = new $relatedModel(new Connection());
        $stmt = $this->con->prepare(
            "SELECT * FROM {$related->table} WHERE {$morphType}_id = :morphId AND {$morphType}_type = :type"
        );
        $stmt->execute(['morphId' => $this->{$morphId}, 'type' => $relatedModel]);

        return $stmt->fetch(PDO::FETCH_ASSOC) ?: null;
    }

}
