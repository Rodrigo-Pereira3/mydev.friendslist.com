<?php

require __DIR__ . '/../dao/UserDAO.php';
require __DIR__ . '/../dao/EmailVerificationDAO.php';

class AuthController
{

    private function view($name, $data = [])
    {
        extract($data, EXTR_SKIP);
        require __DIR__ . '/../../public/views/' . $name . '.php';
    }

    public function validateLogin()
    {
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');

        var_dump($email);
        var_dump($password);

        $passwordEncript = password_hash($password, PASSWORD_DEFAULT);

        var_dump($passwordEncript);
        // Se não houver email ou password, mostrar erro
        if (empty($email) || empty($password)) {
            die("Email e password são obrigatórios");
        }

        $user = (new UserDAO())->findByEmail($email);

        if (!$user) {

            // Password incorreta
            die("Password ou email incorretos");
        }
        var_dump("User encontrado: " . $user['email']);

    }

    public function validateSignup()
    {
        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = trim($_POST['password'] ?? '');



        if ($username == '' || $email == '') {
            throw new Exception("Username e email são obrigatórios");
        }

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Email inválido");
        }
        // Verificar se o email já existe
        $user = (new UserDAO())->findByEmail($email);
        if ($user) {
            throw new Exception("Já existe um usuário com esse email");
        }


        // Criar um utilizador em estado pendente
        $userDAO = new UserDAO();
        $userId = $userDAO->createPending($username, $email);

        //Enviar email de verificação
        $verDAO = new EmailVerificationDAO();

        $token = $verDAO->createForUser($userId, 300);

        // 3) baseUrl dinâmico (vhosts)
        $scheme = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') ? 'https' : 'http';
        $host = $_SERVER['HTTP_HOST'] ?? 'localhost';
        $baseUrl = $scheme . '://' . $host;

        // 4) link para clicar no email
        $link = $baseUrl . "/verify-email?token=" . urlencode($token);

        // 5) envia email via Mailer (PHPMailer/Mailtrap)
        $subject = "Verifica o teu email (expira em 5 min)";
        $html = "
        <div style='font-family: Arial, sans-serif;'>
        <h2>Olá, " . htmlspecialchars($username) . "!</h2>
        <p>Para ativares a tua conta e definires a tua password, clica no link abaixo (válido por <b>5 minutos</b>):</p>
        <p><a href='{$link}'>{$link}</a></p>
        <p>Se o link expirar, faz signup novamente (ou pede reenvio do link).</p>
        </div>
        ";

        (new Mailer())->send($email, $subject, $html);

        // 6) redirect com toast
        $_SESSION['flash_success'] = "Conta criada. Enviámos um email para verificares (link expira em 5 min).";
        header("Location: /login");
        exit;
    }

    public function verifyEmailForm($token)
    {
        $token = $_GET['token'] ?? '';
        if (empty($token)) {
            header("Location: /bad-request");

            exit();
        }

        // Token válido

        $this->view('verify-email', ['token' => $token]);
    }

    public function verifyEmailSubmit()
    {
        $token = $_POST['token'] ?? '';
        $password = $_POST['password'] ?? '';

        if (empty($token) || empty($password)) {
            throw new Exception("Token e password são obrigatórios");
        }

        $verDAO = new EmailVerificationDAO();

        $userId = $verDAO->validateToken($token);

        var_dump($userId);

        if (!$userId) {
            throw new Exception("Token inválido ou expirado");
        }

        $hash = password_hash($password, PASSWORD_DEFAULT);

        $userDAO = new UserDAO();
        // Atualizar a password do utilizador e marcar como verificado
        $userDAO->setPasswordAndVerify($userId, $hash);
        // Desativar o token para não ser usado novamente
        $verDAO->markUsed($token);


        $_SESSION['flash_success'] = "Email verificado e password definida. Já podes fazer login.";
        header("Location: /login");
        exit;
    }
}