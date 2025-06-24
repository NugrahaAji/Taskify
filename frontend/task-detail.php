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

$error_message = '';
$success_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $task_name = $_POST['task_name'] ?? '';
    $category = $_POST['category'] ?? '';
    $deadline = $_POST['deadline'] ?? '';
    $subject = $_POST['subject'] ?? '';
    $description = $_POST['description'] ?? '';
    $cover_color = $_POST['cover_color'] ?? '';
    $status = $_POST['status'] ?? '';

    if (empty($task_name) || empty($category) || empty($deadline) || empty($subject) || empty($status)) {
        $error_message = "Please fill in all required fields.";
    } else {

        $stmt = $conn->prepare("UPDATE tasks SET task_name = ?, category = ?, deadline = ?, subject = ?, description = ?, cover_color = ?, status = ? WHERE id_task = ? AND id_user = ?");
        $stmt->bind_param("sssssssii", $task_name, $category, $deadline, $subject, $description, $cover_color, $status, $task_id, $id_user);

        if ($stmt->execute()) {
            // Arahkan kembali ke halaman yang sama untuk melihat perubahan
            header("Location: task-detail.php?id=" . $task_id . "&update=success");
            exit();
        } else {
            $error_message = "Failed to update task. Please try again.";
        }
        $stmt->close();
    }
}

$stmt = $conn->prepare("SELECT cover_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->bind_result($cover_picture);
$stmt->fetch();
$stmt->close();

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

if (isset($_GET['update']) && $_GET['update'] === 'success') {
    $success_message = "Task updated successfully!";
}

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

    <div>
        <?php if (!empty($cover_picture)): ?>
            <img src="../src/asset/img/<?= htmlspecialchars($cover_picture) ?>" alt="Cover Picture" class="w-full max-h-48 object-cover" />
        <?php else: ?>
            <img src="../src/asset/img/gradient-1.png" alt="Default Cover Picture" class="w-full max-h-48 object-cover" />
        <?php endif; ?>
    </div>

    <main class="container mx-auto mt-[40px]">
        <h1 class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent mb-6">Task detail</h1>

        <?php if (!empty($error_message)): ?>
            <div class="mb-4 p-3 bg-red-200 text-red-800 rounded"><?php echo htmlspecialchars($error_message); ?></div>
        <?php endif; ?>
        <?php if (!empty($success_message)): ?>
            <div class="mb-4 p-3 bg-green-200 text-green-800 rounded"><?php echo htmlspecialchars($success_message); ?></div>
        <?php endif; ?>

        <section class="mb-10">
            <form method="POST" action="task-detail.php?id=<?php echo htmlspecialchars($task_id); ?>" class="w-full">
                <div class="grid grid-cols-2 gap-x-10 space-y-4">

                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="task_name">Task name</label>
                        <input type="text" id="task_name" name="task_name" required class="w-full border-b border-black outline-none focus:ring-0 h-[41px] text-[16px] hover:bg-gray-100" value="<?php echo htmlspecialchars($task['task_name']); ?>" />
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required class="w-full border-b border-black outline-none focus:ring-0 h-[41px] text-[16px] hover:bg-gray-100" value="<?php echo htmlspecialchars($task['subject']); ?>" />
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="description">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full border-b border-black outline-none focus:ring-0 h-[41px] pt-2 text-[16px] hover:bg-gray-100"><?php echo htmlspecialchars($task['description']); ?></textarea>
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="deadline">Deadline</label>
                        <input type="datetime-local" id="deadline" name="deadline" class="w-full border-b border-black outline-none focus:ring-0 h-[41px] text-[16px] pr-2 hover:bg-gray-100" value="<?php echo htmlspecialchars($task['deadline']); ?>" />
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]">Category</label>
                        <div class="relative">
                            <button type="button" id="categorySelectButton" onclick="toggleCustomSelect('category')" class="w-full text-left py-2 pr-2 focus:outline-none flex items-center cursor-pointer hover:bg-gray-100 border-b border-black">
                                <span id="categorySelectValue" class="font-mont text-[16px] font-medium text-accent"><?php echo htmlspecialchars($task['category']); ?></span>
                                <img id="categorySelectArrow" src="../src/asset/icon/arrow-black.svg" alt="arrow" class="ml-auto transition-transform duration-200" />
                            </button>
                            <ul id="categorySelectDropdown" class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto bg-accent text-primary">
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('category', 'Exam')">Exam</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('category', 'Exercise')">Exercise</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('category', 'Presentation')">Presentation</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('category', 'Project')">Project</li>
                            </ul>
                            <input type="hidden" id="categorySelectInput" name="category" value="<?php echo htmlspecialchars($task['category']); ?>" />
                        </div>
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]">Status</label>
                        <div class="relative">
                            <button type="button" id="statusSelectButton" onclick="toggleCustomSelect('status')" class="w-full text-left py-2 pr-2 focus:outline-none flex items-center cursor-pointer hover:bg-gray-100 border-b border-black">
                                <span id="statusSelectValue" class="font-mont text-[16px] font-medium text-accent"><?php echo htmlspecialchars($task['status']); ?></span>
                                <img id="statusSelectArrow" src="../src/asset/icon/arrow-black.svg" alt="arrow" class="ml-auto transition-transform duration-200" />
                            </button>
                            <ul id="statusSelectDropdown" class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto bg-accent text-primary">
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('status', 'Pending')">Pending</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('status', 'Progres')">Progres</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('status', 'Completed')">Completed</li>
                            </ul>
                             <input type="hidden" id="statusSelectInput" name="status" value="<?php echo htmlspecialchars($task['status']); ?>" />
                        </div>
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]">Cover color</label>
                        <div class="relative">
                            <button type="button" id="coverColorSelectButton" onclick="toggleCustomSelect('coverColor')" class="w-full text-left py-2 pr-2 focus:outline-none flex items-center cursor-pointer hover:bg-gray-100 border-b border-black">
                                <span id="coverColorSelectValue" class="font-mont text-[16px] font-medium text-accent">
                                    <?php
                                        $color_map_display = ['from-bs to-be' => 'Blue', 'from-grs to-gre' => 'Gray', 'from-ps to-pe' => 'Purple', 'from-os to-oe' => 'Orange', 'from-gs to-ge' => 'Green'];
                                        echo $color_map_display[$task['cover_color']] ?? 'Gray';
                                    ?>
                                </span>
                                <img id="coverColorSelectArrow" src="../src/asset/icon/arrow-black.svg" alt="arrow" class="ml-auto transition-transform duration-200" />
                            </button>
                            <ul id="coverColorSelectDropdown" class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto bg-accent text-primary">
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Blue', 'from-bs to-be')">Blue</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Gray', 'from-grs to-gre')">Gray</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Purple', 'from-ps to-pe')">Purple</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Orange', 'from-os to-oe')">Orange</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Green', 'from-gs to-ge')">Green</li>
                            </ul>
                             <input type="hidden" id="coverColorSelectInput" name="cover_color" value="<?php echo htmlspecialchars($task['cover_color']); ?>" />
                        </div>
                    </div>
                </div>
                <div class="flex space-x-4 mt-6">
                    <button type="submit" class="bg-accent text-primary px-4 py-2 rounded font-semibold hover:bg-shade transition">Save Changes</button>
                    <a href="dashboard.php" class="inline-block text-accent px-4 py-2 rounded font-semibold hover:bg-gray-400 transition">Back to Dashboard</a>
                </div>
            </form>
        </section>
    </main>

