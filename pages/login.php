<?php
session_start();
$currentPage = basename($_SERVER['PHP_SELF']);
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin') {
        header('Location: ../admin/dashboard.php');
        exit;
    } elseif ($currentPage !== 'home.php') {
        header('Location: home.php');
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Penjualan Miniatur</title>
    <link href="../assets/css/output.css" rel="stylesheet">
</head>

<body class="bg-gray-100 flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-6 rounded shadow">
        <h2 class="text-2xl font-semibold text-center mb-6">Login</h2>

        <?php if (isset($_GET['error'])): ?>
            <div class="bg-red-100 text-red-600 p-2 rounded mb-4 text-sm">
                <?= htmlspecialchars($_GET['error']) ?>
            </div>
        <?php endif; ?>

        <form action="../process/login_process.php" method="POST" class="space-y-4">
            <div>
                <label for="email" class="block text-sm">Email</label>
                <input type="email" name="email" id="email" required
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>
            <div>
                <label for="password" class="block text-sm">Password</label>
                <input type="password" name="password" id="password" required
                    class="w-full px-4 py-2 border rounded focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <button type="submit"
                class="w-full bg-blue-600 text-white py-2 rounded hover:bg-blue-700">Login</button>
        </form>

        <p class="mt-4 text-center text-sm">
            Belum punya akun? <a href="register.php" class="text-blue-600 hover:underline">Daftar sekarang</a>
        </p>
    </div>

</body>

</html>