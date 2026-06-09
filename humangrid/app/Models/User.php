<?php
/**
 * User Model
 * HumanGrid - Anti-AI Social Media Platform
 */

require_once __DIR__ . '/../core/Model.php';

class User extends Model {
    /**
     * Find user by username or email
     */
    public function findByUsernameOrEmail($value) {
        return $this->fetchOne(
            "SELECT * FROM users WHERE username = ? OR email = ?",
            [$value, $value]
        );
    }

    /**
     * Find user by ID
     */
    public function findById($id) {
        return $this->fetchOne("SELECT * FROM users WHERE id = ?", [$id]);
    }

    /**
     * Create new user
     */
    public function create($username, $email, $password, $fullName = null) {
        $passwordHash = password_hash($password, PASSWORD_DEFAULT);
        
        $this->query(
            "INSERT INTO users (username, email, password_hash, full_name) VALUES (?, ?, ?, ?)",
            [$username, $email, $passwordHash, $fullName]
        );
        
        return $this->lastInsertId();
    }

    /**
     * Update user profile
     */
    public function updateProfile($userId, $fullName, $bio) {
        $this->query(
            "UPDATE users SET full_name = ?, bio = ? WHERE id = ?",
            [$fullName, $bio, $userId]
        );
    }

    /**
     * Update avatar
     */
    public function updateAvatar($userId, $avatarPath) {
        $this->query("UPDATE users SET avatar = ? WHERE id = ?", [$avatarPath, $userId]);
    }

    /**
     * Get user with stats
     */
    public function getUserWithStats($userId) {
        return $this->fetchOne(
            "SELECT u.*,
                    (SELECT COUNT(*) FROM posts WHERE user_id = u.id) as post_count,
                    (SELECT COUNT(*) FROM follows WHERE following_id = u.id) as follower_count,
                    (SELECT COUNT(*) FROM follows WHERE follower_id = u.id) as following_count
             FROM users u
             WHERE u.id = ?",
            [$userId]
        );
    }

    /**
     * Search users
     */
    public function search($query, $limit = 10) {
        return $this->fetchAll(
            "SELECT id, username, full_name, avatar, is_verified_human 
             FROM users 
             WHERE username LIKE ? OR full_name LIKE ?
             LIMIT ?",
            ["%{$query}%", "%{$query}%", $limit]
        );
    }

    /**
     * Check if user follows another
     */
    public function isFollowing($followerId, $followingId) {
        $result = $this->fetchOne(
            "SELECT 1 FROM follows WHERE follower_id = ? AND following_id = ?",
            [$followerId, $followingId]
        );
        return !empty($result);
    }

    /**
     * Follow user
     */
    public function follow($followerId, $followingId) {
        $this->query(
            "INSERT IGNORE INTO follows (follower_id, following_id) VALUES (?, ?)",
            [$followerId, $followingId]
        );
    }

    /**
     * Unfollow user
     */
    public function unfollow($followerId, $followingId) {
        $this->query(
            "DELETE FROM follows WHERE follower_id = ? AND following_id = ?",
            [$followerId, $followingId]
        );
    }

    /**
     * Set verified human badge
     */
    public function setVerifiedHuman($userId, $verified = true) {
        $this->query("UPDATE users SET is_verified_human = ? WHERE id = ?", [$verified, $userId]);
    }
}
