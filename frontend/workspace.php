<?php
session_start();

// Security check - redirect if not logged in
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

include '../backend/db.php';

class WorkspaceManager {
    private $conn;
    private $user_id;
    
    public function __construct($connection, $user_id) {
        $this->conn = $connection;
        $this->user_id = $user_id;
    }
    
    /**
     * Get user's workspace ID
     */
    private function getUserWorkspaceId() {
        $stmt = $this->conn->prepare("SELECT id_workspace FROM workspace WHERE id_user = ? LIMIT 1");
        if (!$stmt) {
            error_log("Failed to prepare workspace query: " . $this->conn->error);
            return null;
        }
        
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $workspace = $result->fetch_assoc();
            $stmt->close();
            return $workspace['id_workspace'];
        }
        
        $stmt->close();
        return null;
    }
    
    /**
     * Add collaborator to workspace
     */
    public function addCollaborator($email) {
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return "Invalid email address.";
        }
        
        // Find user by email
        $stmt = $this->conn->prepare("SELECT id FROM users WHERE email = ?");
        if (!$stmt) {
            error_log("Failed to prepare user find statement: " . $this->conn->error);
            return "System error occurred.";
        }
        
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows !== 1) {
            $stmt->close();
            return "User with that email not found.";
        }
        
        $collab_user = $result->fetch_assoc();
        $collab_user_id = $collab_user['id'];
        $stmt->close();
        
        // Prevent adding self as collaborator
        if ($collab_user_id == $this->user_id) {
            return "You cannot add yourself as a collaborator.";
        }
        
        // Get workspace ID
        $workspace_id = $this->getUserWorkspaceId();
        if ($workspace_id === null) {
            return "No workspace found. Please create a workspace first.";
        }
        
        // Check if already collaborator
        $stmt = $this->conn->prepare("SELECT id_collab FROM collaborators WHERE id_workspace = ? AND id_user = ?");
        if (!$stmt) {
            error_log("Failed to prepare collaborator check statement: " . $this->conn->error);
            return "System error occurred.";
        }
        
        $stmt->bind_param("ii", $workspace_id, $collab_user_id);
        $stmt->execute();
        $check_result = $stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $stmt->close();
            return "This user is already a collaborator in your workspace.";
        }
        $stmt->close();
        
        // Add collaborator
        $stmt = $this->conn->prepare("INSERT INTO collaborators (id_workspace, id_user) VALUES (?, ?)");
        if (!$stmt) {
            error_log("Failed to prepare collaborator insert statement: " . $this->conn->error);
            return "System error occurred.";
        }
        
        $stmt->bind_param("ii", $workspace_id, $collab_user_id);
        if ($stmt->execute()) {
            $stmt->close();
            return "success";
        } else {
            error_log("Failed to insert collaborator: " . $stmt->error);
            $stmt->close();
            return "Failed to add collaborator.";
        }
    }
    
    /**
     * Get user's tasks
     */
    public function getTasks() {
        $stmt = $this->conn->prepare("SELECT * FROM tasks WHERE id_user = ? ORDER BY deadline ASC");
        if (!$stmt) {
            error_log("Failed to prepare tasks fetch statement: " . $this->conn->error);
            return [];
        }
        
        $stmt->bind_param("i", $this->user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $tasks = [];
        while ($row = $result->fetch_assoc()) {
            $tasks[] = $row;
        }
        
        $stmt->close();
        return $tasks;
    }
    
    /**
     * Get workspace collaborators
     */
    public function getCollaborators() {
        $workspace_id = $this->getUserWorkspaceId();
        if ($workspace_id === null) {
            return [];
        }
        
        $stmt = $this->conn->prepare("
            SELECT c.id_collab, u.username, u.email 
            FROM collaborators c 
            JOIN users u ON c.id_user = u.id 
            WHERE c.id_workspace = ?
            ORDER BY u.username ASC
        ");
        
        if (!$stmt) {
            error_log("Failed to prepare collaborators fetch statement: " . $this->conn->error);
            return [];
        }
        
        $stmt->bind_param("i", $workspace_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        $collaborators = [];
        while ($row = $result->fetch_assoc()) {
            $collaborators[] = $row;
        }
        
        $stmt->close();
        return $collaborators;
    }
}

// Initialize workspace manager
$workspace = new WorkspaceManager($conn, $_SESSION['id_user']);

// Handle collaborator addition
$collab_message = '';
$message_type = 'error'; // 'success' or 'error'

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['add_collaborator'])) {
    $collab_email = trim($_POST['collab_email'] ?? '');
    $result = $workspace->addCollaborator($collab_email);
    
    if ($result === 'success') {
        $collab_message = "Collaborator added successfully.";
        $message_type = 'success';
        // Redirect to prevent resubmission
        header("Location: workspace.php?success=1");
        exit();
    } else {
        $collab_message = $result;
        $message_type = 'error';
    }
}

// Check for success parameter from redirect
if (isset($_GET['success']) && $_GET['success'] == '1') {
    $collab_message = "Collaborator added successfully.";
    $message_type = 'success';
}

// Fetch data
$tasks = $workspace->getTasks();
$collaborators = $workspace->getCollaborators();

/**
 * Helper function to safely output HTML
 */
