<?php
/**
 * User Controller
 * HumanGrid - Anti-AI Social Media Platform
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Post.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

class UserController extends Controller {
    private $userModel;
    private $postModel;

    public function __construct() {
        $this->userModel = new User();
        $this->postModel = new Post();
    }

    /**
     * Show user profile
     */
    public function profile($username = null) {
        if ($username === null) {
            // Show current user's profile
            if (!$this->isLoggedIn()) {
                $this->redirect(BASE_URL . '/login');
            }
            $username = $_SESSION['username'];
        }

        $user = $this->userModel->findByUsernameOrEmail($username);
        if (!$user) {
            http_response_code(404);
            echo "Pengguna tidak ditemukan";
            exit;
        }

        $stats = $this->userModel->getUserWithStats($user['id']);
        $posts = $this->postModel->getByUser($user['id'], 0, 12);
        
        foreach ($posts as &$post) {
            $post['time_ago'] = timeAgo($post['created_at']);
        }

        $isFollowing = false;
        if ($this->isLoggedIn() && currentUserId() != $user['id']) {
            $isFollowing = $this->userModel->isFollowing(currentUserId(), $user['id']);
        }

        $this->render('profile', [
            'user' => $stats,
            'posts' => $posts,
            'isFollowing' => $isFollowing,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Edit profile page
     */
    public function editProfile() {
        $this->requireAuth();

        $user = $this->userModel->findById(currentUserId());

        $this->render('profile_edit', [
            'user' => $user,
            'csrf_token' => $this->generateCsrfToken(),
            'success' => $_SESSION['profile_success'] ?? null,
            'error' => $_SESSION['profile_error'] ?? null
        ]);
        unset($_SESSION['profile_success'], $_SESSION['profile_error']);
    }

    /**
     * Update profile
     */
    public function updateProfile() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/profile/edit');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['profile_error'] = 'Token CSRF tidak valid';
            $this->redirect(BASE_URL . '/profile/edit');
        }

        $fullName = trim($_POST['full_name'] ?? '');
        $bio = trim($_POST['bio'] ?? '');

        $this->userModel->updateProfile(currentUserId(), $fullName, $bio);

        $_SESSION['profile_success'] = 'Profil berhasil diperbarui';
        $this->redirect(BASE_URL . '/profile/edit');
    }

    /**
     * Upload avatar
     */
    public function uploadAvatar() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/profile/edit');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['profile_error'] = 'Token CSRF tidak valid';
            $this->redirect(BASE_URL . '/profile/edit');
        }

        $file = $_FILES['avatar'] ?? null;
        $validation = validateUpload($file);

        if (!$validation['valid'] || !str_starts_with($validation['mime_type'], 'image')) {
            $_SESSION['profile_error'] = 'File avatar tidak valid';
            $this->redirect(BASE_URL . '/profile/edit');
        }

        $filename = generateUploadFilename($validation['extension']);
        $destination = UPLOAD_DIR . $filename;

        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $_SESSION['profile_error'] = 'Gagal menyimpan avatar';
            $this->redirect(BASE_URL . '/profile/edit');
        }

        $this->userModel->updateAvatar(currentUserId(), $filename);
        $_SESSION['profile_success'] = 'Avatar berhasil diperbarui';
        $this->redirect(BASE_URL . '/profile/edit');
    }

    /**
     * Toggle follow (AJAX)
     */
    public function toggleFollow() {
        $this->requireAuth();

        $data = json_decode(file_get_contents('php://input'), true);
        $targetUserId = $data['user_id'] ?? 0;
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$this->verifyCsrfToken($csrfToken)) {
            $this->json(['error' => 'Token CSRF tidak valid'], 403);
        }

        if ($targetUserId == currentUserId()) {
            $this->json(['error' => 'Tidak bisa follow diri sendiri'], 400);
        }

        $isFollowing = $this->userModel->isFollowing(currentUserId(), $targetUserId);

        if ($isFollowing) {
            $this->userModel->unfollow(currentUserId(), $targetUserId);
        } else {
            $this->userModel->follow(currentUserId(), $targetUserId);
        }

        // Get updated follower count
        $stats = $this->userModel->getUserWithStats($targetUserId);

        $this->json([
            'following' => !$isFollowing,
            'follower_count' => $stats['follower_count']
        ]);
    }

    /**
     * Search users (AJAX)
     */
    public function search() {
        $query = trim($_GET['q'] ?? '');

        if (strlen($query) < 2) {
            $this->json([]);
        }

        $users = $this->userModel->search($query, 10);
        $this->json($users);
    }
}
