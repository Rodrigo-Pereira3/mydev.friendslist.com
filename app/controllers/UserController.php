<?php
require_once __DIR__ . '/../dao/UserDAO.php';
require_once __DIR__ . '/../dao/EmailVerificationDAO.php';

class UserController
{
  private function view($name, $data = [])
  {
    extract($data, EXTR_SKIP);

    require __DIR__ . '/../../public/views/' . $name . '.php';
  }

  public function profile($userId) {
    $user = (new UserDAO())->findById($userId);

    var_dump($user);
    
    $this->view("user/profile", ['user' => $user]);
  }

  public function update($userId) {
    //var_dump($_POST);
    // 1. Apanhar o username e email
    $username = trim($_POST['username']) ?? '';
    $email = trim($_POST['email']) ?? '';
    // Validar se o user realmente colocou dados
    if ($username === '' || $email === '') {
      throw new Exception("Username e email são obrigatórios");
    }
    // 2. 
    $result = (new UserDAO())->updateUser($userId, $username, $email);


    if(! $result) {
      throw new Exception("Erro ao atualizar dados.");
    }
    // 3. Atualizar os dados que estão na seesion
    // A session vai ser atualizada apenas
    // Se $userId == $_SESSION['token]['id']
    var_dump($_SESSION);
    if(AuthMiddlewareWeb::canEdit($userId)) {
      $_SESSION['token']['username'] = $username;
    }

    return;

  }

  public function getUsers() {
    $users = (new UserDAO())->getUsersDAO();
  }

  public function getAllDataToHome($userId) {
    try {
      $users = (new UserDAO())->arrayUsersDAO();
      $emailVerifications = (new EmailVerificationDAO())->getEmailVerificationsByUserId($userId);
      $numUtilizadores = (new UserDAO())->getNumUsersDAO();
      $numEmails = (new EmailVerificationDAO())->getNumEmailVerificationsByUserId($userId);

      $dataResponse = [
        'success' => true,
        'message' => "Operação realizada com sucesso.",
        'data'    => [
          'users' => $users,
          'emails_verification' => $emailVerifications,
          'num_utilizadores' => $numUtilizadores,
          'num_emails' => $numEmails
        ]
      ];

      Utils::jsonResponse($dataResponse, 200);

    } catch(Exception $e) {
      $dataResponse = [
        'success' => false,
        'message' => $e->getMessage(),
        'data'    => []
      ];

      Utils::jsonResponse($dataResponse, 401);

      exit;
    }
  }

}