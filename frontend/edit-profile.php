<?php
session_start();
require_once '../backend/db.php';

// Check if user is logged in
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

$user_id = $_SESSION['id_user'];

// Initialize variables for form fields and messages
$username = "";
$email = "";
$bio = "";
$profile_picture = "";
$cover_picture = "";
$success_message = "";
$error_message = "";

// Fetch current user data
$stmt = $conn->prepare("SELECT username, email, bio, profile_picture, cover_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $bio, $profile_picture, $cover_picture);
$stmt->fetch();
$stmt->close();

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $new_username = trim($_POST["username"] ?? "");
    $new_email = trim($_POST["email"] ?? "");
    $new_bio = trim($_POST["bio"] ?? "");
    $new_password = $_POST["password"] ?? "";

    // Handle profile picture file upload
    if (isset($_FILES['profile_picture_file']) && $_FILES['profile_picture_file']['error'] === UPLOAD_ERR_OK) {
        $upload_dir = '../src/asset/img/uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
        }
        $tmp_name = $_FILES['profile_picture_file']['tmp_name'];
        $filename = basename($_FILES['profile_picture_file']['name']);
        $target_file = $upload_dir . $filename;
        if (move_uploaded_file($tmp_name, $target_file)) {
            $new_profile_picture = 'src/asset/img/uploads/' . $filename;
        } else {
            $new_profile_picture = $profile_picture;
        }
    } else {
        $new_profile_picture = $profile_picture;
    }

    // Handle cover picture selection (template background)
    $new_cover_picture = $_POST['cover_picture_template'] ?? $cover_picture;

    // Convert empty string cover_picture to null for database
    if ($new_cover_picture === '') {
        $new_cover_picture = null;
    }

    // Basic validation
    if (empty($new_username) || empty($new_email)) {
        $error_message = "Username and email cannot be empty.";
    } else {
        // Check if email is changed and already exists
        if ($new_email !== $email) {
            $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
            $stmt->bind_param("si", $new_email, $user_id);
            $stmt->execute();
            $stmt->store_result();
            if ($stmt->num_rows > 0) {
                $error_message = "Email is already registered by another user.";
            }
            $stmt->close();
        }
    }

    if (empty($error_message)) {
        // If password is provided, hash it, else keep old password
        if (!empty($new_password)) {
            $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, bio = ?, password = ?, profile_picture = ?, cover_picture = ? WHERE id = ?");
            $stmt->bind_param("ssssssi", $new_username, $new_email, $new_bio, $hashed_password, $new_profile_picture, $new_cover_picture, $user_id);
        } else {
            $stmt = $conn->prepare("UPDATE users SET username = ?, email = ?, bio = ?, profile_picture = ?, cover_picture = ? WHERE id = ?");
            $stmt->bind_param("sssssi", $new_username, $new_email, $new_bio, $new_profile_picture, $new_cover_picture, $user_id);
        }

        if ($stmt->execute()) {
            $success_message = "Profile updated successfully.";
            // Update variables to reflect changes
            $username = $new_username;
            $email = $new_email;
            $bio = $new_bio;
            $profile_picture = $new_profile_picture;
            $cover_picture = $new_cover_picture;
            header("Location: setting.php");
            exit();
        } else {
            $error_message = "Failed to update profile.";
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
    <link
      href="../src/style.css"
      rel="stylesheet"
    />
    <title>Taskify | Edit Profile</title>
  </head>
  <body class="bg-primary scrollbar">
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
            <form method="POST" action="">
                <div class="w-full space-x-6 flex -mt-[57px] ">
                    <div class="flex flex-row ">
                        <div>
                            <img src="../src/asset/img/profile.svg" alt="">
                        </div>
                    </div>
                    <div class="flex mt-[52px] w-full justify-between items-center">
                        <div class="">
                        <h1 class="font-mont text-[32px] font-semibold tracking-[-2.88px] text-accent"><?= htmlspecialchars($username ?? '') ?></h1>
                        <h1 class="font-mont text-[16px] font-normal tracking-[-1.28px] text-inactive -mt-2"><?= htmlspecialchars($email ?? '') ?></h1>
                        </div>
                    </div>
                </div>
                <div class="mt-12">
                    <div class="justify-between flex items-center">
                        <h1 class="font-mont text-2xl text-accent font-semibold tracking-[-1.92px]">Edit account info</h1>
                        <a href="setting.php" class="cursor-pointer hover:bg-slate-200 rounded-full">
                            <img src="../src/asset/icon/cancel.svg" alt="cancel">
                        </a>
                    </div>
                <div class="grid grid-cols-2 gap-x-[60px] gap-y-[18px] mt-4 pb-[42px]">
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Username</label>
                        <input
                          type="text"
                          name="username"
                          value="<?= htmlspecialchars($username ?? '') ?>"
                          class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]"
                          required
                        />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Bio</label>
                        <input
                          type="text"
                          name="bio"
                          value="<?= htmlspecialchars($bio ?? '') ?>"
                          class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]"
                        />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Email</label>
                        <input
                          type="email"
                          name="email"
                          value="<?= htmlspecialchars($email ?? '') ?>"
                          class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]"
                          required
                        />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Password</label>
                        <input
                          type="password"
                          name="password"
                          placeholder="Leave blank to keep current password"
                          class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]"
                        />
                    </div>
                    <div>
                        <label
                          class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-light"
                        >
                          Profile picture
                        </label>
                        <input
                          type="file"
                          name="profile_picture_file"
                          accept="image/*"
                          class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]"
                        />
                        <?php if (!empty($profile_picture)): ?>
                          <img src="../<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture" class="max-h-24 mt-2" />
                        <?php endif; ?>
                      </div>
                      <div>
                        <label
                          class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-light"
                        >
                          Cover picture template
                        </label>
                        <select name="cover_picture_template" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]">
                          <option value="" <?= empty($cover_picture) ? 'selected' : '' ?>>Default</option>
                          <option value="gradient-2.png" <?= $cover_picture === 'gradient-2.png' ? 'selected' : '' ?>>Gradient 2</option>
                          <option value="gradient-3.png" <?= $cover_picture === 'gradient-3.png' ? 'selected' : '' ?>>Gradient 3</option>
                          <option value="gradient-4.png" <?= $cover_picture === 'gradient-4.png' ? 'selected' : '' ?>>Gradient 4</option>
                          <option value="gradient-5.png" <?= $cover_picture === 'gradient-5.png' ? 'selected' : '' ?>>Gradient 5</option>
                        </select>
                        <?php if (!empty($cover_picture) && $cover_picture !== ''): ?>
                          <img src="../src/asset/img/<?= htmlspecialchars($cover_picture) ?>" alt="Cover Picture" class="max-h-24 mt-2" />
                        <?php endif; ?>
                      </div>
                </div>
                <span class="flex justify-end">
                    <button type="submit" class="group bg-accent rounded-full h-20 w-20 flex items-center justify-center transition-transform duration-200 hover:scale-140">
                        <img src="../src/asset/icon/right-arrow.svg" alt="arrow" class="w-9 h-[46px]" />
                    </button>
                </span>
                </div>
            </form>
        </div>

        <script>
            const uploadInput = document.getElementById('upload');
            const filenameLabel = document.getElementById('filename');
            const imagePreview = document.getElementById('image-preview');

            // Check if the event listener has been added before
            let isEventListenerAdded = false;

            uploadInput.addEventListener('change', (event) => {
                const file = event.target.files[0];

                if (file) {
                filenameLabel.textContent = file.name;

                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.innerHTML =
                    `<img src="${e.target.result}" class="max-h-48 rounded-lg mx-auto" alt="Image preview" />`;
                    // Add event listener for image preview only once
                    if (!isEventListenerAdded) {
                    imagePreview.addEventListener('click', () => {
                        uploadInput.click();
                    });

                    isEventListenerAdded = true;
                    }
                };
                reader.readAsDataURL(file);
                } else {
                filenameLabel.textContent = '';
                imagePreview.innerHTML =
                    `<img class="h-full" src="../src/asset/img/profile.svg" alt="gradient">`;

                // Remove the event listener when there's no image
                imagePreview.removeEventListener('click', () => {
                    uploadInput.click();
                });

                isEventListenerAdded = false;
                }
            });

            uploadInput.addEventListener('click', (event) => {
                event.stopPropagation();
            });

            const selectButton = document.getElementById("customSelectButton");
            const dropdown = document.getElementById("customSelectDropdown");
            const previewImage = document.getElementById("customSelectPreview");
            const hiddenInput = document.getElementById("customSelectInput");

            function toggleCustomSelect() {
                dropdown.classList.toggle("hidden");
            }

            function selectOption(imageSrc) {
                previewImage.src = imageSrc;
                hiddenInput.value = imageSrc;
                dropdown.classList.add("hidden");
            }

            // Close dropdown if clicked outside
            document.addEventListener("click", function (event) {
                if (!selectButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add("hidden");
                }
            });
        </script>
  </body>
</html>
