<?php
/**
 * Feed View - Main timeline
 */
?>

<div class="space-y-6" x-data="{ offset: <?= count($posts) ?>, hasMore: true, loading: false }">
    <?php if (empty($posts)): ?>
    <div class="text-center py-12">
        <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"/>
        </svg>
        <h3 class="text-lg font-medium text-gray-900 mb-2">Belum ada konten</h3>
        <p class="text-gray-500">
            <?php if (isset($_SESSION['user_id'])): ?>
                Ikuti pengguna lain atau unggah konten pertamamu!
            <?php else: ?>
                <a href="<?= BASE_URL ?>/register" class="text-blue-500 hover:underline">Daftar</a> untuk mulai berbagi.
            <?php endif; ?>
        </p>
    </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
        <article class="bg-white rounded-xl shadow-sm border overflow-hidden" 
                 x-data="{ 
                     liked: <?= $post['liked_by_user'] ? 'true' : 'false' ?>, 
                     likes: <?= $post['likes_count'] ?>,
                     showReportModal: false,
                     reportReason: ''
                 }">
            <!-- Header -->
            <div class="flex items-center gap-3 p-4">
                <a href="<?= BASE_URL ?>/profile/<?= $this->e($post['username']) ?>">
                    <img src="<?= BASE_URL ?>/uploads/<?= $this->e($post['avatar']) ?>" 
                         class="w-10 h-10 rounded-full object-cover">
                </a>
                <div class="flex-1">
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
                <button @click="showReportModal = true" class="text-gray-400 hover:text-red-500" title="Laporkan sebagai AI">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 21v-4m0 0V5a2 2 0 012-2h6.5l1 1H21l-3 6 3 6h-8.5l-1-1H5a2 2 0 00-2 2zm9-13.5V9"/>
                    </svg>
                </button>
            </div>

            <!-- Media -->
            <?php if ($post['media_type'] === 'video'): ?>
                <video src="<?= BASE_URL ?>/uploads/<?= $this->e($post['media_path']) ?>" 
                       class="w-full max-h-[500px] object-cover" 
                       controls></video>
            <?php else: ?>
                <img src="<?= BASE_URL ?>/uploads/<?= $this->e($post['media_path']) ?>" 
                     class="w-full max-h-[500px] object-cover">
            <?php endif; ?>

            <!-- Actions -->
            <div class="p-4">
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
                    <a href="<?= BASE_URL ?>/post/<?= $post['id'] ?>" class="flex items-center gap-2 text-gray-600">
                        <svg class="w-7 h-7" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
                        </svg>
                    </a>
                </div>

                <p class="font-medium text-gray-900"><span x-text="likes"></span> suka</p>

                <?php if (!empty($post['caption'])): ?>
                    <p class="mt-2 text-gray-800">
                        <span class="font-medium"><?= $this->e($post['username']) ?></span>
                        <?= $this->e($post['caption']) ?>
                    </p>
                <?php endif; ?>

                <a href="<?= BASE_URL ?>/post/<?= $post['id'] ?>" class="text-gray-500 text-sm mt-2 block">
                    Lihat semua <?= $post['comments_count'] ?> komentar
                </a>
            </div>

            <!-- Report Modal -->
            <div x-show="showReportModal" 
                 x-cloak
                 class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
                 @click.away="showReportModal = false">
                <div class="bg-white rounded-xl max-w-md w-full p-6">
                    <h3 class="text-lg font-bold mb-2">Laporkan Konten AI</h3>
                    <p class="text-sm text-gray-600 mb-4">Mengapa Anda curiga konten ini dibuat dengan AI?</p>
                    
                    <textarea x-model="reportReason" 
                              rows="3" 
                              class="w-full border rounded-lg px-3 py-2 mb-4"
                              placeholder="Jelaskan alasan Anda..."></textarea>
                    
                    <div class="flex justify-end gap-3">
                        <button @click="showReportModal = false" class="px-4 py-2 text-gray-600">Batal</button>
                        <button @click="
                            fetch('<?= BASE_URL ?>/post/report', {
                                method: 'POST',
                                body: JSON.stringify({ post_id: <?= $post['id'] ?>, reason: reportReason }),
                                headers: {
                                    'Content-Type': 'application/json',
                                    'X-CSRF-TOKEN': '<?= $csrf_token ?>'
                                }
                            })
                            .then(r => r.json())
                            .then(data => {
                                alert(data.message);
                                showReportModal = false;
                            })
                        " class="px-4 py-2 bg-red-500 text-white rounded-lg hover:bg-red-600">
                            Laporkan
                        </button>
                    </div>
                </div>
            </div>
        </article>
        <?php endforeach; ?>
    <?php endif; ?>

    <!-- Infinite Scroll Trigger -->
    <div x-intersect="$el.loadMore()" @load-more.window="
        if (hasMore && !loading) {
            loading = true;
            fetch('<?= BASE_URL ?>/?offset=' + offset)
                .then(r => r.json())
                .then(data => {
                    // Append new posts (requires JavaScript implementation)
                    hasMore = data.has_more;
                    offset += data.posts.length;
                    loading = false;
                });
        }
    "></div>
</div>
