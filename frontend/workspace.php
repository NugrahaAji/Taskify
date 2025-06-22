<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

include '../backend/db.php';

$id_user = $_SESSION['id_user'];

// Handle adding new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task_name = $_POST['task_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $deadline = $_POST['deadline'] ?? null;
    $subject = $_POST['subject'] ?? '';
    $description = $_POST['description'] ?? '';
    $cover_color = $_POST['cover_color'] ?? '';
    $status = $_POST['status'] ?? 'pending';

    // Insert new task
    $stmt = $conn->prepare("INSERT INTO tasks (id_user, task_name, category, deadline, subject, description, cover_color, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $id_user, $task_name, $category, $deadline, $subject, $description, $cover_color, $status);
    $stmt->execute();
    $stmt->close();
}

// Handle adding collaborator
$collab_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_collaborator'])) {
    $collab_email = $_POST['collab_email'] ?? '';

    // Find user by email
    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    $stmt->bind_param("s", $collab_email);
    $stmt->execute();
    $result = $stmt->get_result();
    if ($result->num_rows === 1) {
        $collab_user = $result->fetch_assoc();
        $collab_user_id = $collab_user['id'];

        // For simplicity, create a workspace entry linking current user and collaborator
        // Assuming workspace table has id_workspace (auto increment), id_user, id_task, nama_workspace
        // Here we create a workspace named "Default Workspace" if not exists and add collaborator

        // Check if workspace exists for user
        $stmt = $conn->prepare("SELECT id_workspace FROM workspace WHERE id_user = ? LIMIT 1");
        $stmt->bind_param("i", $id_user);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 0) {
            // For now, do not create workspace to avoid id_task error
            // User must have workspace created elsewhere or manually
            $workspace_id = null;
        } else {
            $workspace = $result->fetch_assoc();
            $workspace_id = $workspace['id_workspace'];
        }

        if ($workspace_id !== null) {
            // Add collaborator to collaborators table
            $stmt = $conn->prepare("INSERT INTO collaborators (id_workspace, id_user) VALUES (?, ?)");
            $stmt->bind_param("ii", $workspace_id, $collab_user_id);
            if ($stmt->execute()) {
                $collab_message = "Collaborator added successfully.";
            } else {
                $collab_message = "Failed to add collaborator or already exists.";
            }
            $stmt->close();
        } else {
            $collab_message = "No workspace found for user. Cannot add collaborator.";
        }
    } else {
        $collab_message = "User with that email not found.";
    }
    $stmt->close();
}

// Fetch tasks for user
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id_user = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$tasks_result = $stmt->get_result();
$tasks = [];
while ($row = $tasks_result->fetch_assoc()) {
    $tasks[] = $row;
}
$stmt->close();

// Fetch collaborators for user's workspace
$collaborators = [];
$stmt = $conn->prepare("SELECT c.id_collab, u.username, u.email FROM collaborators c JOIN users u ON c.id_user = u.id WHERE c.id_workspace IN (SELECT id_workspace FROM workspace WHERE id_user = ?)");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$collab_result = $stmt->get_result();
while ($row = $collab_result->fetch_assoc()) {
    $collaborators[] = $row;
}
$stmt->close();

?>

