<?php

require_once __DIR__ . '/../models/User.php';
require_once __DIR__ . '/../config/DataBase.php';

class EmailVerificationDAO {
  private $conn;

  public function __construct()
  {
    // Conectar à base de dados
    $this->conn = (new DataBase())->connect();
  }

  public function createForUser($userId, $ttlSeconds = 300) {
    $token = bin2hex(random_bytes(32) . round(microtime(true) * 1000));

    var_dump("Token gerado: " . $token);

    $tokenHash = hash('sha256', $token);
  
  
    $sql = "
      INSERT INTO email_verifications (user_id, token_hash, expires_at, used_at, created_at)
      VALUES (?, ?, DATE_ADD(NOW(), INTERVAL ? SECOND), NULL, NOW())
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$userId, $tokenHash, $ttlSeconds]);

    return $token;
  }

  public function validateToken($token) {
    $tokenHash = hash('sha256', $token);

    $sql = "
      SELECT user_id
      FROM email_verifications
      WHERE token_hash = ?
        AND used_at IS NULL
        AND expires_at > NOW()
      ORDER BY id DESC
      LIMIT 1
    ";

    $stmt = $this->conn->prepare($sql);
    $stmt->execute([$tokenHash]);

    $userId = $stmt->fetchColumn();
    //$row = $stmt->fetch(PDO::FETCH_ASSOC);

    //$userId = $row['user_id'] ?? null;

    return $userId ? (int)$userId : false;
  }

  public function markUsed(string $token): void
  {
    $tokenHash = hash('sha256', $token);

    $stmt = $this->conn->prepare("
    UPDATE 
      email_verifications 
      SET used_at = NOW() 
      WHERE token_hash = ?"
    );
    
    $stmt->execute([$tokenHash]);
  }

  public function getEmailVerificationsByUserId($userId) {
    $sql = "SELECT * FROM email_verifications WHERE user_id = ?";

    $stmt = $this->conn->prepare($sql);

    $stmt->execute([$userId]);

    $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $rows;
  }

  public function getNumEmailVerificationsByUserId() {
    $sql = "
      SELECT COUNT(*) FROM email_verifications WHERE user_id = ?;
    ";

    $stmt = $this->conn->prepare($sql);

    $stmt->execute();

    $numEmails = $stmt->fetch(PDO::FETCH_ASSOC);

    var_dump($numEmails);

    return $numEmails;
  }
}