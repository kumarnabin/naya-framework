<?php

namespace Konnect\NayaFramework\Models;

use Konnect\NayaFramework\Database\Connection;
use PDO;

class User extends Model
{
    protected string $table = 'users'; // Specify the connection table

    // Seed the connection with default data


    // Example: Get users by age range
    public function getUsersByAgeRange(int $minAge, int $maxAge): array
    {
        $stmt = $this->con->prepare("SELECT * FROM {$this->table} WHERE age BETWEEN :minAge AND :maxAge");
        $stmt->execute([
            'minAge' => $minAge,
            'maxAge' => $maxAge,
        ]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Example: Authenticate user by email and password
    public function authenticate(string $email, string $password): ?array
    {
        $stmt = $this->con->prepare("SELECT * FROM {$this->table} WHERE email = :email AND password = :password");
        $stmt->execute([
            'email' => $email,
            'password' => $password,
        ]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
        return $user ?: null; // Return null if authentication fails
    }

}
