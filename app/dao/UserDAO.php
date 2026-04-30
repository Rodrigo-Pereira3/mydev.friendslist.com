<?php

require_once __DIR__ . '/../config/DataBase.php';
require_once __DIR__ . '/../models/User.php';

class UserDAO
{
    private $conn;

    public function __construct()
    {
        // Conectar á base de dados
        $this->conn = (new DataBase())->connect();
    }

    public function findByEmail($email)
    {
        // Lógica para encontrar um utilizador pelo email
        $sql = "SELECT * FROM users WHERE email = :email LIMIT 1";
        // Preparar e executar a query usando PDO
        $stmt = $this->conn->prepare($sql);

        $stmt->bindParam(':email', $email);

        $stmt->execute();

        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        var_dump($row);

        if ($row) {
            $user = new User(
                $row['id'],
                $row['username'],
                $row['email'],
                $row['created_at'],
                $row['updated_at'],
                $row['deleted_at']
            );
            var_dump($user);
            return $row; // Retorna os dados do utilizador
        } else {
            return null; // Utilizador não encontrado
        }
    }

    public function createPending($username, $email)
    {
        $sql = "
    INSERT INTO users (username, email, password, is_admin, is_verified, verified_at, created_at, updated_at, deleted_at)
    VALUES (?, ?, '', 0, 0, NULL, NOW(), NOW(), NULL)
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$username, $email]);

        return (int) $this->conn->lastInsertId();
    }

    public function setPasswordAndVerify($userId, $passwordHash)
    {
        $sql = "
        UPDATE users
        SET password = ?,
        is_verified = 1,
        verified_at = NOW(),
        updated_at = NOW()
        WHERE id = ?
    ";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute([$passwordHash, $userId]);
    }
}