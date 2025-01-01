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
            'age' => 28,
            'dob' => '1996-03-15',
            'confirm_password' => 'password123',
            'website' => 'https://johndoe.com',
        ],
        [
            'id' => 2,
            'email' => 'nabin.singh@example.com',
            'password' => 'mypassword987',
            'username' => 'nabinSingh91',
            'age' => 32,
            'dob' => '1992-07-22',
            'confirm_password' => 'mypassword987',
            'website' => 'https://nabinsingh.com',
        ],
        [
            'id' => 3,
            'email' => 'alice.williams@domain.com',
            'password' => 'alice12345',
            'username' => 'aliceW',
            'age' => 26,
            'dob' => '1998-05-11',
            'confirm_password' => 'alice12345',
            'website' => 'https://alicewilliams.com',
        ],
        [
            'id' => 4,
            'email' => 'rajesh.kumar@example.com',
            'password' => 'rajesh1234',
            'username' => 'rajeshKumar',
            'age' => 35,
            'dob' => '1989-10-30',
            'confirm_password' => 'rajesh1234',
            'website' => 'https://rajeshkumar.com',
        ],
        [
            'id' => 5,
            'email' => 'sara_thompson99@example.com',
            'password' => 'sara4567',
            'username' => 'saraT99',
            'age' => 27,
            'dob' => '1997-12-05',
            'confirm_password' => 'sara4567',
            'website' => 'https://sarathompson.com',
        ],
        [
            'id' => 6,
            'email' => 'james.anderson@company.org',
            'password' => 'james2024',
            'username' => 'jamesAnderson',
            'age' => 40,
            'dob' => '1984-04-10',
            'confirm_password' => 'james2024',
            'website' => 'https://jamesanderson.com',
        ],
        [
            'id' => 7,
            'email' => 'pooja.sharma@domain.net',
            'password' => 'pooja2023',
            'username' => 'poojaSharma',
            'age' => 30,
            'dob' => '1994-08-15',
            'confirm_password' => 'pooja2023',
            'website' => 'https://poojasharma.com',
        ],
        [
            'id' => 8,
            'email' => 'ravi.patel123@outlook.com',
            'password' => 'ravi12345',
            'username' => 'raviP123',
            'age' => 29,
            'dob' => '1995-11-20',
            'confirm_password' => 'ravi12345',
            'website' => 'https://ravipatel.com',
        ],
        [
            'id' => 9,
            'email' => 'maria.garcia@example.com',
            'password' => 'maria2022',
            'username' => 'mariaG22',
            'age' => 26,
            'dob' => '1998-02-10',
            'confirm_password' => 'maria2022',
            'website' => 'https://mariagarcia.com',
        ],
        [
            'id' => 10,
            'email' => 'david_lee@sample.com',
            'password' => 'david12345',
            'username' => 'davidLee90',
            'age' => 34,
            'dob' => '1990-09-25',
            'confirm_password' => 'david12345',
            'website' => 'https://davidlee.com',
        ]

    ];
    private mixed $connection;
    private User $modal;

    public function __construct($connection = null)
    {
        $this->connection = $connection ?? new Connection();
        $this->modal = new User();
    }

    public function seedData(): void
    {
        foreach ($this->defaultData as $data) {
            $this->modal->create($data);
        }
    }

    public function schema(): void
    {
        $sql = "CREATE TABLE IF NOT EXISTS {$this->modal->getTableName()} (
            id INTEGER PRIMARY KEY AUTOINCREMENT,
            email VARCHAR(255) NOT NULL,
            password VARCHAR(255) NOT NULL,
            username VARCHAR(255) NOT NULL,
            age INTEGER NOT NULL,
            dob DATE NOT NULL,
            confirm_password VARCHAR(255) NOT NULL,
            website VARCHAR(255) NOT NULL
        )";
        $this->connection->exec($sql);
    }
}