<?php

namespace Konnect\NayaFramework\Models;

abstract class Model
{

    protected array $data = [];

    public function __construct(array $data = [])
    {
        $this->data = $data;
    }


    // Get all users
    public function getAll(): array
    {
        return $this->data;
    }

    // Find a user by ID
    public function find(int $id): ?array
    {
        foreach ($this->data as $item) {
            if ($item['id'] === $id) {
                return $item;
            }
        }
        return null; // Return null if the user is not found
    }

    // Create a new user
    public function create(array $data): array
    {
        $nextId = end($this->data)['id'] + 1; // Auto-increment ID
        $data['id'] = $nextId;
        $this->data[] = $data;
        return $this->data;
    }

    // Update a user by ID
    public function update(int $id, array $data): bool
    {
        foreach ($this->data as &$item) {
            if ($item['id'] === $id) {
                $item = array_merge($item, $data); // Update user fields
                return true;
            }
        }
        return false; // Return false if the user is not found
    }

    // Delete a user by ID
    public function delete(int $id): bool
    {
        foreach ($this->data as $index => $item) {
            if ($item['id'] === $id) {
                unset($this->data[$index]); // Remove user
                $this->data = array_values($this->data); // Reindex array
                return true;
            }
        }
        return false; // Return false if the user is not found
    }

    // Filter users by a specific field
    public function filter(string $field, $value): array
    {
        return array_filter($this->data, fn($item) => $item[$field] === $value);
    }

    public function all(array $withRelations): array
    {
        return $this->data;
    }

}