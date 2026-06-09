<form method="POST" action="<?= BASE_URL ?>/login" class="max-w-md mx-auto mt-12">
    <input type="hidden" name="csrf_token" value="<?= $csrf_token ?>">
    
    <div class="bg-white rounded-xl shadow-sm border p-8">
        <h1 class="text-2xl font-bold text-center mb-6">Masuk ke HumanGrid</h1>
        
        <?php if ($error): ?>
        <div class="bg-red-50 border border-red-200 text-red-700 px-4 py-3 rounded-lg mb-4">
            <?= $this->e($error) ?>
        </div>
        <?php endif; ?>

        <div class="space-y-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Username atau Email</label>
                <input type="text" 
                       name="username" 
                       required
                       class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Password</label>
                <input type="password" 
                       name="password" 
                       required
                       class="w-full border rounded-lg px-4 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit" 
                    class="w-full bg-blue-500 text-white py-3 rounded-lg font-medium hover:bg-blue-600">
                Masuk
            </button>
        </div>

        <p class="text-center mt-6 text-gray-600">
            Belum punya akun? 
            <a href="<?= BASE_URL ?>/register" class="text-blue-500 hover:underline">Daftar</a>
        </p>
    </div>
</form>