<?php include 'footer.php'; ?>

<script>
const dropdownStates = {};

function toggleCustomSelect(idPrefix) {
    const dropdown = document.getElementById(idPrefix + 'SelectDropdown');
    const arrow = document.getElementById(idPrefix + 'SelectArrow');

    for (const key in dropdownStates) {
        if (key !== idPrefix && dropdownStates[key]) {
            document.getElementById(key + 'SelectDropdown').classList.add("hidden");
            document.getElementById(key + 'SelectArrow').classList.remove("rotate-180");
            dropdownStates[key] = false;
        }
    }

    const isOpen = !dropdown.classList.contains("hidden");
    if (isOpen) {
        dropdown.classList.add("hidden");
        arrow.classList.remove("rotate-180");
        dropdownStates[idPrefix] = false;
    } else {
        dropdown.classList.remove("hidden");
        arrow.classList.add("rotate-180");
        dropdownStates[idPrefix] = true;
    }
}

function selectOption(idPrefix, displayText, actualValue = displayText) {
    document.getElementById(idPrefix + 'SelectValue').textContent = displayText;
    document.getElementById(idPrefix + 'SelectInput').value = actualValue;
    toggleCustomSelect(idPrefix);
}

document.addEventListener('click', function(event) {
    let clickedOutsideAnyDropdown = true;
    for (const key in dropdownStates) {
        const dropdown = document.getElementById(key + 'SelectDropdown');
        const button = document.getElementById(key + 'SelectButton');
        if ((button && button.contains(event.target)) || (dropdown && dropdown.contains(event.target))) {
            clickedOutsideAnyDropdown = false;
            break;
        }
    }

    if (clickedOutsideAnyDropdown) {
        for (const key in dropdownStates) {
            const dropdown = document.getElementById(key + 'SelectDropdown');
            if (dropdown && !dropdown.classList.contains("hidden")) {
                dropdown.classList.add("hidden");
                document.getElementById(key + 'SelectArrow').classList.remove("rotate-180");
                dropdownStates[key] = false;
            }
        }
    }
});
</script>
</body>
</html>
