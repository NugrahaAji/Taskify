
<?php
session_start();
if (!isset($_SESSION['id_user'])) {
    header("Location: login.php"); // Gagal akses kalau belum login
    exit();
}

include '../backend/db.php';

$id_user = $_SESSION['id_user'];

// Ambil data user
$stmt = $conn->prepare("SELECT username, email, bio, profile_picture, cover_picture FROM users WHERE id = ?");
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($username, $email, $bio, $profile_picture, $cover_picture);
$stmt->fetch();
$stmt->close();

// Handle filters and sorting from GET parameters
$filter_categories = isset($_GET['category']) ? $_GET['category'] : [];
$sort_option = isset($_GET['sort']) ? $_GET['sort'] : '';

// Prepare SQL query with filters and sorting
$sql = "SELECT * FROM tasks WHERE id_user = ?";
$params = [];
$types = "i";
$params[] = $id_user;

if (!empty($filter_categories) && is_array($filter_categories)) {
    $placeholders = implode(',', array_fill(0, count($filter_categories), '?'));
    $sql .= " AND category IN ($placeholders)";
    $types .= str_repeat('s', count($filter_categories));
    $params = array_merge($params, $filter_categories);
}

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
    <!-- Navbar -->
    <nav class="h-[100px] bg-accent">
      <div class="container mx-auto flex justify-between">
        <div class="h-[100px] flex items-center">
          <img src="../src/asset/icon/logo-white.svg" alt="logo" />
          <ul
            class="ml-[152px] flex font-mont text-[18px] font-light tracking-[-1px] text-primary gap-9 h-[100px]"
          >
            <li class="flex items-center border-b-4 border-primary">
              <a href="Dashboard.php">Dashboard</a>
            </li>
            <li
              class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"
            >
              <a href="workspace.php">Workspace</a>
            </li>
            <li
              class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"
            >
              <a href="">Docs</a>
            </li>
          </ul>
        </div>
        <div class="flex items-center">
          <ul class="flex gap-5 hh-[100px] items-center">
            <li>
              <button class="flex items-center">
                <div class="h-[48px] w-[48px] rounded-full overflow-hidden">
                    <?php if (!empty($profile_picture)): ?>
                        <img src="../<?= htmlspecialchars($profile_picture) ?>" alt="Profile Picture" class="h-[48px] w-[48px] object-cover" />
                    <?php else: ?>
                        <img src="../src/asset/img/profile.svg" alt="Default Profile Picture" class="h-[48px] w-[48px] object-cover" />
                    <?php endif; ?>
                </div>

              </button>
            </li>
            <li>
              <a href="setting.php"
                ><img src="../src/asset/icon/setting.svg" alt=""
              /></a>
            </li>
            <li>
              <button class="flex items-center">
                <img src="../src/asset/icon/notif.svg" alt="" />
              </button>
            </li>
          </ul>
        </div>
      </div>
    </nav>

    <!-- Content -->
    <main class="container mx-auto mt-[86px] grid grid-cols-6 space-x-4">
      <!-- Filter & Filler -->
      <div class="">
        <img src="../src/asset/img/docs.svg" alt="" class="w-full"/>
        <div class="mt-6 space-y-[18px]">
          <form method="GET" action="dashboard.php">
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
              class="overflow-hidden transition-all duration-300 ease-in-out space-y-[18px]"
              style="max-height: 1000px"
            >
              <!-- Category -->
              <div class="">
                <label
                  for="Category"
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
            </div>
            <button type="submit" class="hidden">Apply Filters</button>
          </form>
        </div>
      </div>

      <!-- Task shown -->
      <div class="col-span-5">
        <!-- Title & sort -->
        <div class=" flex justify-between grow">
          <div class="flex space-x-4 items-center">
            <h1
              class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent"
            >
              Your Tasks
            </h1>
            <div
              class="rounded-full h-[21px] w-[45px] border-accent border flex justify-center items-center"
            >
              <span
                class="font-mont font-semibold tracking--2.08px text-accent text-[16px]"
                ><?php echo $task_count; ?></span
              >
            </div>
          </div>
          <div class="space-x-2 flex items-center">
            <span
              class="font-mont text-[16px] font-normal tracking-[-1.44px] text-inactive"
              >Sort by</span
            >
            <!-- Dropdown Menu -->
            <div class="relative inline-block text-left w-[120px]">
              <div class="relative">
                <!-- Tombol Select -->
                <button
                  id="customSelectButton"
                  onclick="toggleCustomSelect()"
                  class="w-full text-left py-2 focus:outline-none flex  items-center cursor-pointer hover:bg-gray-100"
                >
                  <span
                    id="customSelectValue"
                    class="font-mont text-[16px] font-medium tracking-[-1.44px] text-accent"
                    ><?php echo $sort_option ? $sort_option : 'None'; ?></span
                  >
                </button>

                <!-- Dropdown Options -->
                <ul
                  id="customSelectDropdown"
                  class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto text-[16px] tracking-[-1.12px] font-mont font-normal text-primary bg-accent"
                >
                  <li
                    class="p-2 hover:bg-shade cursor-pointer"
                    onclick="selectOption('Deadline')"
                  >
                    Deadline
                  </li>
                  <li
                    class="p-2 hover:bg-shade cursor-pointer"
                    onclick="selectOption('Name')"
                  >
                    Name
                  </li>
                </ul>

                <!-- Optional: hidden input for form use -->
                <form id="sortForm" method="GET" action="dashboard.php">
                  <input
                    type="hidden"
                    id="customSelectInput"
                    name="sort"
                    value="<?php echo htmlspecialchars($sort_option); ?>"
                  />
                  <?php
                  // Preserve category filters in sort form
                  foreach ($filter_categories as $cat) {
                      echo '<input type="hidden" name="category[]" value="' . htmlspecialchars($cat) . '">';
                  }
                  ?>
                </form>
              </div>
            </div>
            <button
              class="h-6 cursor-pointer hover:bg-gray-100"
              onclick="document.getElementById('sortForm').submit()"
            >
              <img src="../src/asset/icon/sort.svg" alt="" />
            </button>
          </div>
        </div>

        <!-- Task card -->
        <div class="grid grid-cols-5 gap-x-6 gap-y-[18px] mt-[18px]">
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
        const arrow = document.getElementById('selectArrow');

        const isOpen = !dropdown.classList.contains('hidden');

        if (isOpen) {
          dropdown.classList.add('hidden');
          arrow.classList.remove('rotate-180');
        } else {
          dropdown.classList.remove('hidden');
          arrow.classList.add('rotate-180');
        }
      }

      function selectOption(value) {
        document.getElementById('customSelectValue').textContent = value;
        document.getElementById('customSelectInput').value = value;
        toggleCustomSelect();
      }

      // Optional: close on click outside
      document.addEventListener('click', function (event) {
        const dropdown = document.getElementById('customSelectDropdown');
        const button = document.getElementById('customSelectButton');
        if (!button.contains(event.target) && !dropdown.contains(event.target)) {
          dropdown.classList.add('hidden');
          document.getElementById('selectArrow').classList.remove('rotate-180');
        }
      });
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
    </script>
  </body>
</html>