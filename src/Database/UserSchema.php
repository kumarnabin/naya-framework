<?php

namespace Konnect\NayaFramework\Database;

use Konnect\NayaFramework\Models\User;

class UserSchema
{
    private array $defaultData = [
        [
            'id' => 1,
            'email' => 'john.doe@example.com',
            'password' => 'password123',
            'username' => 'johnDoe92',
            'name' => 'John Doe',
            'dob' => '1996-03-15',
            'website' => 'https://johndoe.com',
        ],
        [
            'id' => 2,
            'email' => 'nabin.singh@example.com',
            'password' => 'mypassword987',
            'username' => 'nabinSingh91',
            'name' => 'Nabin Singh',
            'dob' => '1992-07-22',
            'website' => 'https://nabinsingh.com',
        ],
        [
            'id' => 3,
            'email' => 'alice.williams@domain.com',
            'password' => 'alice12345',
            'username' => 'aliceW',
            'name' => 'Alice Williams',
            'dob' => '1998-05-11',
            'website' => 'https://alicewilliams.com',
        ],
        [
            'id' => 4,
            'email' => 'rajesh.kumar@example.com',
            'password' => 'rajesh1234',
            'username' => 'rajeshKumar',
            'name' => 'Rajesh Kumar',
            'dob' => '1989-10-30',
            'website' => 'https://rajeshkumar.com',
        ],
        [
            'id' => 5,
            'email' => 'sara_thompson99@example.com',
            'password' => 'sara4567',
            'username' => 'saraT99',
            'name' => 'Sara Thompson',
            'dob' => '1997-12-05',
            'website' => 'https://sarathompson.com',
        ],
        [
            'id' => 6,
            'email' => 'james.anderson@company.org',
            'password' => 'james2024',
            'username' => 'jamesAnderson',
            'name' => 'James Anderson',
            'dob' => '1984-04-10',
            'website' => 'https://jamesanderson.com',
        ],
        [
            'id' => 7,
            'email' => 'pooja.sharma@domain.net',
            'password' => 'pooja2023',
            'username' => 'poojaSharma',
            'name' => 'Pooja Sharma',
            'dob' => '1994-08-15',
            'website' => 'https://poojasharma.com',
        ],
        [
            'id' => 8,
            'email' => 'ravi.patel123@outlook.com',
            'password' => 'ravi12345',
            'username' => 'raviP123',
            'name' => 'Ravi Patel',
            'dob' => '1995-11-20',
            'website' => 'https://ravipatel.com',
        ],
        [
            'id' => 9,
            'email' => 'maria.garcia@example.com',
            'password' => 'maria2022',
            'username' => 'mariaG22',
            'name' => 'Maria Garcia',
            'dob' => '1998-02-10',
            'website' => 'https://mariagarcia.com',
        ],
        [
            'id' => 10,
            'email' => 'david_lee@sample.com',
            'password' => 'david12345',
            'username' => 'davidLee90',
            'name' => 'David Lee',
            'dob' => '1990-09-25',
            'website' => 'https://davidlee.com',
        ],
    ];

    private User $modal;
    private Connection $connection;
    private User $model;

    public function __construct(?Connection $connection = null)
    {
        $this->connection = $connection ?? new Connection();
        $this->model = new User();
    }

    public function seedData(): void
    {
        foreach ($this->defaultData as $data) {
            // Hash the password before saving
            $data['password'] = password_hash($data['password'], PASSWORD_DEFAULT);
            $this->model->create($data);
        }
    }

    public function schema(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->model->getTableName()} (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL UNIQUE,
            name VARCHAR(255) NOT NULL,
            dob DATE NOT NULL,
            website VARCHAR(255) NOT NULL
        )";

        $this->connection->getConnection()->exec($sql);
    }

    public function dropSchema(): void
    {
        $sql = "DROP TABLE IF EXISTS {$this->model->getTableName()}";
        $this->connection->getConnection()->exec($sql);
    }

    public function truncate(): void
    {
        $sql = "DELETE FROM {$this->model->getTableName()}";
        $this->connection->getConnection()->exec($sql);
    }

    public function reset(): void
    {
        $this->dropSchema();
        $this->schema();
        $this->seedData();
    }

    public function refresh(): void
    {
        $this->truncate();
        $this->seedData();
    }

    public function migrate(): void
    {
        $this->schema();
    }

    public function rollback(): void
    {
        $this->dropSchema();
    }
}