<form method="POST" action="<?= BASE_URL ?>/register" class="max-w-md mx-auto mt-12">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    
    <div class="bg-white rounded-xl shadow-sm border p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Daftar HumanGrid</h1>
        
        <?php if (!empty($errors)): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4 space-y-1">
            <?php foreach ($errors as $error): ?>
                <p>• <?= $this->e($error) ?></p>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username</label>
                <input type="text" 
                       name="username" 
                       required
                       maxlength="30"
                       pattern="[a-zA-Z0-9_]+"
                       value="<?= $this->e($old['username'] ?? '') ?>"
                       class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Huruf, angka, dan underscore saja (3-30 karakter)</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Nama Lengkap (opsional)</label>
                <input type="text" 
                       name="full_name" 
                       value="<?= $this->e($old['full_name'] ?? '') ?>"
                       class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Email</label>
                <input type="email" 
                       name="email" 
                       required
                       value="<?= $this->e($old['email'] ?? '') ?>"
                       class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" 
                       name="password" 
                       required
                       minlength="6"
                       class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                <p class="text-xs text-gray-500 mt-1">Minimal 6 karakter</p>
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Konfirmasi Password</label>
                <input type="password" 
                       name="password_confirm" 
                       required
                       class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600">
                Daftar
            </button>
        </div>

        <p class="text-center mt-6 text-gray-600">
            Sudah punya akun? 
            <a href="<?= BASE_URL ?>/login" class="text-blue-500 hover:underline">Masuk</a>
        </p>

        <div class="mt-6 p-4 bg-blue-50 border border-blue-200 rounded-lg text-sm text-blue-800">
            <strong>📷 Platform Anti-AI:</strong> HumanGrid berkomitmen menampilkan hanya konten asli buatan manusia. Tidak ada filter AI, generator, atau rekomendasi algoritmik.
        </div>
    </div>
</form>
