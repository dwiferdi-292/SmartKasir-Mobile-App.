<?php
require_once '../auth.php';
checkAuth('admin');
require_once '../config.php';

// Handle Add / Delete User
if ($_SERVER['REQUEST_METHOD'] == 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] == 'add') {
        $name = $_POST['name'];
        $email = $_POST['email'];
        $pass = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];

        try {
            $stmt = $pdo->prepare("INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)");
            $stmt->execute([$name, $email, $pass, $role]);
            header("Location: users.php?success=User ditambahkan");
            exit;
        } catch(PDOException $e) {
            header("Location: users.php?error=Gagal tambah user (Email sudah terdaftar)");
            exit;
        }
    } elseif ($_POST['action'] == 'delete') {
        $id = $_POST['id'];
        $pdo->prepare("DELETE FROM users WHERE id = ?")->execute([$id]);
        header("Location: users.php?success=User dihapus");
        exit;
    } elseif ($_POST['action'] == 'change_password') {
        $id = $_POST['id'];
        $new_pass = password_hash($_POST['new_password'], PASSWORD_DEFAULT);
        $pdo->prepare("UPDATE users SET password = ? WHERE id = ?")->execute([$new_pass, $id]);
        header("Location: users.php?success=Kata sandi berhasil diperbarui!");
        exit;
    }
}

$users = $pdo->query("SELECT id, name, email, role, created_at FROM users ORDER BY id DESC")->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Kelola User - SmartKasir</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="dashboard-layout">
        <div class="sidebar">
            <div class="sidebar-header"><i class="fa-solid fa-cube"></i> SmartKasir</div>
            <ul class="sidebar-menu">
                <li><a href="dashboard.php"><i class="fa-solid fa-house"></i> Dashboard</a></li>
                <li><a href="products.php"><i class="fa-solid fa-boxes-stacked"></i> Produk & Stok</a></li>
                <li><a href="users.php" class="active"><i class="fa-solid fa-users-gear"></i> Kelola User</a></li>
            </ul>
        </div>
        <div class="main-content">
            <div class="topbar">
                <h2>MANAJEMEN PENGGUNA</h2>
                <div class="d-flex align-center gap-4">
                    <a href="../logout.php" class="badge badge-danger"><i class="fa-solid fa-power-off"></i> Logout</a>
                    <div class="user-info">
                        <img src="https://ui-avatars.com/api/?name=<?= urlencode($_SESSION['user_name']) ?>&background=4f46e5&color=fff" alt="Avatar">
                        <?= htmlspecialchars($_SESSION['user_name']) ?>
                    </div>
                </div>
            </div>
            <div class="content-area">
                <?php if(isset($_GET['success'])): ?><div class="alert success"><i class="fa-solid fa-check-circle"></i> <?= htmlspecialchars($_GET['success']) ?></div><?php endif; ?>
                <?php if(isset($_GET['error'])): ?><div class="alert error"><i class="fa-solid fa-triangle-exclamation"></i> <?= htmlspecialchars($_GET['error']) ?></div><?php endif; ?>
                
                <div class="d-flex" style="gap: 30px;">
                    <div class="card" style="flex: 1; height: max-content;">
                        <h3 class="mb-4"><i class="fa-solid fa-user-plus"></i> Registrasi Staf Baru</h3>
                        <form action="users.php" method="POST">
                            <input type="hidden" name="action" value="add">
                            <div class="form-group"><label>Nama Lengkap</label><i class="fa-solid fa-user form-icon"></i><input type="text" name="name" required></div>
                            <div class="form-group"><label>Email Akses</label><i class="fa-solid fa-envelope form-icon"></i><input type="email" name="email" required></div>
                            <div class="form-group"><label>Katasandi</label><i class="fa-solid fa-lock form-icon"></i><input type="password" name="password" required></div>
                            <div class="form-group no-icon"><label>Akses / Role</label>
                                <select name="role" required>
                                    <option value="kasir">Kasir (POS)</option>
                                    <option value="admin">Admin (Gudang)</option>
                                    <option value="owner">Owner (Laporan)</option>
                                </select>
                            </div>
                            <button type="submit" class="btn-primary"><i class="fa-solid fa-user-check"></i> Daftarkan</button>
                        </form>

                        <hr style="margin: 30px 0; border: none; border-top: 1px dashed var(--border-color);">

                        <h3 class="mb-4"><i class="fa-solid fa-key"></i> Ganti Password</h3>
                        <form action="users.php" method="POST">
                            <input type="hidden" name="action" value="change_password">
                            <div class="form-group no-icon"><label>Pilih Akun Pengguna</label>
                                <select name="id" required>
                                    <?php foreach($users as $opt): ?>
                                        <option value="<?= $opt['id'] ?>"><?= htmlspecialchars($opt['name']) ?> (<?= $opt['role'] ?>)</option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group"><label>Password Baru</label><i class="fa-solid fa-lock form-icon"></i><input type="password" name="new_password" required placeholder="Gunakan sandi yang kuat" minlength="4"></div>
                            <button type="submit" class="btn-primary" style="background: var(--warning);"><i class="fa-solid fa-key"></i> Update Kata Sandi</button>
                        </form>
                    </div>

                    <div class="card" style="flex: 2.5;">
                        <h3 class="mb-4 d-flex justify-between align-center">
                            <span><i class="fa-solid fa-id-card"></i> Daftar Akun Pegawai</span>
                        </h3>
                        <div class="table-wrapper">
                            <table class="table-modern">
                                <thead>
                                    <tr>
                                        <th>Pengguna</th>
                                        <th>Email</th>
                                        <th>Role Akses</th>
                                        <th>Aksi</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach($users as $u): ?>
                                    <tr>
                                        <td>
                                            <div class="d-flex align-center gap-3">
                                                <img src="https://ui-avatars.com/api/?name=<?= urlencode($u['name']) ?>&background=random&color=fff" alt="Avatar" style="border-radius:50%; width:35px;">
                                                <span style="font-weight: 600; color:var(--text-dark);"><?= htmlspecialchars($u['name']) ?></span>
                                            </div>
                                        </td>
                                        <td><?= htmlspecialchars($u['email']) ?></td>
                                        <td>
                                            <?php if($u['role']=='admin'): ?>
                                                <span class="badge badge-danger"><i class="fa-solid fa-shield"></i> Admin</span>
                                            <?php elseif($u['role']=='owner'): ?>
                                                <span class="badge badge-warning"><i class="fa-solid fa-chart-pie"></i> Owner</span>
                                            <?php else: ?>
                                                <span class="badge badge-success"><i class="fa-solid fa-cash-register"></i> Kasir</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if($u['id'] != $_SESSION['user_id']): ?>
                                            <form action="users.php" method="POST" style="display:inline;" onsubmit="return confirm('Cabut akses user ini?');">
                                                <input type="hidden" name="action" value="delete">
                                                <input type="hidden" name="id" value="<?= $u['id'] ?>">
                                                <button type="submit" class="btn-danger"><i class="fa-solid fa-user-xmark"></i> Cabut Akses</button>
                                            </form>
                                            <?php else: ?>
                                                <span style="color:var(--text-muted); font-size:0.8rem;">(Anda Sendiri)</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
