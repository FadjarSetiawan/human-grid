<?php
/**
 * Post Model
 * HumanGrid - Anti-AI Social Media Platform
 */

require_once __DIR__ . '/../core/Model.php';

class Post extends Model {
    /**
     * Get feed posts (chronological, from followed users)
     */
    public function getFeed($userId, $offset = 0, $limit = 10) {
        return $this->fetchAll(
            "SELECT p.*, u.username, u.avatar, u.is_verified_human,
                    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                    EXISTS(SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?) as liked_by_user
             FROM posts p
             JOIN users u ON p.user_id = u.id
             LEFT JOIN follows f ON p.user_id = f.following_id AND f.follower_id = ?
             WHERE p.is_ai_flagged = FALSE
               AND (f.follower_id = ? OR p.user_id = ?)
             ORDER BY p.created_at DESC
             LIMIT ?, ?",
            [$userId, $userId, $userId, $userId, $offset, $limit]
        );
    }

    /**
     * Get all posts (for public view)
     */
    public function getAll($offset = 0, $limit = 10) {
        return $this->fetchAll(
            "SELECT p.*, u.username, u.avatar, u.is_verified_human,
                    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                    0 as liked_by_user
             FROM posts p
             JOIN users u ON p.user_id = u.id
             WHERE p.is_ai_flagged = FALSE
             ORDER BY p.created_at DESC
             LIMIT ?, ?",
            [$offset, $limit]
        );
    }

    /**
     * Get single post by ID
     */
    public function getById($id) {
        return $this->fetchOne(
            "SELECT p.*, u.username, u.avatar, u.is_verified_human,
                    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count,
                    EXISTS(SELECT 1 FROM likes WHERE post_id = p.id AND user_id = ?) as liked_by_user
             FROM posts p
             JOIN users u ON p.user_id = u.id
             WHERE p.id = ?",
            [currentUserId() ?? 0, $id]
        );
    }

    /**
     * Create new post
     */
    public function create($userId, $caption, $mediaPath, $mediaType, $exifData = null) {
        $this->query(
            "INSERT INTO posts (user_id, caption, media_path, media_type, exif_data) VALUES (?, ?, ?, ?, ?)",
            [$userId, $caption, $mediaPath, $mediaType, $exifData ? json_encode($exifData) : null]
        );
        
        return $this->lastInsertId();
    }

    /**
     * Get user's posts
     */
    public function getByUser($userId, $offset = 0, $limit = 12) {
        return $this->fetchAll(
            "SELECT p.*, 
                    (SELECT COUNT(*) FROM likes WHERE post_id = p.id) as likes_count
             FROM posts p
             WHERE p.user_id = ? AND p.is_ai_flagged = FALSE
             ORDER BY p.created_at DESC
             LIMIT ?, ?",
            [$userId, $offset, $limit]
        );
    }

    /**
     * Toggle like
     */
    public function toggleLike($userId, $postId) {
        $existing = $this->fetchOne(
            "SELECT 1 FROM likes WHERE user_id = ? AND post_id = ?",
            [$userId, $postId]
        );
        
        if ($existing) {
            $this->query("DELETE FROM likes WHERE user_id = ? AND post_id = ?", [$userId, $postId]);
            return false; // Unliked
        } else {
            $this->query("INSERT INTO likes (user_id, post_id) VALUES (?, ?)", [$userId, $postId]);
            return true; // Liked
        }
    }

    /**
     * Get like count
     */
    public function getLikeCount($postId) {
        $result = $this->fetchOne("SELECT COUNT(*) as count FROM likes WHERE post_id = ?", [$postId]);
        return $result['count'];
    }

    /**
     * Report post as AI content
     */
    public function report($reporterId, $postId, $reason) {
        $this->query(
            "INSERT INTO reports (reporter_id, post_id, reason) VALUES (?, ?, ?)",
            [$reporterId, $postId, $reason]
        );
        
        // Auto-flag after 3 reports
        $reportCount = $this->fetchOne(
            "SELECT COUNT(*) as count FROM reports WHERE post_id = ? AND status = 'pending'",
            [$postId]
        );
        
        if ($reportCount['count'] >= 3) {
            $this->query("UPDATE posts SET is_ai_flagged = TRUE WHERE id = ?", [$postId]);
            return true; // Auto-flagged
        }
        
        return false; // Just reported
    }

    /**
     * Delete post
     */
    public function delete($postId, $userId) {
        $this->query("DELETE FROM posts WHERE id = ? AND user_id = ?", [$postId, $userId]);
    }
}
