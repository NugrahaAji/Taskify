<?php
session_start();
require_once '../backend/db.php';

// Redirect if already logged in
if (isset($_SESSION['id_user'])) {
    header("Location: dashboard.php");
    exit();
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi!";
    } else {
        $sql = "SELECT * FROM users WHERE email = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($user = $result->fetch_assoc()) {
            if (password_verify($password, $user['password'])) {
                $_SESSION['id_user'] = $user['id']; // assuming 'id' column
                $_SESSION['email'] = $user['email'];
                header("Location: dashboard.php");
                exit();
            } else {
                $error = "Password salah!";
            }
        } else {
            $error = "Email tidak ditemukan!";
        }

        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../src/asset/icon/Taskify.ico" type="image/x-icon">
    <link href="../src/style.css" rel="stylesheet" />
    <title>Taskify | Login</title>
  </head>
  <body class="bg-accent">
    <div class="container mx-auto grow grid grid-cols-2 my-14">
        <div class="flex flex-col h-[920px] flex-grow">
            <div>
                <img src="../src/asset/icon/logo-white.svg" alt="logo" />
            </div>
            <div class="flex-grow flex items-center justify-center">
                <div class="flip-slideshow">
                    <img class="h-[400px]" id="flipImage" src="../src/asset/icon/calendar.svg" alt="icon" />
                </div>
            </div>
        </div>

        <div class="bg-primary rounded-[20px] h-full flex items-center">
            <form class="px-8 flex flex-col w-full" method="POST" action="login.php">
                <div class="block">
                    <h1 class="font-mont text-shade text-8xl font-medium tracking-[-1.4px]">Login</h1>
                </div>

                <?php if (!empty($error)): ?>
                    <p class="text-red-500 mb-4 mt-2 text-center"><?= htmlspecialchars($error) ?></p>
                <?php endif; ?>

                <div class="grid grid-cols-2 gap-x-[60px] gap-y-4 mt-4">
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1">Email</label>
                        <input type="email" name="email" required class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]" />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1">Password</label>
                        <input type="password" name="password" required class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]" />
                    </div>
                    <div></div>
                    <div class="text-right">
                        <a href="./signup.php" class="font-dm text-[16px] italic underline hover:decoration-amber-300 tracking-[-1.12px]">Create an account?</a>
                    </div>
                </div>
                <span class="flex px-5 justify-end pb-[32px] mt-5">
                    <button type="submit" class="group bg-accent rounded-full h-20 w-20 flex items-center justify-center transition-transform duration-200 hover:scale-140">
                        <img src="../src/asset/icon/right-arrow.svg" alt="arrow" class="w-9 h-[46px]" />
                    </button>
                </span>
            </form>
        </div>
    </div>

    <script>
        const images = [
            "../src/asset/icon/calendar.svg",
            "../src/asset/icon/notes.svg",
            "../src/asset/icon/checklist.svg"
        ];

        let current = 0;
        const imgElement = document.getElementById("flipImage");

        setInterval(() => {
            imgElement.style.transform = "rotateY(90deg)";
            setTimeout(() => {
                current = (current + 1) % images.length;
                imgElement.src = images[current];
                imgElement.style.transform = "rotateY(0deg)";
            }, 500);
        }, 4000);
    </script>
  </body>
</html>
