<?php
/**
 * Authentication Controller
 * HumanGrid - Anti-AI Social Media Platform
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../app/Models/User.php';

class AuthController extends Controller {
    private $userModel;

    public function __construct() {
        $this->userModel = new User();
    }

    /**
     * Show login page
     */
    public function showLogin() {
        if ($this->isLoggedIn()) {
            $this->redirect(BASE_URL . '/');
        }
        $this->render('auth/login', [
            'csrf_token' => $this->generateCsrfToken(),
            'error' => $_SESSION['login_error'] ?? null
        ]);
        unset($_SESSION['login_error']);
    }

    /**
     * Process login
     */
    public function login() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/login');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['login_error'] = 'Token CSRF tidak valid';
            $this->redirect(BASE_URL . '/login');
        }

        $usernameOrEmail = trim($_POST['username'] ?? '');
        $password = $_POST['password'] ?? '';

        if (empty($usernameOrEmail) || empty($password)) {
            $_SESSION['login_error'] = 'Username/email dan password wajib diisi';
            $this->redirect(BASE_URL . '/login');
        }

        $user = $this->userModel->findByUsernameOrEmail($usernameOrEmail);

        if (!$user || !password_verify($password, $user['password_hash'])) {
            $_SESSION['login_error'] = 'Username/email atau password salah';
            $this->redirect(BASE_URL . '/login');
        }

        // Set session
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        $this->redirect(BASE_URL . '/');
    }

    /**
     * Show register page
     */
    public function showRegister() {
        if ($this->isLoggedIn()) {
            $this->redirect(BASE_URL . '/');
        }
        $this->render('auth/register', [
            'csrf_token' => $this->generateCsrfToken(),
            'errors' => $_SESSION['register_errors'] ?? [],
            'old' => $_SESSION['register_old'] ?? []
        ]);
        unset($_SESSION['register_errors'], $_SESSION['register_old']);
    }

    /**
     * Process registration
     */
    public function register() {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/register');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['register_errors'] = ['Token CSRF tidak valid'];
            $this->redirect(BASE_URL . '/register');
        }

        $username = trim($_POST['username'] ?? '');
        $email = trim($_POST['email'] ?? '');
        $password = $_POST['password'] ?? '';
        $passwordConfirm = $_POST['password_confirm'] ?? '';
        $fullName = trim($_POST['full_name'] ?? '');

        $errors = [];

        // Validation
        if (strlen($username) < 3 || strlen($username) > 30) {
            $errors[] = 'Username harus 3-30 karakter';
        }
        if (!preg_match('/^[a-zA-Z0-9_]+$/', $username)) {
            $errors[] = 'Username hanya boleh berisi huruf, angka, dan underscore';
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            $errors[] = 'Email tidak valid';
        }
        if (strlen($password) < 6) {
            $errors[] = 'Password minimal 6 karakter';
        }
        if ($password !== $passwordConfirm) {
            $errors[] = 'Password konfirmasi tidak cocok';
        }

        // Check existing user
        $existingUser = $this->userModel->findByUsernameOrEmail($username);
        if ($existingUser) {
            $errors[] = 'Username sudah digunakan';
        }
        $existingEmail = $this->userModel->findByUsernameOrEmail($email);
        if ($existingEmail) {
            $errors[] = 'Email sudah digunakan';
        }

        if (!empty($errors)) {
            $_SESSION['register_errors'] = $errors;
            $_SESSION['register_old'] = [
                'username' => $username,
                'email' => $email,
                'full_name' => $fullName
            ];
            $this->redirect(BASE_URL . '/register');
        }

        // Create user
        try {
            $userId = $this->userModel->create($username, $email, $password, $fullName);
            
            // Auto login
            $_SESSION['user_id'] = $userId;
            $_SESSION['username'] = $username;

            $this->redirect(BASE_URL . '/');
        } catch (Exception $e) {
            $_SESSION['register_errors'] = ['Terjadi kesalahan. Silakan coba lagi.'];
            $this->redirect(BASE_URL . '/register');
        }
    }

    /**
     * Logout
     */
    public function logout() {
        session_destroy();
        $this->redirect(BASE_URL . '/login');
    }
}
