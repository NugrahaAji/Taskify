<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

include '../backend/db.php';

$id_user = $_SESSION['id_user'];
$task_id = $_GET['id'] ?? null;

if ($task_id === null) {
    echo "Task ID is required.";
    exit();
}

// Ambil data user
$stmt = $conn->prepare("SELECT username, email, bio, profile_picture, cover_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $bio, $profile_picture, $cover_picture);
$stmt->fetch();
$stmt->close();

// Prepare and execute query to fetch task details for the logged-in user
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id_task = ? AND id_user = ?");
$stmt->bind_param("ii", $task_id, $id_user);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Task not found or you do not have permission to view this task.";
    exit();
}

$task = $result->fetch_assoc();
$stmt->close();
?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Taskify | Task Detail</title>
    <link rel="icon" href="../src/asset/icon/Taskify.ico" type="image/x-icon" />
    <link href="../src/style.css" rel="stylesheet" />
</head>
<body class="bg-primary">
    <nav class="h-[100px] bg-accent">
        <div class="container mx-auto flex justify-between">
            <div class="h-[100px] flex items-center">
                <img src="../src/asset/icon/logo-white.svg" alt="logo" />
                <ul class="ml-[152px] flex font-mont text-[18px] font-light tracking-[-1px] text-primary gap-9 h-[100px]">
                    <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="dashboard.php">Dashboard</a></li>
                    <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="workspace.php">Workspace</a></li>
                    <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="dashboard.php">Docs</a></li>
                </ul>
            </div>
            <div class="flex items-center">
                <ul class="flex gap-5 hh-[100px] items-center">
                    <li><button class="flex items-center">
                        <div class="h-[48px] w-[48px] rounded-full overflow-hidden">
                            <?php if (!empty($profile_picture)): ?>
                                <img src="../<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture" class="h-[48px] w-[48px] object-cover" />
                            <?php else: ?>
                                <img src="../src/asset/img/profile.svg" alt="Default Profile Picture" class="h-[48px] w-[48px] object-cover" />
                            <?php endif; ?>
                        </div>
                    </button></li>
                    <li><a href="setting.php"><img src="../src/asset/icon/setting.svg" alt="" /></a></li>
                    <li><button class="flex items-center"><img src="../src/asset/icon/notif.svg" alt="" /></button></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-[86px] max-w-3xl bg-white p-6 rounded shadow">
        <h1 class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent mb-6">Task Detail</h1>

        <div class="mb-4">
            <h2 class="font-mont font-semibold text-xl mb-2"><?php echo htmlspecialchars($task['task_name']); ?></h2>
            <p><strong>Category:</strong> <?php echo htmlspecialchars($task['category']); ?></p>
            <p><strong>Deadline:</strong> <?php echo htmlspecialchars($task['deadline']); ?></p>
            <p><strong>Subject:</strong> <?php echo htmlspecialchars($task['subject']); ?></p>
            <p><strong>Description:</strong></p>
            <p class="whitespace-pre-wrap border p-3 rounded bg-gray-100"><?php echo htmlspecialchars($task['description']); ?></p>
            <p><strong>Cover Color:</strong> <?php echo htmlspecialchars($task['cover_color']); ?></p>
            <p><strong>Status:</strong> <?php echo htmlspecialchars($task['status']); ?></p>
        </div>

        <a href="dashboard.php" class="inline-block bg-accent text-primary px-4 py-2 rounded font-semibold hover:bg-shade transition">Back to Dashboard</a>
    </main>
</body>
</html>
