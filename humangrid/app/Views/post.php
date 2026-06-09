<?php
/**
 * Single Post View
 */
?>

<div class="bg-white rounded-xl shadow-sm border overflow-hidden">
    <!-- Header -->
    <div class="flex items-center justify-between p-4 border-b">
        <div class="flex items-center gap-3">
            <a href="<?= BASE_URL ?>/profile/<?= $this->e($post['username']) ?>">
                <img src="<?= BASE_URL ?>/uploads/<?= $this->e($post['avatar']) ?>" 
                     class="w-10 h-10 rounded-full object-cover">
            </a>
            <div>
                <a href="<?= BASE_URL ?>/profile/<?= $this->e($post['username']) ?>" 
                   class="font-medium text-gray-900 hover:underline">
                    <?= $this->e($post['username']) ?>
                    <?php if ($post['is_verified_human']): ?>
                        <span class="inline-flex items-center text-blue-500" title="Verified Human">
                            <svg class="w-4 h-4 ml-1" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                        </span>
                    <?php endif; ?>
                </a>
                <p class="text-sm text-gray-500"><?= $this->e($post['time_ago']) ?></p>
            </div>
        </div>
        
        <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $post['user_id']): ?>
        <form action="<?= BASE_URL ?>/post/<?= $post['id'] ?>/delete" method="POST" onsubmit="return confirm('Hapus postingan ini?')">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            <button type="submit" class="text-red-500 hover:text-red-700 text-sm">Hapus</button>
        </form>
        <?php endif; ?>
    </div>

    <!-- Media -->
    <?php if ($post['media_type'] === 'video'): ?>
        <video src="<?= BASE_URL ?>/uploads/<?= $this->e($post['media_path']) ?>" 
               class="w-full" 
               controls></video>
    <?php else: ?>
        <img src="<?= BASE_URL ?>/uploads/<?= $this->e($post['media_path']) ?>" 
             class="w-full">
    <?php endif; ?>

    <!-- EXIF Info (if available) -->
    <?php if ($post['exif_data']): ?>
        <?php $exif = json_decode($post['exif_data'], true); ?>
        <?php if ($exif): ?>
        <div class="px-4 py-2 bg-gray-50 border-t text-xs text-gray-500">
            📷 
            <?php if ($exif['Make']): ?><?= $this->e($exif['Make']) ?><?php endif; ?>
            <?php if ($exif['Model']): ?><?= $this->e($exif['Model']) ?><?php endif; ?>
            <?php if ($exif['DateTimeOriginal']): ?>• <?= $this->e($exif['DateTimeOriginal']) ?><?php endif; ?>
        </div>
        <?php endif; ?>
    <?php endif; ?>

    <!-- Actions & Caption -->
    <div class="p-4" x-data="{ liked: <?= $post['liked_by_user'] ? 'true' : 'false' ?>, likes: <?= $post['likes_count'] ?> }">
        <div class="flex items-center gap-4 mb-3">
            <button @click="
                fetch('<?= BASE_URL ?>/like/toggle', {
                    method: 'POST',
                    body: JSON.stringify({ post_id: <?= $post['id'] ?> }),
                    headers: {
                        'Content-Type': 'application/json',
                        'X-CSRF-TOKEN': '<?= $csrf_token ?>'
                    }
                })
                .then(r => r.json())
                .then(data => { liked = data.liked; likes = data.likes; })
            " class="flex items-center gap-2">
                <svg class="w-7 h-7" :class="liked ? 'text-red-500 fill-current' : 'text-gray-600'" 
                     fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                </svg>
            </button>
        </div>

        <p class="font-medium text-gray-900"><span x-text="likes"></span> suka</p>

        <?php if (!empty($post['caption'])): ?>
            <p class="mt-2 text-gray-800">
                <span class="font-medium"><?= $this->e($post['username']) ?></span>
                <?= $this->e($post['caption']) ?>
            </p>
        <?php endif; ?>
    </div>

    <!-- Comments -->
    <div class="border-t p-4">
        <h3 class="font-medium mb-4">Komentar (<?= count($comments) ?>)</h3>
        
        <div class="space-y-4 mb-4">
            <?php foreach ($comments as $comment): ?>
            <div class="flex gap-3">
                <a href="<?= BASE_URL ?>/profile/<?= $this->e($comment['username']) ?>">
                    <img src="<?= BASE_URL ?>/uploads/<?= $this->e($comment['avatar']) ?>" 
                         class="w-8 h-8 rounded-full object-cover">
                </a>
                <div class="flex-1">
                    <div class="bg-gray-50 rounded-lg px-3 py-2">
                        <a href="<?= BASE_URL ?>/profile/<?= $this->e($comment['username']) ?>" 
                           class="font-medium text-sm hover:underline">
                            <?= $this->e($comment['username']) ?>
                        </a>
                        <p class="text-gray-800"><?= $this->e($comment['content']) ?></p>
                    </div>
                    <p class="text-xs text-gray-500 mt-1"><?= $this->e($comment['time_ago']) ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>

        <?php if (isset($_SESSION['user_id'])): ?>
        <div x-data="{ content: '' }" class="flex gap-3">
            <img src="<?= BASE_URL ?>/uploads/default.png" class="w-8 h-8 rounded-full object-cover">
            <div class="flex-1 flex gap-2">
                <input type="text" 
                       x-model="content"
                       placeholder="Tambahkan komentar..."
                       class="flex-1 border rounded-full px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                       @keydown.enter="
                           fetch('<?= BASE_URL ?>/post/<?= $post['id'] ?>/comment', {
                               method: 'POST',
                               body: JSON.stringify({ content: content }),
                               headers: {
                                   'Content-Type': 'application/json',
                                   'X-CSRF-TOKEN': '<?= $csrf_token ?>'
                               }
                           })
                           .then(r => r.json())
                           .then(data => {
                               if (data.success) {
                                   location.reload();
                               }
                           })
                       ">
                <button @click="
                    fetch('<?= BASE_URL ?>/post/<?= $post['id'] ?>/comment', {
                        method: 'POST',
                        body: JSON.stringify({ content: content }),
                        headers: {
                            'Content-Type': 'application/json',
                            'X-CSRF-TOKEN': '<?= $csrf_token ?>'
                        }
                    })
                    .then(r => r.json())
                    .then(data => {
                        if (data.success) {
                            location.reload();
                        }
                    })
                " class="text-blue-500 font-medium">Kirim</button>
            </div>
        </div>
        <?php else: ?>
        <p class="text-sm text-gray-500 text-center">
            <a href="<?= BASE_URL ?>/login" class="text-blue-500 hover:underline">Masuk</a> untuk berkomentar
        </p>
        <?php endif; ?>
    </div>
</div>

<a href="<?= BASE_URL ?>/" class="block mt-4 text-blue-500 hover:underline">← Kembali ke feed</a>
