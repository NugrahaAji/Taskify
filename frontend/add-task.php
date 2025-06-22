<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

include '../backend/db.php'; // Pastikan path ini benar

$id_user = $_SESSION['id_user'];

// Ambil data user
$stmt = $conn->prepare("SELECT username, email, bio, profile_picture, cover_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->bind_result($username, $email, $bio, $profile_picture, $cover_picture);
$stmt->fetch();
$stmt->close();

// Handle adding new task
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_task'])) {
    $task_name = $_POST['task_name'] ?? '';
    $category = $_POST['category'] ?? ''; // Mengambil dari name="category"
    $deadline = $_POST['deadline'] ?? null;
    $subject = $_POST['subject'] ?? '';
    $description = $_POST['description'] ?? '';
    $cover_color = $_POST['cover_color'] ?? ''; // Mengambil dari name="cover_color"
    $status = $_POST['status'] ?? 'pending'; // Mengambil dari name="status"

    // Map status to ENUM values in DB
    $status_map = [
        'pending' => 'pending',
        'In progress' => 'in_progress',
        'completed' => 'completed'
    ];
    if (isset($status_map[$status])) {
        $status = $status_map[$status];
    } else {
        $status = 'pending'; // default fallback
    }

    // Pastikan nilai default jika tidak dipilih dari dropdown (misal dari JavaScript)
    if (empty($category)) $category = 'General'; // Default value jika dropdown tidak dipilih
    if (empty($status)) $status = 'pending'; // Default value
    if (empty($cover_color)) $cover_color = 'from-grs to-gre'; // Default color

    // Insert new task
    $stmt = $conn->prepare("INSERT INTO tasks (id_user, task_name, category, deadline, subject, description, cover_color, status) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
    $stmt->bind_param("isssssss", $id_user, $task_name, $category, $deadline, $subject, $description, $cover_color, $status);
    $stmt->execute();
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
<?php include 'header.php'; ?>

    <main class="container mx-auto mt-[86px]">
        <h1 class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent mb-6">Add new task</h1>

        <section class="mb-10">
<form method="POST" action="add-task.php" class="w-full ">
                <div class="grid grid-cols-2 gap-x-10 space-y-4">
                    <input type="hidden" name="add_task" value="1" />
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="task_name">Task name</label>
                        <input type="text" id="task_name" name="task_name" required class="w-full border-b border-black outline-none focus:ring-0 h-[41px] text-[16px] hover:bg-gray-100 " />
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="subject">Subject</label>
                        <input type="text" id="subject" name="subject" required class="w-full border-b border-black outline-none focus:ring-0 h-[41px] text-[16px] hover:bg-gray-100 " />
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="description">Description</label>
                        <textarea id="description" name="description" rows="3" class="w-full border-b border-black outline-none focus:ring-0 h-[41px] pt-2 text-[16px] hover:bg-gray-100 "></textarea>
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="deadline">Deadline</label>
                        <input type="datetime-local" id="deadline" name="deadline" class="w-full border-b border-black outline-none focus:ring-0 h-[41px] text-[16px] pr-2 hover:bg-gray-100 " />
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="category_input">Category</label>
                        <div class="relative inline-block text-left w-full">
                            <div class="relative">
                                <button type="button" id="categorySelectButton" onclick="toggleCustomSelect('category')" class="w-full text-left py-2 pr-2 focus:outline-none flex items-center cursor-pointer hover:bg-gray-100 border-b border-black outline-none focus:ring-0">
                                    <span id="categorySelectValue" class="font-mont text-[16px] font-medium tracking-[-1.44px] text-accent">Select category</span>
                                    <img id="categorySelectArrow" src="../src/asset/icon/arrow-black.svg" alt="arrow" class="ml-auto transition-transform duration-200" />
                                </button>

                                <ul id="categorySelectDropdown" class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto text-[16px] tracking-[-1.12px] font-mont font-normal text-primary bg-accent">
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('category', 'Exam')">Exam</li>
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('category', 'Exercise')">Exercise</li>
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('category', 'Presentation')">Presentation</li>
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('category', 'Project')">Project</li>
                                </ul>

                                <input type="hidden" id="categorySelectInput" name="category" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="status_input">Status</label>
                        <div class="relative inline-block text-left w-full">
                            <div class="relative">
                                <button type="button" id="statusSelectButton" onclick="toggleCustomSelect('status')" class="w-full text-left py-2 pr-2 focus:outline-none flex items-center cursor-pointer hover:bg-gray-100 border-b border-black outline-none focus:ring-0">
                                    <span id="statusSelectValue" class="font-mont text-[16px] font-medium tracking-[-1.44px] text-accent">Select status</span>
                                    <img id="statusSelectArrow" src="../src/asset/icon/arrow-black.svg" alt="arrow" class="ml-auto transition-transform duration-200" />
                                </button>

                                <ul id="statusSelectDropdown" class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto text-[16px] tracking-[-1.12px] font-mont font-normal text-primary bg-accent">
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('status', 'pending')">Pending</li>
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('status', 'In progress')">In progress</li>
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('status', 'completed')">Completed</li>
                                </ul>

                                <input type="hidden" id="statusSelectInput" name="status" />
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block font-mont text-accent mb-1 tracking-[-1.22px]" for="cover_color_input">Cover color</label>
                        <div class="relative inline-block text-left w-full">
                            <div class="relative">
                                <button type="button" id="coverColorSelectButton" onclick="toggleCustomSelect('coverColor')" class="w-full text-left py-2 pr-2 focus:outline-none flex items-center cursor-pointer hover:bg-gray-100 border-b border-black outline-none focus:ring-0">
                                    <span id="coverColorSelectValue" class="font-mont text-[16px] font-medium tracking-[-1.44px] text-accent">Select cover color</span>
                                    <img id="coverColorSelectArrow" src="../src/asset/icon/arrow-black.svg" alt="arrow" class="ml-auto transition-transform duration-200" />
                                </button>

                                <ul id="coverColorSelectDropdown" class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto text-[16px] tracking-[-1.12px] font-mont font-normal text-primary bg-accent">
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Blue', 'from-bs to-be')">Blue</li>
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Gray', 'from-grs to-gre')">Grey</li>
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Purple', 'from-ps to-pe')">Purple</li>
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Orange', 'from-os to-oe')">Orange</li>
                                    <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('coverColor', 'Green', 'from-gs to-ge')">Green</li>
                                </ul>
                                <input type="hidden" id="coverColorSelectInput" name="cover_color" />
                            </div>
                        </div>
                    </div>
                </div>
                <button type="submit" class="bg-accent text-primary px-4 py-2 rounded font-normal mt-8 w-[120px]">Add Task</button>
            </form>
        </section>

    </main>

 <script>
        // Menggunakan objek untuk melacak status setiap dropdown
        const dropdownStates = {};

        function toggleCustomSelect(idPrefix) {
            const dropdown = document.getElementById(idPrefix + 'SelectDropdown');
            const arrow = document.getElementById(idPrefix + 'SelectArrow');
            const button = document.getElementById(idPrefix + 'SelectButton');

            // Tutup semua dropdown lain sebelum membuka yang baru
            for (const key in dropdownStates) {
                if (key !== idPrefix && dropdownStates[key]) { // Jika dropdown lain terbuka
                    const otherDropdown = document.getElementById(key + 'SelectDropdown');
                    const otherArrow = document.getElementById(key + 'SelectArrow');
                    if (otherDropdown) {
                        otherDropdown.classList.add("hidden");
                    }
                    if (otherArrow) {
                        otherArrow.classList.remove("rotate-180");
                    }
                    dropdownStates[key] = false;
                }
            }

            const isOpen = !dropdown.classList.contains("hidden");

            if (isOpen) {
                dropdown.classList.add("hidden");
                if (arrow) arrow.classList.remove("rotate-180");
                dropdownStates[idPrefix] = false;
            } else {
                dropdown.classList.remove("hidden");
                if (arrow) arrow.classList.add("rotate-180");
                dropdownStates[idPrefix] = true;
            }
        }

        // Fungsi selectOption yang diperbarui untuk menerima dua nilai (tampilan dan aktual)
        function selectOption(idPrefix, displayText, actualValue = displayText) {
            document.getElementById(idPrefix + 'SelectValue').textContent = displayText;
            document.getElementById(idPrefix + 'SelectInput').value = actualValue; // Set value ke hidden input
            toggleCustomSelect(idPrefix); // Tutup dropdown setelah memilih
        }

        // Optional: close on click outside
        document.addEventListener('click', function(event) {
            // Tutup semua dropdown jika klik di luar tombol atau dropdown mana pun
            let clickedOutsideAnyDropdown = true;
            for (const key in dropdownStates) {
                const dropdown = document.getElementById(key + 'SelectDropdown');
                const button = document.getElementById(key + 'SelectButton');
                if (button && button.contains(event.target) || (dropdown && dropdown.contains(event.target))) {
                    clickedOutsideAnyDropdown = false;
                    break;
                }
            }

            if (clickedOutsideAnyDropdown) {
                for (const key in dropdownStates) {
                    const dropdown = document.getElementById(key + 'SelectDropdown');
                    const arrow = document.getElementById(key + 'SelectArrow');
                    if (dropdown && !dropdown.classList.contains("hidden")) {
                        dropdown.classList.add("hidden");
                        if (arrow) arrow.classList.remove("rotate-180");
                        dropdownStates[key] = false;
                    }
                }
            }
        });
    </script>
<?php include 'footer.php'; ?>
</body>
</html>
