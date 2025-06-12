<?php
require_once '../backend/db.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $email = trim($_POST["email"]);
    $password = $_POST["password"];
    $confirm_password = $_POST["confirm_password"];

    // Basic validation
    if (empty($email) || empty($password) || empty($confirm_password)) {
        echo "<script>alert('All fields are required');</script>";
        exit;
    }

    if ($password !== $confirm_password) {
        echo "<script>alert('Passwords do not match');</script>";
        exit;
    }

    // Check if email already exists
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $stmt->store_result();

    if ($stmt->num_rows > 0) {
        echo "<script>alert('Email is already registered');</script>";
        $stmt->close();
        exit;
    }

    $stmt->close();

    // Hash the password
    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
    $username = explode("@", $email)[0]; // Generate username from email

    // Insert user into DB
    $stmt = $conn->prepare("INSERT INTO users (username, email, password, created_at) VALUES (?, ?, ?, NOW())");
    $stmt->bind_param("sss", $username, $email, $hashed_password);

    if ($stmt->execute()) {
        echo "<script>alert('Account created successfully'); window.location.href='login.php';</script>";
    } else {
        echo "<script>alert('Something went wrong while registering.');</script>";
    }

    $stmt->close();
    $conn->close();
}
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../src/asset/icon/Taskify.ico" type="image/x-icon">
    <link href="../src/style.css" rel="stylesheet" />
    <title>Taskify | Sign up</title>
  </head>
  <body class="bg-accent">
    <div class="container mx-auto grow grid grid-cols-2 my-14">
      <div class="flex flex-col h-[920px]">
        <div>
          <img src="../src/asset/icon/logo-white.svg" alt="logo" />
        </div>
        <div class="flex-grow flex items-center justify-center">
          <div class="flip-slideshow">
            <img class="h-[574px]" id="flipImage" src="../src/asset/icon/calendar.svg" alt="icon" />
          </div>
        </div>
      </div>

      <div class="bg-primary rounded-[20px] h-[920px] items-end grid">
        <form class="px-8 flex-col flex w-full" method="POST" action="signup.php">
          <div class="block">
            <h1 class="font-mont text-shade text-8xl font-medium tracking-[-14.4px]">Sign Up</h1>
          </div>

          <div class="grid grid-cols-2 gap-x-[60px] gap-y-4 mt-4">
            <div>
              <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Email</label>
              <input type="email" name="email" required class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]" />
            </div>

            <div>
              <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Password</label>
              <input type="password" name="password" required class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]" />
            </div>

            <div></div>

            <div>
              <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Confirm your password</label>
              <input type="password" name="confirm_password" required class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]" />
            </div>

            <div></div>

            <div class="text-right">
              <a href="login.php" class="font-dm text-[16px] italic underline hover:italic tracking-[-1.12px] font-[300px]">Already have an account?</a>
            </div>
          </div>

          <span class="flex px-[52px] justify-end pb-[52px]">
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
          imgElement.style.transform = "rotateY(360deg)";
          imgElement.style.transform = "rotateY(0deg)";
        }, 500);
      }, 4000);
    </script>
  </body>
</html>