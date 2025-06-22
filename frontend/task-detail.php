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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Handle form submission to update task
    $task_name = $_POST['task_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $description = $_POST['description'] ?? '';
    $cover_color = $_POST['cover_color'] ?? '';
    $status = $_POST['status'] ?? '';

    // Basic validation (can be extended)
    if (empty($task_name) || empty($category) || empty($deadline) || empty($subject) || empty($status)) {
        $error_message = "Please fill in all required fields.";
    } else {
        $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, category = ?, deadline = ?, subject = ?, description = ?, cover_color = ?, status = ? WHERE id_task = ? AND id_user = ?");
        $stmt->bind_param("sssssssii", $task_name, $category, $deadline, $subject, $description, $cover_color, $status, $task_id, $id_user);
        if ($stmt->execute()) {
            header("Location: dashboard.php?update=success");
            exit();
        } else {
            $error_message = "Failed to update task. Please try again.";
        }
        $stmt->close();
    }
}

// Fetch task details for the logged-in user
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
<?php include 'header.php'; ?>

<main class="container mx-auto mt-[86px] max-w-3xl bg-white p-6 rounded shadow">
    <h1 class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent mb-6">Task Detail</h1>

    <?php if (!empty($error_message)): ?>
        <div class="mb-4 p-3 bg-red-200 text-red-800 rounded"><?php echo htmlspecialchars($error_message); ?></div>
    <?php endif; ?>

    <form method="POST" action="task-detail.php?id=<?php echo htmlspecialchars($task_id); ?>">
        <div class="mb-4">
            <label for="task_name" class="block font-semibold mb-1">Task Name</label>
            <input type="text" id="task_name" name="task_name" value="<?php echo htmlspecialchars($task['task_name']); ?>" required class="w-full border rounded p-2" />
        </div>

        <div class="mb-4">
            <label for="category" class="block font-semibold mb-1">Category</label>
            <select id="category" name="category" required class="w-full border rounded p-2">
                <?php
                $categories = ['Exam', 'Excercise', 'Presentation', 'Project'];
                foreach ($categories as $cat) {
                    $selected = ($task['category'] === $cat) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($cat) . "\" $selected>" . htmlspecialchars($cat) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="mb-4">
            <label for="deadline" class="block font-semibold mb-1">Deadline</label>
            <input type="datetime-local" id="deadline" name="deadline" value="<?php echo date('Y-m-d\TH:i', strtotime($task['deadline'])); ?>" required class="w-full border rounded p-2" />
        </div>

        <div class="mb-4">
            <label for="subject" class="block font-semibold mb-1">Subject</label>
            <input type="text" id="subject" name="subject" value="<?php echo htmlspecialchars($task['subject']); ?>" required class="w-full border rounded p-2" />
        </div>

        <div class="mb-4">
            <label for="description" class="block font-semibold mb-1">Description</label>
            <textarea id="description" name="description" class="w-full border rounded p-2" rows="4"><?php echo htmlspecialchars($task['description']); ?></textarea>
        </div>

        <div class="mb-4">
            <label for="cover_color" class="block font-semibold mb-1">Cover Color</label>
            <input type="text" id="cover_color" name="cover_color" value="<?php echo htmlspecialchars($task['cover_color']); ?>" class="w-full border rounded p-2" />
        </div>

        <div class="mb-4">
            <label for="status" class="block font-semibold mb-1">Status</label>
            <select id="status" name="status" required class="w-full border rounded p-2">
                <?php
                $statuses = ['pending' => 'Pending', 'in_progress' => 'In Progress', 'completed' => 'Completed'];
                foreach ($statuses as $value => $label) {
                    $selected = ($task['status'] === $value) ? 'selected' : '';
                    echo "<option value=\"" . htmlspecialchars($value) . "\" $selected>" . htmlspecialchars($label) . "</option>";
                }
                ?>
            </select>
        </div>

        <div class="flex space-x-4">
            <button type="submit" class="bg-accent text-primary px-4 py-2 rounded font-semibold hover:bg-shade transition">Save</button>
            <a href="dashboard.php" class="inline-block bg-gray-300 text-gray-700 px-4 py-2 rounded font-semibold hover:bg-gray-400 transition">Back to Dashboard</a>
        </div>
    </form>
</main>
<?php include 'footer.php'; ?>
</body>
</html>
