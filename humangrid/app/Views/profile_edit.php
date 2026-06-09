<?php
/**
 * Profile Edit View
 */
?>

<div class="max-w-lg mx-auto">
    <h1 class="text-2xl font-bold mb-6">Edit Profil</h1>

    <?php if ($success): ?>
    <div class="bg-green-50 border border-green-200 text-green-700 px-4 py-3 rounded-lg mb-4">
        <?= $this->e($success) ?>
    </div>
    <?php endif; ?>

    <?php if ($error): ?>
    <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
        <?= $this->e($error) ?>
    </div>
    <?php endif; ?>

    <!-- Edit Info Form -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mb-6">
        <h2 class="font-bold mb-4">Informasi Profil</h2>
        
        <form method="POST" action="<?= BASE_URL ?>/profile/update">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap</label>
                    <input type="text" 
                           name="full_name" 
                           value="<?= $this->e($user['full_name'] ?? '') ?>"
                           class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Bio</label>
                    <textarea name="bio" 
                              rows="3"
                              class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500"><?= $this->e($user['bio'] ?? '') ?></textarea>
                </div>

                <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                    Simpan Perubahan
                </button>
            </div>
        </form>
    </div>

    <!-- Upload Avatar Form -->
    <div class="bg-white rounded-xl shadow-sm border p-6">
        <h2 class="font-bold mb-4">Foto Profil</h2>
        
        <form method="POST" action="<?= BASE_URL ?>/profile/avatar" enctype="multipart/form-data">
            <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
            
            <div class="flex items-center gap-4 mb-4">
                <img src="<?= BASE_URL ?>/uploads/<?= $this->e($user['avatar']) ?>" 
                     class="w-16 h-16 rounded-full object-cover">
                <input type="file" 
                       name="avatar" 
                       accept="image/*"
                       required
                       class="flex-1 border rounded-lg px-3 py-2">
            </div>

            <button type="submit" class="px-4 py-2 bg-blue-500 text-white rounded-lg hover:bg-blue-600">
                Upload Avatar Baru
            </button>
        </form>
    </div>

    <!-- Verified Human Badge Info -->
    <div class="bg-white rounded-xl shadow-sm border p-6 mt-6">
        <h2 class="font-bold mb-2">Badge "Verified Human"</h2>
        
        <?php if ($user['is_verified_human']): ?>
            <div class="flex items-center gap-2 text-green-600">
                <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 20 20">
                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/>
                </svg>
                <span>Akun Anda telah terverifikasi sebagai manusia!</span>
            </div>
        <?php else: ?>
            <p class="text-gray-600 mb-4">
                Dapatkan badge "Verified Human" dengan mengajukan verifikasi manual. 
                Hubungi admin untuk proses verifikasi.
            </p>
        <?php endif; ?>
    </div>

    <a href="<?= BASE_URL ?>/profile" class="block mt-6 text-blue-500 hover:underline">← Kembali ke profil</a>
</div>
