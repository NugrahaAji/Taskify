<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php");
    exit();
}

include '../backend/db.php';

$id_user = $_SESSION['id_user'];

// Ambil data user
$stmt = $conn->prepare("SELECT username, email, bio, profile_picture, cover_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $id_user);
$stmt->execute();
$stmt->bind_result($username, $email, $bio, $profile_picture, $cover_picture);
$stmt->fetch();
$stmt->close();

$filter_categories = isset($_GET['category']) ? $_GET['category'] : [];

$filter_statuses = isset($_GET['status']) ? $_GET['status'] : [];
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : '';

$sql = "SELECT * FROM tasks WHERE id_user = ?";
$params = [];
$types = "i";
$params[] = $id_user;

if ($sort_option === 'Deadline') {
    $sql .= " ORDER BY deadline ASC";
} elseif ($sort_option === 'Name') {
    $sql .= " ORDER BY task_name ASC";
} else {
    $sql .= " ORDER BY created_at DESC";
}

// Prepare and execute statement
$stmt = $conn->prepare($sql);
if ($stmt === false) {
    die("Prepare failed: " . $conn->error);
}

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

$tasks = [];
while ($row = $result->fetch_assoc()) {
    $tasks[] = $row;
}

$task_count = count($tasks);
?>

<!DOCTYPE html>
<html>
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <link rel="icon" href="../src/asset/icon/Taskify.ico" type="image/x-icon" />
    <link href="../src/style.css" rel="stylesheet" />
    <title>Taskify | Dashboard</title>
  </head>
  <body class="bg-primary">
    <?php include 'header.php'; ?>

    <main class="container mx-auto mt-[86px] grid grid-cols-6 space-x-4">
      <div class="">
        <img src="../src/asset/img/docs.svg" alt="" class="w-full"/>
        <div class="mt-6 space-y-[18px]">
            <div id="filter-section">
              <button
                type="button"
                onclick="toggleAccordion(1)"
                class="w-full flex space-x-4 items-center cursor-pointer"
              >
                <span
                  class="font-mont font-semibold tracking-[-1.92px] text-2xl text-accent"
                  >Filters</span
                >
                <img
                  id="icon-1"
                  src="../src/asset/icon/arrow-black.svg"
                  alt=""
                  class="transition-transform duration-300 transform rotate-180"
                />
              </button>

              <div
                id="content-1"
                class="overflow-hidden transition-all duration-300 ease-in-out space-y-[18px] pt-4"
                style="max-height: 1000px"
              >
                <div class="">
                  <label
                    class="font-mont font-[400px] tracking-[-1.44px] text-inactive text-[16px]"
                    >Category</label
                  >
                  <div class="flex flex-col">
                    <?php
                    $categories = ['Exam', 'Excercise', 'Presentation', 'Project'];
                    foreach ($categories as $category) {
                        $checked = in_array($category, $filter_categories) ? 'checked' : '';
                        echo '<label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">';
                        echo '<input type="checkbox" name="category[]" value="' . $category . '" class="peer hidden" ' . $checked . ' />';
                        echo '<span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>';
                        echo '<span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">' . $category . '</span>';
                        echo '</label>';
                    }
                    ?>
                  </div>
                </div>

                <div class="pt-2">
                  <label
                    class="font-mont font-[400px] tracking-[-1.44px] text-inactive text-[16px]"
                    >Status</label
                  >
                  <div class="flex flex-col">
                    <?php
                    $statuses = ['Pending', 'Progres', 'Completed'];
                    foreach ($statuses as $status) {
                        $checked = in_array($status, $filter_statuses) ? 'checked' : '';
                        echo '<label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">';
                        echo '<input type="checkbox" name="status[]" value="' . $status . '" class="peer hidden" ' . $checked . ' />';
                        echo '<span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>';
                        echo '<span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">' . $status . '</span>';
                        echo '</label>';
                    }
                    ?>
                  </div>
                </div>
                </div>
            </div>
        </div>
      </div>

      <div class="col-span-5">
        <div class=" flex justify-between grow">
          <div class="flex space-x-4 items-center">
            <h1 class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent">
              Your Tasks
            </h1>
            <div class="rounded-full h-[21px] w-auto min-w-[45px] px-2 border-accent border flex justify-center items-center">
              <span id="task-count-display" class="font-mont font-semibold tracking--2.08px text-accent text-[16px]"><?php echo $task_count; ?></span>
            </div>
          </div>
          <div class="space-x-2 flex items-center">
            <span class="font-mont text-[16px] font-normal tracking-[-1.44px] text-inactive">Sort by</span>
            <div class="relative inline-block text-left w-[120px]">
              <div class="relative">
                <button id="customSelectButton" onclick="toggleCustomSelect()" class="w-full text-left py-2 focus:outline-none flex  items-center cursor-pointer hover:bg-gray-100">
                  <span id="customSelectValue" class="font-mont text-[16px] font-medium tracking-[-1.44px] text-accent"><?php echo $sort_option ? $sort_option : 'None'; ?></span>
                </button>
                <ul id="customSelectDropdown" class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto text-[16px] tracking-[-1.12px] font-mont font-normal text-primary bg-accent">
                  <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('Deadline')">Deadline</li>
                  <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('Name')">Name</li>
                </ul>
                <form id="sortForm" method="GET" action="dashboard.php">
                  <input type="hidden" id="customSelectInput" name="sort" value="<?php echo htmlspecialchars($sort_option); ?>" />
                  <?php
                  // Preserve filters in sort form
                  foreach ($filter_categories as $cat) {
                      echo '<input type="hidden" name="category[]" value="' . htmlspecialchars($cat) . '">';
                  }
                  foreach ($filter_statuses as $stat) {
                      echo '<input type="hidden" name="status[]" value="' . htmlspecialchars($stat) . '">';
                  }
                  ?>
                </form>
              </div>
            </div>
            <button class="h-6 cursor-pointer hover:bg-gray-100" onclick="document.getElementById('sortForm').submit()">
              <img src="../src/asset/icon/sort.svg" alt="" />
            </button>
          </div>
        </div>

        <div id="task-container" class="grid grid-cols-5 gap-x-6 gap-y-[18px] mt-[18px]">
        <?php if (count($tasks) === 0): ?>
            <p id="no-tasks-message" class="text-accent font-mont col-span-2">No tasks added yet.</p>
            <?php else: ?>
                <?php foreach ($tasks as $task): ?>
                    <div class="rounded-[17px] border border-inactive task-card">
                        <div class="gradient-box bg-gradient-to-t <?php echo htmlspecialchars($task['cover_color']); ?> m-[6px] rounded-[12px] flex items-end">
                            <div class="mx-4 mb-6">
                                <h2 class="font-mont font-semibold text-xl mb-2 tracking-[-1.12px] leading-5"><?php echo htmlspecialchars($task['task_name']); ?></h2>
                                <div class="flex items-center space-x-2">
                                    <div class="inline font-medium tracking-[-1.08px] text-[14px]">
                                        <?php echo htmlspecialchars($task['subject']); ?>
                                    </div>
                                </div>
                                <div class="flex items-center space-x-2">
                                    <span class="task-category inline border border-accent rounded-full w-fits px-2 py-1 text-[12px] font-semibold">
                                        <?php echo htmlspecialchars($task['category']); ?>
                                    </span>
                                    <span class="task-status inline border border-accent rounded-full w-fits px-2 py-1 text-[12px] font-semibold">
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
                              <a href="task-detail.php?id=<?php echo htmlspecialchars($task['id_task']); ?>" class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px] text-center pt-1">
                                  Detail
                              </a>
                        </div>
                    </div>
                <?php endforeach; ?>
                <p id="no-tasks-match" class="text-accent font-mont col-span-5 hidden">No tasks match the selected filters.</p>
            <?php endif; ?>
        </div>
      </div>
    </main>
    <script>
      function toggleAccordion(index) {
        const content = document.getElementById(`content-${index}`);
        const icon = document.getElementById(`icon-${index}`);
        const isOpen = content.style.maxHeight && content.style.maxHeight !== '0px';
        if (isOpen) {
          content.style.maxHeight = '0';
          icon.classList.remove('rotate-180');
        } else {
          content.style.maxHeight = content.scrollHeight + 'px';
          icon.classList.add('rotate-180');
        }
      }

      function toggleCustomSelect() {
        const dropdown = document.getElementById('customSelectDropdown');
        const isOpen = !dropdown.classList.contains('hidden');
        if (isOpen) {
          dropdown.classList.add('hidden');
        } else {
          dropdown.classList.remove('hidden');
        }
      }

      function selectOption(value) {
        document.getElementById('customSelectValue').textContent = value;
        document.getElementById('customSelectInput').value = value;
        toggleCustomSelect();
      }

      document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('customSelectDropdown');
        const button = document.getElementById('customSelectButton');
        if (!button.contains(event.target) && !dropdown.contains(event.target)) {
          dropdown.classList.add('hidden');
        }
      });

      function setGradientBoxAspectRatio() {
        const gradientBoxes = document.querySelectorAll('.gradient-box');
        gradientBoxes.forEach(box => {
            const width = box.clientWidth;
            const height = (width * 4) / 5;
            box.style.height = `${height}px`;
        });
      }

      function applyFilters() {

        const checkedCategoryCheckboxes = document.querySelectorAll('input[name="category[]"]:checked');
        const selectedCategories = Array.from(checkedCategoryCheckboxes).map(cb => cb.value);

        const checkedStatusCheckboxes = document.querySelectorAll('input[name="status[]"]:checked');
        const selectedStatuses = Array.from(checkedStatusCheckboxes).map(cb => cb.value);


        const allTasks = document.querySelectorAll('.task-card');
        const noTasksMatchMessage = document.getElementById('no-tasks-match');
        let visibleTaskCount = 0;

        allTasks.forEach(taskCard => {
            const taskCategoryElement = taskCard.querySelector('.task-category');

            const taskStatusElement = taskCard.querySelector('.task-status');

            if (taskCategoryElement && taskStatusElement) {
                const taskCategory = taskCategoryElement.textContent.trim();
                const taskStatus = taskStatusElement.textContent.trim();

                const categoryMatch = selectedCategories.length === 0 || selectedCategories.includes(taskCategory);
                const statusMatch = selectedStatuses.length === 0 || selectedStatuses.includes(taskStatus);

                if (categoryMatch && statusMatch) {
                    taskCard.style.display = 'block';
                    visibleTaskCount++;
                } else {
                    taskCard.style.display = 'none';
                }
            }
        });

        const taskCountDisplay = document.getElementById('task-count-display');
        if(taskCountDisplay) {
            taskCountDisplay.textContent = visibleTaskCount;
        }

        if (visibleTaskCount === 0 && allTasks.length > 0) {
            noTasksMatchMessage.classList.remove('hidden');
        } else {
            noTasksMatchMessage.classList.add('hidden');
        }
      }

      document.addEventListener('DOMContentLoaded', () => {
        setGradientBoxAspectRatio();

        const categoryCheckboxes = document.querySelectorAll('input[name="category[]"]');
        categoryCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', applyFilters);
        });

        const statusCheckboxes = document.querySelectorAll('input[name="status[]"]');
        statusCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', applyFilters);
        });
        // END: Status Filter Logic

        applyFilters();
      });

      window.addEventListener('resize', setGradientBoxAspectRatio);
    </script>
    <?php include 'footer.php'; ?>
  </body>
</html>
