<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($pageTitle) ? $this->e($pageTitle) . ' - ' : '' ?>HumanGrid</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <script defer src="https://cdn.jsdelivr.net/npm/alpinejs@3.x.x/dist/cdn.min.js"></script>
    <style>
        [x-cloak] { display: none !important; }
    </style>
</head>
<body class="bg-gray-50 min-h-screen">
    <!-- Navigation -->
    <nav class="bg-white shadow-sm sticky top-0 z-50" x-data="{ searchOpen: false, searchQuery: '' }">
        <div class="max-w-4xl mx-auto px-4 py-3">
            <div class="flex items-center justify-between">
                <!-- Logo -->
                <a href="<?= BASE_URL ?>/" class="text-xl font-bold text-gray-900">
                    HumanGrid
                </a>

                <!-- Search (desktop) -->
                <div class="hidden md:flex flex-1 mx-8">
                    <div class="relative w-full max-w-md">
                        <input 
                            type="text" 
                            placeholder="Cari pengguna..."
                            class="w-full bg-gray-100 rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                            x-model="searchQuery"
                            @input.debounce.300ms="
                                if (searchQuery.length >= 2) {
                                    fetch('<?= BASE_URL ?>/api/search?q=' + encodeURIComponent(searchQuery))
                                        .then(r => r.json())
                                        .then(data => { window.searchResults = data; })
                                }
                            "
                        >
                        <div x-show="searchResults && searchResults.length > 0" 
                             x-cloak
                             class="absolute top-full left-0 right-0 mt-1 bg-white rounded-lg shadow-lg border overflow-hidden">
                            <template x-for="user in searchResults">
                                <a :href="'<?= BASE_URL ?>/profile/' + user.username" 
                                   class="flex items-center gap-3 px-4 py-2 hover:bg-gray-50">
                                    <img :src="'<?= BASE_URL ?>/uploads/' + user.avatar" 
                                         class="w-8 h-8 rounded-full object-cover">
                                    <div>
                                        <p class="font-medium" x-text="user.username"></p>
                                        <p class="text-sm text-gray-500" x-text="user.full_name || ''"></p>
                                    </div>
                                </a>
                            </template>
                        </div>
                    </div>
                </div>

                <!-- Actions -->
                <div class="flex items-center gap-4">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <!-- Upload Button -->
                        <button @click="uploadModal = true" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                            </svg>
                        </button>

                        <!-- Profile -->
                        <a href="<?= BASE_URL ?>/profile" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/>
                            </svg>
                        </a>

                        <!-- Logout -->
                        <a href="<?= BASE_URL ?>/logout" class="text-gray-600 hover:text-gray-900">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"/>
                            </svg>
                        </a>
                    <?php else: ?>
                        <a href="<?= BASE_URL ?>/login" class="text-gray-600 hover:text-gray-900">Masuk</a>
                        <a href="<?= BASE_URL ?>/register" class="bg-blue-500 text-white px-4 py-2 rounded-lg hover:bg-blue-600">Daftar</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="max-w-2xl mx-auto mt-6 px-4 pb-12">
        <?= $content ?>
    </main>

    <!-- Upload Modal -->
    <?php if (isset($_SESSION['user_id'])): ?>
    <div x-data="{ uploadModal: false, preview: null, exifWarning: null }" 
         x-show="uploadModal" 
         x-cloak
         class="fixed inset-0 bg-black/50 z-50 flex items-center justify-center p-4"
         @click.away="uploadModal = false">
        <div class="bg-white rounded-xl max-w-lg w-full p-6">
            <h2 class="text-xl font-bold mb-4">Unggah Konten Baru</h2>
            
            <?php if (isset($_SESSION['upload_warning'])): ?>
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-3 mb-4 text-sm text-yellow-800">
                ⚠️ <?= $this->e($_SESSION['upload_warning']) ?>
            </div>
            <?php unset($_SESSION['upload_warning']); endif; ?>

            <form action="<?= BASE_URL ?>/post/create" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
                
                <!-- File Input -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Foto atau Video</label>
                    <input type="file" 
                           name="media" 
                           accept="image/*,video/*"
                           required
                           @change="
                               const file = $event.target.files[0];
                               if (file) {
                                   const reader = new FileReader();
                                   reader.onload = e => preview = e.target.result;
                                   reader.readAsDataURL(file);
                               }
                           "
                           class="w-full border rounded-lg px-3 py-2">
                    
                    <!-- Preview -->
                    <div x-show="preview" class="mt-3">
                        <img :src="preview" class="max-h-48 rounded-lg object-cover">
                    </div>
                </div>

                <!-- Caption -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Caption (opsional)</label>
                    <textarea name="caption" 
                              rows="3" 
                              class="w-full border rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"
                              placeholder="Tulis sesuatu..."></textarea>
                </div>

                <!-- Info -->
                <div class="bg-blue-50 border border-blue-200 rounded-lg p-3 mb-4 text-sm text-blue-800">
                    <strong>📷 Prinsip Anti-AI:</strong> Konten wajib hasil kreasi manusia. Foto tanpa metadata kamera mungkin akan ditandai untuk review.
                </div>

                <!-- Actions -->
                <div class="flex justify-end gap-3">
                    <button type="button" 
                            @click="uploadModal = false"
                            class="px-4 py-2 text-gray-600 hover:text-gray-900">
                        Batal
                    </button>
                    <button type="submit" 
                            class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                        Bagikan
                    </button>
                </div>
            </form>
        </div>
    </div>
    <script>window.uploadModal = false;</script>
    <?php endif; ?>

    <!-- Flash Messages -->
    <?php if (isset($_SESSION['error'])): ?>
    <div x-data="{ show: true }" 
         x-show="show" 
         x-init="setTimeout(() => show = false, 5000)"
         class="fixed bottom-4 right-4 bg-red-500 text-white px-6 py-3 rounded-lg shadow-lg">
        <?= $this->e($_SESSION['error']) ?>
        <?php unset($_SESSION['error']); ?>
    </div>
    <?php endif; ?>
</body>
</html>
