<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

include '../backend/db.php';
$id_user = $_SESSION['id_user'];

// Handle adding collaborator
$collab_message = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_collaborator'])) {
    $collab_email = $_POST['collab_email'] ?? '';

    $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
    if ($stmt) {
        $stmt->bind_param("s", $collab_email);
        $stmt->execute();
        $result = $stmt->get_result();
        if ($result->num_rows === 1) {
            $collab_user = $result->fetch_assoc();
            $collab_user_id = $collab_user['id'];

            $stmt_ws = $conn->prepare("SELECT id_workspace FROM workspace WHERE id_user = ? LIMIT 1");
            if ($stmt_ws) {
                $stmt_ws->bind_param("i", $id_user);
                $stmt_ws->execute();
                $result_ws = $stmt_ws->get_result();
                if ($result_ws->num_rows === 1) {
                    $workspace = $result_ws->fetch_assoc();
                    $workspace_id = $workspace['id_workspace'];

                    $stmt_check = $conn->prepare("SELECT id_collab FROM collaborators WHERE id_workspace = ? AND id_user = ?");
                    if ($stmt_check) {
                        $stmt_check->bind_param("ii", $workspace_id, $collab_user_id);
                        $stmt_check->execute();
                        $check_result = $stmt_check->get_result();
                        if ($check_result->num_rows > 0) {
                            $collab_message = "This user is already a collaborator in your workspace.";
                        } else {
                            $stmt_insert = $conn->prepare("INSERT INTO collaborators (id_workspace, id_user) VALUES (?, ?)");
                            if ($stmt_insert) {
                                $stmt_insert->bind_param("ii", $workspace_id, $collab_user_id);
                                if ($stmt_insert->execute()) {
                                    $collab_message = "Collaborator added successfully.";
                                    // --- INI PERBAIKAN UNTUK ADD COLLABORATOR ---
                                    header("Location: workspace.php"); // Redirect ke halaman yang sama
                                    exit(); // Penting: hentikan eksekusi skrip setelah redirect
                                } else {
                                    $collab_message = "Failed to add collaborator.";
                                    error_log("Failed to insert collaborator: " . $stmt_insert->error);
                                }
                                $stmt_insert->close();
                            } else {
                                error_log("Failed to prepare insert statement for collaborator: " . $conn->error);
                            }
                        }
                        $stmt_check->close();
                    } else {
                        error_log("Failed to prepare check statement for collaborator: " . $conn->error);
                    }
                } else {
                    $collab_message = "No workspace found for your user. Please create a workspace first.";
                }
                $stmt_ws->close();
            } else {
                error_log("Failed to prepare workspace check statement: " . $conn->error);
            }
        } else {
            $collab_message = "User with that email not found.";
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare user find statement: " . $conn->error);
    }
}

// Bagian untuk fetch data task dan collaborator tidak perlu diubah,
// karena mereka hanya mengambil data, bukan mengubahnya.
// ... (sisa kode PHP untuk fetch tasks dan collaborators) ...

// Fetch tasks for user
$stmt = $conn->prepare("SELECT * FROM tasks WHERE id_user = ?");
if ($stmt) {
    $stmt->bind_param("i", $id_user);
    $stmt->execute();
    $tasks_result = $stmt->get_result();
    $tasks = [];
    while ($row = $tasks_result->fetch_assoc()) {
        $tasks[] = $row;
    }
    $stmt->close();
} else {
    error_log("Failed to prepare tasks fetch statement: " . $conn->error);
    $tasks = []; // Ensure $tasks is an empty array if query fails
}


// Fetch collaborators for user's workspace
$collaborators = [];
// First, find the workspace_id for the current user
$user_workspace_id = null;
$stmt_ws = $conn->prepare("SELECT id_workspace FROM workspace WHERE id_user = ? LIMIT 1");
if ($stmt_ws) {
    $stmt_ws->bind_param("i", $id_user);
    $stmt_ws->execute();
    $result_ws = $stmt_ws->get_result();
    if ($result_ws->num_rows === 1) {
        $ws_data = $result_ws->fetch_assoc();
        $user_workspace_id = $ws_data['id_workspace'];
    }
    $stmt_ws->close();
} else {
    error_log("Failed to prepare user workspace fetch statement: " . $conn->error);
}


