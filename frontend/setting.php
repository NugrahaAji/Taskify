<?php
session_start();
require_once '../backend/db.php'; // koneksi ke database

// Cek apakah user sudah login
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id_user'];

// Ambil data user
$stmt = $conn->prepare("SELECT username, email, bio, profile_picture, cover_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $bio, $profile_picture, $cover_picture);
$stmt->fetch();
$stmt->close();

// Logout
if (isset($_POST['logout'])) {
    session_destroy();
    header("Location: login.php");
    exit();
}

// Delete account
if (isset($_POST['delete_account'])) {
    $stmt = $conn->prepare("DELETE FROM users WHERE id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $stmt->close();

    session_destroy();
    header("Location: register.php"); // arahkan ke halaman register setelah hapus akun
    exit();
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../src/asset/icon/Taskify.ico" type="image/x-icon">
    <link href="../src/style.css" rel="stylesheet" />
    <title>Taskify | Setting</title>
  </head>
  <body class="bg-primary">
    <nav class="h-[100px] bg-accent">
      <div class="container mx-auto flex justify-between">
        <div class="h-[100px] flex items-center">
          <img src="../src/asset/icon/logo-white.svg" alt="logo">
          <ul class="ml-[152px] flex font-mont text-[18px] font-light tracking-[-1px] text-primary gap-9 h-[100px]">
            <li class="flex items-center border-b-4 hover:border-shade border-accent"><a href="Dashboard.php">Dashboard</a></li>
            <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="">Workspace</a></li>
            <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="">Dashboard</a></li>
          </ul>
        </div>
        <div class="flex items-center">
          <ul class="flex gap-5 hh-[100px] items-center">
            <li><button class="flex items-center"><img src="../src/asset/icon/profile.svg" alt=""></button></li>
            <li><a href="setting.php"><img src="../src/asset/icon/setting-white.svg" alt=""></a></li>
            <li><button class="flex items-center"><img src="../src/asset/icon/notif.svg" alt=""></button></li>
          </ul>
        </div>
      </div>
    </nav>

    <div>
      <?php if (!empty($cover_picture) && $cover_picture !== ''): ?>
        <img src="../src/asset/img/<?= htmlspecialchars($cover_picture) ?>" alt="Cover Picture" class="w-full max-h-48 object-cover rounded-lg" />
      <?php else: ?>
        <img src="../src/asset/img/gradient.png" alt="Default Cover Picture" class="w-full max-h-48 object-cover rounded-lg" />
      <?php endif; ?>
    </div>

    <div class="max-w-[1120px] mx-auto">
      <div class="w-full space-x-6 flex -mt-[57px]">
          <div class="flex flex-row">
            <div>
              <?php if (!empty($profile_picture)): ?>
                <img src="../<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture" class="max-h-24 rounded-lg" />
              <?php else: ?>
                <img src="../src/asset/img/profile.svg" alt="Default Profile Picture" class="max-h-24 rounded-lg" />
              <?php endif; ?>
            </div>
          </div>
          <div class="flex mt-[52px] w-full items-center">
            <div>
              <h1 class="font-mont text-[32px] font-semibold tracking-[-2.88px] text-accent"><?= htmlspecialchars($username) ?></h1>
              <h1 class="font-mont text-[16px] font-normal tracking-[-1.28px] text-inactive -mt-2"><?= htmlspecialchars($email) ?></h1>
            </div>
          </div>
      </div>

      <div class="mt-12">
        <div class="flex items-center justify-between">
          <h1 class="font-mont text-2xl text-accent font-semibold tracking-[-1.92px]">Account info</h1>
          <a href="edit-profile.php" class="cursor-pointer">
            <img src="../src/asset/icon/edit.svg" alt="edit">
          </a>
        </div>

          <div class="grid grid-cols-2 gap-x-[60px] gap-y-[18px] mt-4">
            <div>
              <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Username</label>
              <input type="text" value="<?= htmlspecialchars($username) ?>" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]" disabled />
            </div>
            <div>
              <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Bio</label>
              <input type="text" value="<?= htmlspecialchars($bio ?? '') ?>" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]" disabled />
            </div>
            <div>
              <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Email</label>
              <input type="text" value="<?= htmlspecialchars($email) ?>" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]" disabled />
            </div>
            <div>
              <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Password</label>
              <input type="password" value="********" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]" disabled />
            </div>
          </div>

        <div class="space-y-3 mt-6">
          <form method="post">
            <button type="submit" name="logout" class="py-1 pr-2 flex cursor-pointer">
              <img src="../src/asset/icon/logout.svg" alt="">
              <span class="font-mont text-[18px] font-normal tracking-[-1.26px] text-warn">Log out</span>
            </button>
          </form>
          <form method="post" onsubmit="return confirm('Are you sure you want to delete your account? This action cannot be undone.');">
            <button type="submit" name="delete_account" class="py-1 pr-2 flex cursor-pointer">
              <img src="../src/asset/icon/delete.svg" alt="">
              <span class="font-mont text-[18px] font-normal tracking-[-1.26px] text-warn">Delete account</span>
            </button>
          </form>
        </div>
      </div>
    </div>
  </body>
</html>