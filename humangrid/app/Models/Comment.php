<?php
/**
 * Comment Model
 * HumanGrid - Anti-AI Social Media Platform
 */

require_once __DIR__ . '/../core/Model.php';

class Comment extends Model {
    /**
     * Get comments for a post
     */
    public function getByPost($postId) {
        return $this->fetchAll(
            "SELECT c.*, u.username, u.avatar
             FROM comments c
             JOIN users u ON c.user_id = u.id
             WHERE c.post_id = ?
             ORDER BY c.created_at ASC",
            [$postId]
        );
    }

    /**
     * Create comment
     */
    public function create($postId, $userId, $content) {
        $this->query(
            "INSERT INTO comments (post_id, user_id, content) VALUES (?, ?, ?)",
            [$postId, $userId, $content]
        );
        
        return $this->lastInsertId();
    }

    /**
     * Delete comment
     */
    public function delete($commentId, $userId) {
        $this->query("DELETE FROM comments WHERE id = ? AND user_id = ?", [$commentId, $userId]);
    }

    /**
     * Get comment count for post
     */
    public function getCount($postId) {
        $result = $this->fetchOne("SELECT COUNT(*) as count FROM comments WHERE post_id = ?", [$postId]);
        return $result['count'];
    }
}
