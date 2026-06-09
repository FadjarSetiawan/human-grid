<?php
/**
 * Base Controller Class
 * HumanGrid - Anti-AI Social Media Platform
 */

class Controller {
    /**
     * Render a view with layout
     */
    protected function render($view, $data = []) {
        extract($data);
        
        ob_start();
        require __DIR__ . '/../app/Views/' . $view . '.php';
        $content = ob_get_clean();
        
        require __DIR__ . '/../app/Views/layouts/main.php';
    }

    /**
     * Render without layout (for AJAX)
     */
    protected function renderPartial($view, $data = []) {
        extract($data);
        require __DIR__ . '/../app/Views/' . $view . '.php';
    }

    /**
     * Redirect to a URL
     */
    protected function redirect($url) {
        header("Location: $url");
        exit;
    }

    /**
     * Return JSON response
     */
    protected function json($data, $status = 200) {
        http_response_code($status);
        header('Content-Type: application/json');
        echo json_encode($data);
        exit;
    }

    /**
     * Check if user is logged in
     */
    protected function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Require login
     */
    protected function requireAuth() {
        if (!$this->isLoggedIn()) {
            $this->redirect(BASE_URL . '/login');
        }
    }

    /**
     * Generate CSRF token
     */
    protected function generateCsrfToken() {
        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }
        return $_SESSION['csrf_token'];
    }

    /**
     * Verify CSRF token
     */
    protected function verifyCsrfToken($token) {
        return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
    }

    /**
     * Sanitize output
     */
    protected function e($string) {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
}
