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
    <?php include 'header.php'; ?>
    <div>
        <?php if (!empty($cover_picture) && $cover_picture !== ''): ?>
            <img src="../src/asset/img/<?= htmlspecialchars($cover_picture) ?>" alt="Cover Picture" class="w-full max-h-48 object-cover" />
        <?php else: ?>
            <img src="../src/asset/img/gradient-1.png" alt="Default Cover Picture" class="w-full max-h-48 object-cover" />
        <?php endif; ?>
    </div>
    <div class="max-w-[1120px] mx-auto">
        <form method="POST" action="" enctype="multipart/form-data">
            <div class="w-full space-x-6 flex -mt-[57px] ">
                <div class="flex flex-row ">
                    <div class="h-[162px] w-[162px] rounded-full overflow-hidden">
                        <?php if (!empty($profile_picture)): ?>
                            <img src="../<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture" class="h-[162px]" />
                        <?php else: ?>
                            <img src="../src/asset/img/profile.svg" alt="Default Profile Picture" class="h-[162px]" />
                        <?php endif; ?>
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
                            class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]"
                            required
                        />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Bio</label>
                        <input
                            type="text"
                            name="bio"
                            value="<?= htmlspecialchars($bio ?? '') ?>"
                            class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]"
                        />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Email</label>
                        <input
                            type="email"
                            name="email"
                            value="<?= htmlspecialchars($email ?? '') ?>"
                            class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]"
                            required
                        />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Password</label>
                        <input
                            type="password"
                            name="password"
                            placeholder="Leave blank to keep current password"
                            class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]"
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
                            class="w-full border-b border-black outline-none focus:ring-0 h-8 text-[18px]"
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
                        <div class="relative inline-block text-left w-full">
                            <div class="relative w-full">
                                <div id="customSelectButton"
                                    onclick="toggleCustomSelect()"
                                    class="w-full h-20 rounded-md overflow-hidden cursor-pointer relative">
                                    <img id="customSelectPreview"
                                        src="../src/asset/img/gradient-1.png"
                                        alt="Selected"
                                        class="w-full h-full object-cover" />
                                </div>

                                <ul id="customSelectDropdown"
                                    class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-48 overflow-y-scroll bg-accent p-2 space-y-2 hide-scrollbar">
                                    <li onclick="selectOption('gradient-1.png')"
                                        class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                        <img src="../src/asset/img/gradient-1.png" class="w-full h-20 object-cover" alt="gradient 1" />
                                    </li>
                                    <li onclick="selectOption('gradient-2.png')"
                                        class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                        <img src="../src/asset/img/gradient-2.png" class="w-full h-20 object-cover" alt="gradient 2" />
                                    </li>
                                    <li onclick="selectOption('gradient-3.png')"
                                        class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                        <img src="../src/asset/img/gradient-3.png" class="w-full h-20 object-cover" alt="gradient 3" />
                                    </li>
                                    <li onclick="selectOption('gradient-4.png')"
                                        class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                        <img src="../src/asset/img/gradient-4.png" class="w-full h-20 object-cover" alt="gradient 4" />
                                    </li>
                                    <li onclick="selectOption('gradient-5.png')"
                                        class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                        <img src="../src/asset/img/gradient-5.png" class="w-full h-20 object-cover" alt="gradient 5" />
                                    </li>
                                </ul>

                                <input type="hidden" id="customSelectInput" name="cover_picture_template" value="<?= htmlspecialchars($cover_picture ?? 'gradient-1.png') ?>" />
                            </div>
                        </div>
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
        const uploadInput = document.querySelector('input[name="profile_picture_file"]');
        const filenameLabel = document.getElementById('filename'); // This ID doesn't exist in your HTML, consider removing or adding
        const imagePreview = document.getElementById('image-preview'); // This ID doesn't exist in your HTML, consider removing or adding

        // Function to update the profile picture preview
        function updateProfilePicturePreview(file) {
            const profilePicElement = document.querySelector('img[alt="Profile Picture"]');
            if (file && profilePicElement) {
                const reader = new FileReader();
                reader.onload = (e) => {
                    profilePicElement.src = e.target.result;
                };
                reader.readAsDataURL(file);
            }
        }

        // Event listener for profile picture file input change
        if (uploadInput) {
            uploadInput.addEventListener('change', (event) => {
                const file = event.target.files[0];
                updateProfilePicturePreview(file);
            });
        }

        const selectButton = document.getElementById("customSelectButton");
        const dropdown = document.getElementById("customSelectDropdown");
        const previewImage = document.getElementById("customSelectPreview");
        const hiddenInput = document.getElementById("customSelectInput");

        // Initialize the preview image based on the current cover_picture value
        document.addEventListener('DOMContentLoaded', () => {
            if (hiddenInput.value && hiddenInput.value !== 'null') { // Check if a value exists and is not 'null'
                previewImage.src = `../src/asset/img/${hiddenInput.value.split('/').pop()}`; // Extract filename from path
            } else {
                previewImage.src = '../src/asset/img/gradient-1.png'; // Default if null or empty
            }
        });


        function toggleCustomSelect() {
            dropdown.classList.toggle("hidden");
        }

        function selectOption(imageName) {
            previewImage.src = `../src/asset/img/${imageName}`;
            hiddenInput.value = imageName;
            dropdown.classList.add("hidden");
        }

        // Close dropdown if clicked outside
        document.addEventListener("click", function (event) {
            if (selectButton && dropdown && !selectButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add("hidden");
            }
        });
    </script>
</body>
</html>