if ($user_workspace_id !== null) {
    $stmt = $conn->prepare("SELECT c.id_collab, u.username, u.email FROM collaborators c JOIN users u ON c.id_user = u.id WHERE c.id_workspace = ?");
    if ($stmt) {
        $stmt->bind_param("i", $user_workspace_id);
        $stmt->execute();
        $collab_result = $stmt->get_result();
        while ($row = $collab_result->fetch_assoc()) {
            $collaborators[] = $row;
        }
        $stmt->close();
    } else {
        error_log("Failed to prepare collaborators fetch statement: " . $conn->error);
    }
}

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
<body class="bg-primary font-mont">
    <nav class="h-[100px] bg-accent">
        <div class="container mx-auto flex justify-between">
            <div class="h-[100px] flex items-center">
                <img src="../src/asset/icon/logo-white.svg" alt="logo" />
                <ul class="ml-[152px] flex font-mont text-[18px] font-light tracking-[-1px] text-primary gap-9 h-[100px]">
                    <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="dashboard.php">Dashboard</a></li>
                    <li class="flex items-center border-b-4 border-primary"><a href="workspace.php">Workspace</a></li>
                    <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="dashboard.php">Docs</a></li>
                </ul>
            </div>
            <div class="flex items-center">
                <ul class="flex gap-5 h-[100px] items-center">
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

    <div class="fixed bottom-10 right-[60px] z-10 hover:scale-120 ease-in-out transform transition-all duration-300">
        <a href="add-task.php" class="">
            <img src="../src/asset/icon/big-plus.svg" alt="add task">
        </a>
    </div>

    <main class="container mx-auto mt-[86px]">
        <h1 class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent mb-6">Your workspace</h1>
        <section class="grid grid-cols-6 gap-x-6 gap-y-[18px] mt-[18px]">
            <?php if (count($tasks) === 0): ?>
                <p class="text-accent font-mont col-span-3">No tasks added yet.</p>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="rounded-[17px] border border-inactive">
                        <div class="gradient-box bg-gradient-to-t <?php echo htmlspecialchars($task['cover_color']); ?> m-[6px] rounded-[12px] flex items-end">
                            <div class="mx-4 mb-6">
                                <h2 class="font-mont font-semibold text-xl mb-2 tracking-[-1.12px] leading-5"><?php echo htmlspecialchars($task['task_name']); ?></h2>
                                <div class="flex items-center space-x-2">
                                    <div class="inline font-medium tracking-[-1.08px] text-[14px]">
                                        <?php echo htmlspecialchars($task['subject']); ?>
                                    </div>
                                    <div class="inline border border-accent rounded-full w-fits px-2 py-1 text-[12px] font-semibold">
                                        <?php echo htmlspecialchars($task['category']); ?>
                                    </div>
                                    <div class="inline border border-accent rounded-full w-fits px-2 py-1 text-[12px] font-semibold">
                                        <?php echo htmlspecialchars($task['status']); ?>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="mx-4 py-2 flex justify-between items-center">
                            <div class="flex flex-col items-start justify-center">
                                <span class="text-inactive text-[12px] font-normal tracking-[-0.84px]">Deadline</span>
                                <p class="text-accent font-mont text-[16px] font-medium tracking-[-1.12px] leading-[0.9]"><?php echo htmlspecialchars(date('D, j F H:i', strtotime($task['deadline']))); ?></p>
                            </div>
                            <button class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px]">
                                Detail
                            </button>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>

        </section>


        <section class="mb-10 w-full mt-10">
            <h2 class="font-mont font-semibold text-xl mb-2 tracking-[-1.12px]">Collaborators</h2>
            <h3 class="font-mont font-semibold mb-2 tracking-[-1.1px]">Current Collaborators</h3>
            <?php if (count($collaborators) === 0): ?>
                <p class="text-accent font-mont text-[16px] font-normal tracking-[-1.0px] mb-8">No collaborators added yet.</p>
            <?php else: ?>
                <ul class="list-disc list-inside font-mont text-accent mb-8">
                    <?php foreach ($collaborators as $collab): ?>
                        <li><?php echo htmlspecialchars($collab['username']) . ' (' . htmlspecialchars($collab['email']) . ')'; ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <?php if ($collab_message): ?>
                <p class="text-red-600 mb-4"><?php echo htmlspecialchars($collab_message); ?></p>
            <?php endif; ?>
            <h3 class="font-mont font-semibold mb-2 tracking-[-1.1px]">Add Collaborators</h3>
            <form method="POST" action="workspace.php" class="space-y-4">
                <input type="hidden" name="add_collaborator" value="1" />
                <div>
                    <label class="block font-mont text-accent mb-1" for="collab_email">Collaborator Email</label>
                    <input type="email" id="collab_email" name="collab_email" required class="w-lg border-b border-black outline-none focus:ring-0 h-[41px] text-[16px] hover:bg-gray-100 " />
                </div>
                <button type="submit" class="bg-accent text-primary px-4 py-2 rounded font-normal text-[16px] tracking-[-0.9px] flex items-center group space-x-2 transform transition ease-in-out duration-1000 ">
                    <img src="../src/asset/icon/plus.svg" alt="" class="hidden group-hover:block">
                    <span>Add Collaborator</span>
                </button>
            </form>
        </section>
    </main>

    <script>
        function setGradientBoxAspectRatio() {
            const gradientBoxes = document.querySelectorAll('.gradient-box');
            gradientBoxes.forEach(box => {
                const width = box.clientWidth;
                const height = (width * 4) / 5; // Adjusted to 4/5 as per previous discussion
                box.style.height = `${height}px`;
            });
        }

        document.addEventListener('DOMContentLoaded', setGradientBoxAspectRatio);
        window.addEventListener('resize', setGradientBoxAspectRatio);
    </script>
</body>
</html>