<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Taskify | Workspace</title>
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
                    <li class="flex items-center border-b-4 border-primary"><a href="workspace.php">Workspace</a></li>
                    <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="setting.php">Setting</a></li>
                </ul>
            </div>
            <div class="flex items-center">
                <ul class="flex gap-5 hh-[100px] items-center">
                    <li><button class="flex items-center"><img src="../src/asset/icon/profile.svg" alt="" /></button></li>
                    <li><a href="setting.php"><img src="../src/asset/icon/setting.svg" alt="" /></a></li>
                    <li><button class="flex items-center"><img src="../src/asset/icon/notif.svg" alt="" /></button></li>
                </ul>
            </div>
        </div>
    </nav>

    <main class="container mx-auto mt-[86px]">
        <h1 class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent mb-6">Workspace</h1>

        <section class="mb-10">
            <h2 class="font-mont font-semibold text-xl mb-4">Add New Task</h2>
            <form method="POST" action="workspace.php" class="space-y-4 max-w-lg">
                <input type="hidden" name="add_task" value="1" />
                <div>
                    <label class="block font-mont text-accent mb-1" for="task_name">Task Name</label>
                    <input type="text" id="task_name" name="task_name" required class="w-full p-2 rounded border" />
                </div>
                <div>
                    <label class="block font-mont text-accent mb-1" for="category">Category</label>
                    <select id="category" name="category" required class="w-full p-2 rounded border">
                        <option value="">Select category</option>
                        <option value="Exam">Exam</option>
                        <option value="Excercise">Excercise</option>
                        <option value="Presentation">Presentation</option>
                        <option value="Project">Project</option>
                    </select>
                </div>
                <div>
                    <label class="block font-mont text-accent mb-1" for="deadline">Deadline</label>
                    <input type="datetime-local" id="deadline" name="deadline" class="w-full p-2 rounded border" />
                </div>
                <div>
                    <label class="block font-mont text-accent mb-1" for="subject">Subject</label>
                    <input type="text" id="subject" name="subject" class="w-full p-2 rounded border" />
                </div>
                <div>
                    <label class="block font-mont text-accent mb-1" for="description">Description</label>
                    <textarea id="description" name="description" rows="3" class="w-full p-2 rounded border"></textarea>
                </div>
                <div>
                    <label class="block font-mont text-accent mb-1" for="cover_color">Cover Color (Tailwind classes)</label>
                    <input type="text" id="cover_color" name="cover_color" placeholder="e.g. from-blue-200 to-blue-100" class="w-full p-2 rounded border" />
                </div>
                <div>
                    <label class="block font-mont text-accent mb-1" for="status">Status</label>
                    <select id="status" name="status" class="w-full p-2 rounded border">
                        <option value="pending" selected>Pending</option>
                        <option value="in_progress">In Progress</option>
                        <option value="completed">Completed</option>
                    </select>
                </div>
                <button type="submit" class="bg-accent text-primary px-4 py-2 rounded font-semibold">Add Task</button>
            </form>
        </section>

        <section class="mb-10">
            <h2 class="font-mont font-semibold text-xl mb-4">Collaborators</h2>
            <?php if ($collab_message): ?>
                <p class="text-red-600 mb-4"><?php echo htmlspecialchars($collab_message); ?></p>
            <?php endif; ?>
            <form method="POST" action="workspace.php" class="max-w-lg space-y-4">
                <input type="hidden" name="add_collaborator" value="1" />
                <div>
                    <label class="block font-mont text-accent mb-1" for="collab_email">Collaborator Email</label>
                    <input type="email" id="collab_email" name="collab_email" required class="w-full p-2 rounded border" />
                </div>
                <button type="submit" class="bg-accent text-primary px-4 py-2 rounded font-semibold">Add Collaborator</button>
            </form>

            <h3 class="font-mont font-semibold mt-8 mb-4">Current Collaborators</h3>
            <?php if (count($collaborators) === 0): ?>
                <p class="text-accent font-mont">No collaborators added yet.</p>
            <?php else: ?>
                <ul class="list-disc list-inside font-mont text-accent">
                    <?php foreach ($collaborators as $collab): ?>
                        <li><?php echo htmlspecialchars($collab['username']) . ' (' . htmlspecialchars($collab['email']) . ')'; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>

        <section>
            <h2 class="font-mont font-semibold text-xl mb-4">Tasks in Workspace</h2>
            <?php if (count($tasks) === 0): ?>
                <p class="text-accent font-mont">No tasks added yet.</p>
            <?php else: ?>
                <ul class="list-disc list-inside font-mont text-accent">
                    <?php foreach ($tasks as $task): ?>
                        <li>
                            <strong><?php echo htmlspecialchars($task['task_name']); ?></strong>
                            (<?php echo htmlspecialchars($task['category']); ?>)
                            - Deadline: <?php echo htmlspecialchars($task['deadline']); ?>
                            - Status: <?php echo htmlspecialchars($task['status']); ?>
                        </li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>
        </section>
    </main>
</body>
</html>
