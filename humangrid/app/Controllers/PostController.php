<?php
/**
 * Post Controller
 * HumanGrid - Anti-AI Social Media Platform
 */

require_once __DIR__ . '/../core/Controller.php';
require_once __DIR__ . '/../app/Models/Post.php';
require_once __DIR__ . '/../app/Models/User.php';
require_once __DIR__ . '/../app/Models/Comment.php';
require_once __DIR__ . '/../app/Helpers/functions.php';

class PostController extends Controller {
    private $postModel;
    private $userModel;
    private $commentModel;

    public function __construct() {
        $this->postModel = new Post();
        $this->userModel = new User();
        $this->commentModel = new Comment();
    }

    /**
     * Show feed (home page)
     */
    public function feed() {
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $limit = 10;

        if ($this->isLoggedIn()) {
            $posts = $this->postModel->getFeed(currentUserId(), $offset, $limit);
        } else {
            $posts = $this->postModel->getAll($offset, $limit);
        }

        // Format posts
        foreach ($posts as &$post) {
            $post['time_ago'] = timeAgo($post['created_at']);
            $post['liked_by_user'] = (bool)$post['liked_by_user'];
            $post['comments_count'] = $this->commentModel->getCount($post['id']);
        }

        if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest') {
            // AJAX request for infinite scroll
            $this->json([
                'posts' => $posts,
                'has_more' => count($posts) === $limit
            ]);
        }

        $this->render('feed', [
            'posts' => $posts,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Show single post
     */
    public function show($id) {
        $post = $this->postModel->getById($id);

        if (!$post) {
            http_response_code(404);
            echo "Post tidak ditemukan";
            exit;
        }

        $post['time_ago'] = timeAgo($post['created_at']);
        $post['liked_by_user'] = (bool)$post['liked_by_user'];
        $comments = $this->commentModel->getByPost($id);

        foreach ($comments as &$comment) {
            $comment['time_ago'] = timeAgo($comment['created_at']);
        }

        $this->render('post', [
            'post' => $post,
            'comments' => $comments,
            'csrf_token' => $this->generateCsrfToken()
        ]);
    }

    /**
     * Create new post
     */
    public function create() {
        $this->requireAuth();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            $this->redirect(BASE_URL . '/');
        }

        $csrfToken = $_POST['csrf_token'] ?? '';
        if (!$this->verifyCsrfToken($csrfToken)) {
            $_SESSION['error'] = 'Token CSRF tidak valid';
            $this->redirect(BASE_URL . '/');
        }

        $caption = trim($_POST['caption'] ?? '');
        $file = $_FILES['media'] ?? null;

        // Validate upload
        $validation = validateUpload($file);
        if (!$validation['valid']) {
            $_SESSION['error'] = implode(', ', $validation['errors']);
            $this->redirect(BASE_URL . '/');
        }

        // Validate EXIF
        $exifResult = validateExif($file['tmp_name']);
        
        // Generate filename and move file
        $filename = generateUploadFilename($validation['extension']);
        $destination = UPLOAD_DIR . $filename;
        
        if (!move_uploaded_file($file['tmp_name'], $destination)) {
            $_SESSION['error'] = 'Gagal menyimpan file';
            $this->redirect(BASE_URL . '/');
        }

        // Determine media type
        $mediaType = str_starts_with($validation['mime_type'], 'video') ? 'video' : 'image';

        // Store EXIF warning in session if needed
        if (!$exifResult['has_exif']) {
            $_SESSION['upload_warning'] = $exifResult['warning'];
        } elseif ($exifResult['is_ai_detected']) {
            $_SESSION['upload_warning'] = $exifResult['warning'];
        }

        // Create post
        $postId = $this->postModel->create(
            currentUserId(),
            $caption,
            $filename,
            $mediaType,
            $exifResult['exif_data']
        );

        $this->redirect(BASE_URL . "/post/{$postId}");
    }

    /**
     * Toggle like (AJAX)
     */
    public function toggleLike() {
        $this->requireAuth();

        $data = json_decode(file_get_contents('php://input'), true);
        $postId = $data['post_id'] ?? 0;
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$this->verifyCsrfToken($csrfToken)) {
            $this->json(['error' => 'Token CSRF tidak valid'], 403);
        }

        $liked = $this->postModel->toggleLike(currentUserId(), $postId);
        $likesCount = $this->postModel->getLikeCount($postId);

        $this->json([
            'liked' => $liked,
            'likes' => $likesCount
        ]);
    }

    /**
     * Add comment (AJAX)
     */
    public function addComment() {
        $this->requireAuth();

        $data = json_decode(file_get_contents('php://input'), true);
        $postId = $data['post_id'] ?? 0;
        $content = trim($data['content'] ?? '');
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$this->verifyCsrfToken($csrfToken)) {
            $this->json(['error' => 'Token CSRF tidak valid'], 403);
        }

        if (empty($content)) {
            $this->json(['error' => 'Komentar tidak boleh kosong'], 400);
        }

        $commentId = $this->commentModel->create($postId, currentUserId(), $content);
        $comment = [
            'id' => $commentId,
            'user_id' => currentUserId(),
            'username' => $_SESSION['username'],
            'avatar' => 'default.png',
            'content' => $content,
            'created_at' => date('Y-m-d H:i:s'),
            'time_ago' => 'Baru saja'
        ];

        $this->json([
            'success' => true,
            'comment' => $comment
        ]);
    }

    /**
     * Report post as AI content
     */
    public function report() {
        $this->requireAuth();

        $data = json_decode(file_get_contents('php://input'), true);
        $postId = $data['post_id'] ?? 0;
        $reason = trim($data['reason'] ?? '');
        $csrfToken = $_SERVER['HTTP_X_CSRF_TOKEN'] ?? '';

        if (!$this->verifyCsrfToken($csrfToken)) {
            $this->json(['error' => 'Token CSRF tidak valid'], 403);
        }

        $autoFlagged = $this->postModel->report(currentUserId(), $postId, $reason);

        $this->json([
            'success' => true,
            'auto_flagged' => $autoFlagged,
            'message' => $autoFlagged 
                ? 'Laporan diterima. Postingan telah disembunyikan karena banyak laporan.'
                : 'Laporan diterima. Terima kasih atas kontribusinya.'
        ]);
    }

    /**
     * Delete post
     */
    public function delete($id) {
        $this->requireAuth();

        $post = $this->postModel->getById($id);
        if (!$post || $post['user_id'] != currentUserId()) {
            http_response_code(403);
            echo "Tidak diizinkan";
            exit;
        }

        // Delete file
        $filePath = UPLOAD_DIR . $post['media_path'];
        if (file_exists($filePath)) {
            unlink($filePath);
        }

        $this->postModel->delete($id, currentUserId());
        $this->redirect(BASE_URL . '/');
    }
}
