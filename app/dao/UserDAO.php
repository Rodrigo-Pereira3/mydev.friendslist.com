<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/DataBase.php';

class UserDAO {
  private $conn;

  public function __construct() {
    // Conectar à base de dados
    $this->conn = (new DataBase())->connect();
  }

  public function findById($userId) {
    $sql = "
      SELECT * 
      FROM users 
      WHERE id = :id
      AND is_verified = 1 
      AND verified_at IS NOT NULL 
      LIMIT 1
    ";

    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(':id', $userId);

    $stmt->execute();
    // resultado da base de dados
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //var_dump($row);
    //var_dump($row);

    if ($row) {
      $user = new User(
        $row['id'],
        $row['username'],
        $row['email'],
        $row['password'],
        $row['is_admin'],

      );


      //var_dump($user);
      return $user;
    } else {
      return FALSE;
    }
  }

  public function updateUser($userId, $username, $email) {
    //var_dump($userId);
  
    $sql = "
      UPDATE users
      SET username = ?, email = ?
      WHERE id = ?
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([
      $username,
      $email,
      $userId
    ]);

    $result = $stmt->rowCount();
    var_dump($result);
    return $result;
  }

  public function findByEmail($email) {
    // Implementação para encontrar usuário pelo email
    $sql = "
      SELECT * 
      FROM users 
      WHERE email = :email 
      AND is_verified = 1 
      AND verified_at IS NOT NULL 
      LIMIT 1
    ";
    // Preparar e executar a query usando PDO
    $stmt = $this->conn->prepare($sql);

    $stmt->bindParam(':email', $email);

    $stmt->execute();
    // resultado da base de dados
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    //var_dump($row);
    //var_dump($row);

    if($row) {
      $user = new User(
        $row['id'],
        $row['username'],
        $row['email'],
        $row['password'],
        $row['is_admin'],

      );


      //var_dump($user);
      return $user;
    } else {
      return FALSE;
    }

  }

  public function createPending($username, $email) {
    $sql = "
    INSERT INTO users (username, email, password, is_admin, is_verified, verified_at, created_at, updated_at, deleted_at)
    VALUES (?, ?, '', 0, 0, NULL, NOW(), NOW(), NULL)
  ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$username, $email]);

    return (int)$this->conn->lastInsertId();
  }

  public function setPasswordAndVerify(int $userId, string $passwordHash): void
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

  public function getUsersDAO() {
    $sql = "
      SELECT * FROM users;
    ";

    $stmt = $this->conn->prepare($sql);

    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    $users = [];
    //var_dump($rows);
    foreach($rows as $row) {
      
      // Criar um objeto do tipo user
      $user = new User(
        $row['id'],
        $row['username'],
        $row['email'],
        $row['password'],
        $row['is_admin'],
      );

      // Adicionar o user criado ao array
      $users[] = $user;
    }
    
    return $users;
  }

  public function getNumUsersDAO() {
    $sql = "
      SELECT COUNT(*) FROM users;
    ";

    $stmt = $this->conn->prepare($sql);

    $stmt->execute();

    $numUsers = $stmt->fetch(PDO::FETCH_ASSOC);

    var_dump($numUsers);

    return $numUsers;
  }

  public function arrayUsersDAO()
  {
    $sql = "
      SELECT * FROM users;
    ";

    $stmt = $this->conn->prepare($sql);

    $stmt->execute();

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
  }

}