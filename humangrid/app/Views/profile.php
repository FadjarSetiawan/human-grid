<?php
/**
 * Profile View
 */
?>

<div class="mb-8">
    <!-- Profile Header -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <div class="flex items-center gap-6">
            <img src="<?= BASE_URL ?>/uploads/<?= $this->e($user['avatar']) ?>" 
                 class="w-24 h-24 rounded-full object-cover">
            
            <div class="flex-1">
                <div class="flex items-center gap-3 mb-2">
                    <h1 class="text-2xl font-bold"><?= $this->e($user['username']) ?></h1>
                    <?php if ($user['is_verified_human']): ?>
                        <span class="inline-flex items-center text-blue-500" title="Verified Human">
                            <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M6.267 3.455a3.066 3.066 0 001.745-.723 3.066 3.066 0 013.976 0 3.066 3.066 0 001.745.723 3.066 3.066 0 012.812 2.812c.051.643.304 1.254.723 1.745a3.066 3.066 0 010 3.976 3.066 3.066 0 00-.723 1.745 3.066 3.066 0 01-2.812 2.812 3.066 3.066 0 00-1.745.723 3.066 3.066 0 01-3.976 0 3.066 3.066 0 00-1.745-.723 3.066 3.066 0 01-2.812-2.812 3.066 3.066 0 00-.723-1.745 3.066 3.066 0 010-3.976 3.066 3.066 0 00.723-1.745 3.066 3.066 0 012.812-2.812zm7.44 5.252a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                            </svg>
                            <span class="ml-1 text-sm">Verified Human</span>
                        </span>
                    <?php endif; ?>
                </div>
                
                <?php if ($user['full_name']): ?>
                    <p class="text-gray-900 font-medium"><?= $this->e($user['full_name']) ?></p>
                <?php endif; ?>
                
                <?php if ($user['bio']): ?>
                    <p class="text-gray-600 mt-1"><?= $this->e($user['bio']) ?></p>
                <?php endif; ?>

                <!-- Stats -->
                <div class="flex gap-6 mt-4">
                    <div class="text-center">
                        <span class="font-bold text-lg"><?= $user['post_count'] ?></span>
                        <span class="text-gray-500 text-sm"> posting</span>
                    </div>
                    <div class="text-center">
                        <span class="font-bold text-lg"><?= $user['follower_count'] ?></span>
                        <span class="text-gray-500 text-sm"> pengikut</span>
                    </div>
                    <div class="text-center">
                        <span class="font-bold text-lg"><?= $user['following_count'] ?></span>
                        <span class="text-gray-500 text-sm"> mengikuti</span>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="flex flex-col gap-2">
                <?php if (isset($_SESSION['user_id']) && $_SESSION['user_id'] == $user['id']): ?>
                    <a href="<?= BASE_URL ?>/profile/edit" class="px-4 py-2 border rounded-lg hover:bg-gray-50 text-center">
                        Edit Profil
                    </a>
                <?php elseif (isset($_SESSION['user_id'])): ?>
                    <button x-data="{ following: <?= $isFollowing ? 'true' : 'false' ?> }"
                            @click="
                                fetch('<?= BASE_URL ?>/follow/toggle', {
                                    method: 'POST',
                                    body: JSON.stringify({ user_id: <?= $user['id'] ?> }),
                                    headers: {
                                        'Content-Type': 'application/json',
                                        'X-CSRF-TOKEN': '<?= $csrf_token ?>'
                                    }
                                })
                                .then(r => r.json())
                                .then(data => {
                                    following = data.following;
                                })
                            "
                            class="px-4 py-2 rounded-lg text-center"
                            :class="following ? 'border bg-gray-100' : 'bg-blue-500 text-white hover:bg-blue-600'"
                            x-text="following ? 'Mengikuti' : 'Ikuti'">
                    </button>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>/login" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600 text-center">
                        Masuk untuk mengikuti
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <!-- Posts Grid -->
    <h2 class="text-lg font-bold mb-4">Postingan</h2>
    
    <?php if (empty($posts)): ?>
        <div class="text-center py-12 bg-white rounded-xl border">
            <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
            </svg>
            <p class="text-gray-500">Belum ada postingan</p>
        </div>
    <?php else: ?>
        <div class="grid grid-cols-3 gap-2">
            <?php foreach ($posts as $post): ?>
            <a href="<?= BASE_URL ?>/post/<?= $post['id'] ?>" class="relative aspect-square group">
                <?php if ($post['media_type'] === 'video'): ?>
                    <video src="<?= BASE_URL ?>/uploads/<?= $this->e($post['media_path']) ?>" 
                           class="w-full h-full object-cover"></video>
                <?php else: ?>
                    <img src="<?= BASE_URL ?>/uploads/<?= $this->e($post['media_path']) ?>" 
                         class="w-full h-full object-cover">
                <?php endif; ?>
                
                <!-- Hover overlay with likes -->
                <div class="absolute inset-0 bg-black/50 opacity-0 group-hover:opacity-100 transition-opacity flex items-center justify-center">
                    <div class="flex items-center gap-4 text-white">
                        <svg class="w-6 h-6 fill-current" viewBox="0 0 24 24">
                            <path d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"/>
                        </svg>
                        <span><?= $post['likes_count'] ?></span>
                    </div>
                </div>
            </a>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>
