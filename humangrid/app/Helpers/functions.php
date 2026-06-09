<?php
/**
 * Helper Functions
 * HumanGrid - Anti-AI Social Media Platform
 */

/**
 * Validate and read EXIF data from uploaded file
 */
function validateExif($tmpPath) {
    $result = [
        'has_exif' => false,
        'is_ai_detected' => false,
        'exif_data' => null,
        'warning' => null
    ];

    // Check if file is an image
    $imageInfo = @getimagesize($tmpPath);
    if (!$imageInfo || !in_array($imageInfo['mime'], ['image/jpeg', 'image/png', 'image/gif'])) {
        return $result;
    }

    // Read EXIF data (only for JPEG)
    if ($imageInfo['mime'] === 'image/jpeg') {
        $exif = @exif_read_data($tmpPath);
        
        if ($exif !== false && !empty($exif)) {
            $result['has_exif'] = true;
            
            // Check for AI-related keywords in Make/Model/Software
            $aiKeywords = ['DALL-E', 'Midjourney', 'Stable Diffusion', 'Firefly', 'Adobe AI', 'Generative AI'];
            $checkFields = [
                $exif['Make'] ?? '',
                $exif['Model'] ?? '',
                $exif['Software'] ?? '',
                $exif['ImageDescription'] ?? ''
            ];
            
            $combined = implode(' ', $checkFields);
            
            foreach ($aiKeywords as $keyword) {
                if (stripos($combined, $keyword) !== false) {
                    $result['is_ai_detected'] = true;
                    $result['warning'] = "Konten ini terdeteksi menggunakan AI: {$keyword}";
                    break;
                }
            }
            
            // Store relevant EXIF data (strip sensitive info)
            $result['exif_data'] = [
                'Make' => $exif['Make'] ?? null,
                'Model' => $exif['Model'] ?? null,
                'DateTimeOriginal' => $exif['DateTimeOriginal'] ?? null,
                'Software' => $exif['Software'] ?? null,
            ];
        } else {
            $result['warning'] = 'Tidak ada metadata kamera. Konten mungkin hasil AI.';
        }
    }

    return $result;
}

/**
 * Generate unique filename for upload
 */
function generateUploadFilename($extension) {
    return uniqid('hg_', true) . '.' . strtolower($extension);
}

/**
 * Validate file upload
 */
function validateUpload($file) {
    $errors = [];
    
    if (!isset($file) || $file['error'] !== UPLOAD_ERR_OK) {
        $errors[] = 'Gagal mengupload file';
        return ['valid' => false, 'errors' => $errors];
    }
    
    // Check file size
    if ($file['size'] > MAX_FILE_SIZE) {
        $errors[] = 'Ukuran file melebihi 10MB';
    }
    
    // Check extension
    $extension = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
    if (!in_array($extension, ALLOWED_EXTENSIONS)) {
        $errors[] = 'Tipe file tidak diizinkan';
    }
    
    // Check MIME type
    $allowedMimes = ['image/jpeg', 'image/png', 'image/gif', 'video/mp4'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mimeType = finfo_file($finfo, $file['tmp_name']);
    finfo_close($finfo);
    
    if (!in_array($mimeType, $allowedMimes)) {
        $errors[] = 'Tipe file tidak valid';
    }
    
    return [
        'valid' => empty($errors),
        'errors' => $errors,
        'extension' => $extension,
        'mime_type' => $mimeType
    ];
}

/**
 * Format time ago
 */
function timeAgo($datetime) {
    $timestamp = strtotime($datetime);
    $diff = time() - $timestamp;
    
    if ($diff < 60) {
        return 'Baru saja';
    } elseif ($diff < 3600) {
        $mins = floor($diff / 60);
        return "{$mins} menit yang lalu";
    } elseif ($diff < 86400) {
        $hours = floor($diff / 3600);
        return "{$hours} jam yang lalu";
    } elseif ($diff < 604800) {
        $days = floor($diff / 86400);
        return "{$days} hari yang lalu";
    } else {
        return date('d M Y', $timestamp);
    }
}

/**
 * Get current user ID
 */
function currentUserId() {
    return $_SESSION['user_id'] ?? null;
}

/**
 * Check if user is verified human
 */
function isVerifiedHuman($userId = null) {
    $userId = $userId ?? currentUserId();
    if (!$userId) return false;
    
    $pdo = require __DIR__ . '/../config/database.php';
    $stmt = $pdo->prepare("SELECT is_verified_human FROM users WHERE id = ?");
    $stmt->execute([$userId]);
    $user = $stmt->fetch();
    
    return $user && $user['is_verified_human'];
}