function h($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

/**
 * Helper function to format date
 */
function formatDeadline($datetime) {
    return date('D, j F H:i', strtotime($datetime));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Taskify | Workspace</title>
    <link rel="icon" href="../src/asset/icon/Taskify.ico" type="image/x-icon" />
    <link href="../src/style.css" rel="stylesheet" />
</head>
<body class="bg-primary font-mont">
    <?php include 'header.php'; ?>

    <main class="container mx-auto mt-[86px]">
        <div class="flex justify-between items-center mb-6">
            <h1 class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent">Your workspace</h1>
            <a href="add-task.php" 
               class="bg-accent text-primary px-4 py-2 rounded-[12px] font-normal tracking-[-1px] hover:scale-105 ease-in-out transform transition-all duration-300 shadow-lg hover:shadow-xl">
                Add Task
            </a>
        </div>
        
        <!-- Tasks Section -->
        <section class="grid grid-cols-6 gap-x-6 gap-y-[18px] mt-[18px] mb-10">
            <?php if (empty($tasks)): ?>
                <p class="text-accent font-mont col-span-6">No tasks added yet.</p>
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
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="inline border border-accent rounded-full w-fits px-2 py-1 text-[12px] font-semibold">
                                        <?php echo htmlspecialchars($task['category']); ?>
                                    </span>
                                    <span class="inline border border-accent rounded-full w-fits px-2 py-1 text-[12px] font-semibold">
                                        <?php echo htmlspecialchars($task['status']); ?>
                                    </span>
                                </div>
                            </div>
                        </div>
                        <div class="mx-4 py-2 flex justify-between items-center p">
                            <div class="flex flex-col items-start justify-center">
                                <span class="text-inactive text-[12px] font-normal tracking-[-0.84px]">Deadline</span>
                                <p class="text-accent font-mont text-[16px] font-medium tracking-[-1.12px] leading-[0.9]"><?php echo htmlspecialchars(date('D, j F H:i', strtotime($task['deadline']))); ?></p>
                            </div>
                            <a href="" class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px] text-center pt-1">
                                Detail
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </section>

        <!-- Collaborators Section -->
        <section class="mb-10 w-full">
            <h2 class="font-mont font-semibold text-xl mb-2 tracking-[-1.12px] text-accent">Collaborators</h2>
            
            <!-- Current Collaborators -->
            <h3 class="font-mont font-semibold mb-2 tracking-[-1.1px]">Current Collaborators</h3>
            <?php if (empty($collaborators)): ?>
                <p class="text-accent font-mont text-[16px] font-normal tracking-[-1.0px] mb-8">No collaborators added yet.</p>
            <?php else: ?>
                <ul class="list-disc list-inside font-mont text-accent mb-8">
                    <?php foreach ($collaborators as $collab): ?>
                        <li><?= h($collab['username']) . ' (' . h($collab['email']) . ')' ?></li>
                    <?php endforeach; ?>
                </ul>
            <?php endif; ?>

            <!-- Message Display -->
            <?php if ($collab_message): ?>
                <p class="<?= $message_type === 'success' ? 'text-green-600' : 'text-red-600' ?> mb-4">
                    <?= h($collab_message) ?>
                </p>
            <?php endif; ?>
            
            <!-- Add Collaborator Form -->
            <h3 class="font-mont font-semibold mb-2 tracking-[-1.1px]">Add Collaborators</h3>
            <form method="POST" action="workspace.php" class="space-y-4" id="collaborator-form">
                <input type="hidden" name="add_collaborator" value="1" />
                <div>
                    <label class="block font-mont text-accent mb-1" for="collab_email">Collaborator Email</label>
                    <input type="email" id="collab_email" name="collab_email" required 
                           class="w-lg border-b border-black outline-none focus:ring-0 h-[41px] text-[16px] hover:bg-gray-100" />
                </div>
                <button type="submit" 
                        class="bg-accent text-primary px-4 py-2 rounded font-normal text-[16px] tracking-[-0.9px] flex items-center group space-x-2 transform transition ease-in-out duration-1000 disabled:opacity-50"
                        id="submit-btn">
                    <img src="../src/asset/icon/plus.svg" alt="" class="hidden group-hover:block">
                    <span>Add Collaborator</span>
                </button>
            </form>
        </section>
    </main>

    <script>
        // Aspect ratio management for gradient boxes
        function setGradientBoxAspectRatio() {
            const gradientBoxes = document.querySelectorAll('.gradient-box');
            gradientBoxes.forEach(box => {
                const width = box.clientWidth;
                const height = (width * 4) / 5;
                box.style.height = `${height}px`;
            });
        }

        // Form enhancement
        function enhanceForm() {
            const form = document.getElementById('collaborator-form');
            const submitBtn = document.getElementById('submit-btn');
            
            if (form && submitBtn) {
                form.addEventListener('submit', function() {
                    submitBtn.disabled = true;
                    const span = submitBtn.querySelector('span');
                    if (span) span.textContent = 'Adding...';
                });
            }
        }

        // Auto-hide success messages
        function autoHideMessages() {
            const successMessages = document.querySelectorAll('.text-green-600');
            successMessages.forEach(msg => {
                setTimeout(() => {
                    msg.style.transition = 'opacity 0.5s ease-out';
                    msg.style.opacity = '0';
                    setTimeout(() => msg.remove(), 500);
                }, 5000);
            });
        }

        // Initialize everything
        document.addEventListener('DOMContentLoaded', function() {
            setGradientBoxAspectRatio();
            enhanceForm();
            autoHideMessages();
        });

        window.addEventListener('resize', setGradientBoxAspectRatio);
    </script>

    <?php include 'footer.php'; ?>
</body>
</html>