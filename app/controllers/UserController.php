<?php
require_once __DIR__ . '/../dao/UserDAO.php';

class UserController {
  
  private function view($name, $data = [])
  {
    extract($data, EXTR_SKIP);

    require __DIR__ . '/../../public/views/' . $name . '.php';
  }

  public function user($id) {
    $user = (new UserDAO())->findById($id);

    $this->view('user/profile', ['user' => $user]);
  }

  public function listAll() {
    (new UserDAO())->getAll();
  }

  public function userUpdate($userId) {
    $email = trim($_POST['email']) ?? '';
    $username = trim($_POST['username']) ?? '';
    $isAdmin = isset($_POST['is_admin']) ? 1 : 0;

    // Se não houver email ou password, mostrar erro
    if (empty($email) || empty($username)) {
      throw Exception("Email e nome de utilizador são obrigatórios");
    }

    $linhasAlteradas = (new UserDAO())->userUpdateDAO($userId, $username, $email, $isAdmin);

    if(! $linhasAlteradas) {
      throw new Exception("Erro ao atualizar os dados");
    }

    return;
  }

  public function userDelete($userId) {
    $linhasAlteradas = (new UserDAO())->userDeleteDAO($userId);

    if (! $linhasAlteradas) {
      throw new Exception("Erro ao eleiminar user");
    }

    return;
  }
}