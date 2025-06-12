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
    <title>Taskify | Dashboard</title>
  </head>
  <body class="bg-primary">
    <!-- Navbar -->
    <nav class="h-[100px] bg-accent">
        <div class="container mx-auto flex justify-between">
            <div class="h-[100px] flex items-center">
                <img src="../src/asset/icon/logo-white.svg" alt="logo">
                <ul class="ml-[152px] flex font-mont text-[18px] font-light tracking-[-1px] text-primary gap-9 h-[100px]">
                    <li class="flex items-center border-b-4 border-primary"><a href="Dashboard.php">Dashboard</a></li>
                    <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="">Workspace</a></li>
                    <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="">Dashboard</a></li>
                </ul>
            </div>
            <div class="flex items-center">
                <ul class="flex gap-5 hh-[100px] items-center">
                    <li><button class="flex items-center"><img src="../src/asset/icon/profile.svg" alt=""></button></li>
                    <li><a href="setting.php"><img src="../src/asset/icon/setting.svg" alt=""></a></li>
                    <li><button class="flex items-center"><img src="../src/asset/icon/notif.svg" alt=""></button></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Content -->
    <main class="container mx-auto mt-[86px] grid grid-cols-6 space-x-4">
        <!-- Filter & Filler -->
        <div class="">
            <img src="../src/asset/icon/iklan.svg" alt="">
            <div class="mt-6 space-y-[18px]">
                <button onclick="toggleAccordion(1)" class="w-full flex space-x-4 items-center cursor-pointer">
                    <span class="font-mont font-semibold tracking-[-1.92px] text-2xl text-accent">Filters</span>
                    <img id="icon-1" src="../src/asset/icon/arrow-black.svg" alt="" class="transition-transform duration-300 transform rotate-180">
                </button>

                <div id="content-1" class="overflow-hidden transition-all duration-300 ease-in-out space-y-[18px]" style="max-height: 1000px;">
                    <!-- Category -->
                    <div class="">
                        <label for="Category" class="font-mont font-[400px] tracking-[-1.44px] text-inactive text-[16px]">Category</label>
                        <div class="flex flex-col">
                            <label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">
                                <input type="checkbox" class="peer hidden" />
                                <span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>
                                <span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">Exam</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">
                                <input type="checkbox" class="peer hidden" />
                                <span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>
                                <span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">Excercise</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">
                                <input type="checkbox" class="peer hidden" />
                                <span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>
                                <span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">Presentation</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">
                                <input type="checkbox" class="peer hidden" />
                                <span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>
                                <span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">Project</span>
                            </label>
                        </div>
                    </div>
                    <!-- yg lain -->
                    <div class="">
                        <label for="Category" class="font-mont font-[400px] tracking-[-1.44px] text-inactive text-[16px]">Category</label>
                        <div class="flex flex-col">
                            <label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">
                                <input type="checkbox" class="peer hidden" />
                                <span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>
                                <span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">Exam</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">
                                <input type="checkbox" class="peer hidden" />
                                <span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>
                                <span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">Excercise</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">
                                <input type="checkbox" class="peer hidden" />
                                <span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>
                                <span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">Presentation</span>
                            </label>
                            <label class="inline-flex items-center cursor-pointer space-x-2 hover:bg-gray-100">
                                <input type="checkbox" class="peer hidden" />
                                <span class="w-[14px] h-[14px] rounded-full border border-accent peer-checked:bg-shade peer-checked:border-accent transition-all duration-150"></span>
                                <span class="text-[16px] tracking-[-1.12px] font-mont font-normal text-accent">Project</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Task shown -->
        <div class="col-span-5">
            <!-- Title & sort -->
            <div class=" flex justify-between grow">
                <div class="flex space-x-4 items-center">
                    <h1 class="font-mont font-semibold tracking-[-2.88px] text-[32px] text-accent">Your Tasks</h1>
                    <div class="rounded-full h-[21px] w-[45px] border-accent border flex justify-center items-center">
                        <span class="font-mont font-semibold tracking--2.08px text-accent text-[16px]">7</span>
                    </div>
                </div>
                <div class="space-x-2 flex items-center">
                    <span class="font-mont text-[16px] font-normal tracking-[-1.44px] text-inactive">Sort by</span>
                    <!-- Dropdown Menu -->
                    <div class="relative inline-block text-left w-[120px]">
                        <div class="relative">
                            <!-- Tombol Select -->
                            <button id="customSelectButton" onclick="toggleCustomSelect()" class="w-full text-left py-2 focus:outline-none flex  items-center cursor-pointer hover:bg-gray-100">
                                <span id="customSelectValue" class="font-mont text-[16px] font-medium tracking-[-1.44px] text-accent">None</span>
                            </button>

                            <!-- Dropdown Options -->
                            <ul id="customSelectDropdown" class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-60 overflow-y-auto text-[16px] tracking-[-1.12px] font-mont font-normal text-primary bg-accent">
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('Deadline')">Deadline</li>
                                <li class="p-2 hover:bg-shade cursor-pointer" onclick="selectOption('Name')">Name</li>
                            </ul>

                            <!-- Optional: hidden input for form use -->
                            <input type="hidden" id="customSelectInput" name="selected_option" />
                        </div>
                    </div>
                    <button class="h-6 cursor-pointer hover:bg-gray-100"><img src="../src/asset/icon/sort.svg" alt=""></button>
                </div>
            </div>

            <!-- Task card -->
             <div class="grid grid-cols-5 gap-x-6 gap-y-[18px] mt-[18px]">
                <div class="h-[312px] rounded-[17px] border border-inactive">
                    <div class="h-3/4 bg-gradient-to-t from-orange-200 to-orange-100 m-[6px] rounded-[12px]"></div>
                    <div class="mx-4 py-2 flex justify-between items-center">
                        <div class="flex flex-col items-start justify-center">
                            <span class="text-inactive text-[12px] font-normal tracking-[-0.84px]">Deadline</span>
                            <p class="text-accent font-mont text-[16px] font-medium tracking-[-1.12px] leading-[0.9]">Sunday, 8 June 23.59</p>
                        </div>
                        <button class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px]">
                            Detail
                        </button>
                    </div>
                </div>
                <div class="h-[312px] rounded-[17px] border border-inactive">
                    <div class="h-3/4 bg-gradient-to-t from-purple-200 to-purple-100 m-[6px] rounded-[12px]"></div>
                    <div class="mx-4 py-2 flex justify-between items-center">
                        <div class="flex flex-col items-start justify-center">
                            <span class="text-inactive text-[12px] font-normal tracking-[-0.84px]">Deadline</span>
                            <p class="text-accent font-mont text-[16px] font-medium tracking-[-1.12px] leading-[0.9]">Sunday, 8 June 23.59</p>
                        </div>
                        <button class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px]">
                            Detail
                        </button>
                    </div>
                </div>
                <div class="h-[312px] rounded-[17px] border border-inactive">
                    <div class="h-3/4 bg-gradient-to-t from-emerald-200 to-emerald-100 m-[6px] rounded-[12px]"></div>
                    <div class="mx-4 py-2 flex justify-between items-center">
                        <div class="flex flex-col items-start justify-center">
                            <span class="text-inactive text-[12px] font-normal tracking-[-0.84px]">Deadline</span>
                            <p class="text-accent font-mont text-[16px] font-medium tracking-[-1.12px] leading-[0.9]">Sunday, 8 June 23.59</p>
                        </div>
                        <button class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px]">
                            Detail
                        </button>
                    </div>
                </div>
                <div class="h-[312px] rounded-[17px] border border-inactive">
                    <div class="h-3/4 bg-gradient-to-t from-amber-200 to-amber-100 m-[6px] rounded-[12px]"></div>
                    <div class="mx-4 py-2 flex justify-between items-center">
                        <div class="flex flex-col items-start justify-center">
                            <span class="text-inactive text-[12px] font-normal tracking-[-0.84px]">Deadline</span>
                            <p class="text-accent font-mont text-[16px] font-medium tracking-[-1.12px] leading-[0.9]">Sunday, 8 June 23.59</p>
                        </div>
                        <button class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px]">
                            Detail
                        </button>
                    </div>
                </div>
                <div class="h-[312px] rounded-[17px] border border-inactive">
                    <div class="h-3/4 bg-gradient-to-t from-slate-200 to-slate-100 m-[6px] rounded-[12px]"></div>
                    <div class="mx-4 py-2 flex justify-between items-center">
                        <div class="flex flex-col items-start justify-center">
                            <span class="text-inactive text-[12px] font-normal tracking-[-0.84px]">Deadline</span>
                            <p class="text-accent font-mont text-[16px] font-medium tracking-[-1.12px] leading-[0.9]">Sunday, 8 June 23.59</p>
                        </div>
                        <button class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px]">
                            Detail
                        </button>
                    </div>
                </div>
                <div class="h-[312px] rounded-[17px] border border-inactive">
                    <div class="h-3/4 bg-gradient-to-t from-blue-200 to-blue-100 m-[6px] rounded-[12px]"></div>
                    <div class="mx-4 py-2 flex justify-between items-center">
                        <div class="flex flex-col items-start justify-center">
                            <span class="text-inactive text-[12px] font-normal tracking-[-0.84px]">Deadline</span>
                            <p class="text-accent font-mont text-[16px] font-medium tracking-[-1.12px] leading-[0.9]">Sunday, 8 June 23.59</p>
                        </div>
                        <button class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px]">
                            Detail
                        </button>
                    </div>
                </div>
                <div class="h-[312px] rounded-[17px] border border-inactive">
                    <div class="h-3/4 bg-gradient-to-t from-red-200 to-red-100 m-[6px] rounded-[12px]"></div>
                    <div class="mx-4 py-2 flex justify-between items-center">
                        <div class="flex flex-col items-start justify-center">
                            <span class="text-inactive text-[12px] font-normal tracking-[-0.84px]">Deadline</span>
                            <p class="text-accent font-mont text-[16px] font-medium tracking-[-1.12px] leading-[0.9]">Sunday, 8 June 23.59</p>
                        </div>
                        <button class="bg-accent text-primary h-[33px] w-[83px] rounded-full font-normal tracking-[-1px]">
                            Detail
                        </button>
                    </div>
                </div>
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
    const dropdown = document.getElementById("customSelectDropdown");
    const arrow = document.getElementById("selectArrow");

    const isOpen = !dropdown.classList.contains("hidden");

    if (isOpen) {
      dropdown.classList.add("hidden");
      arrow.classList.remove("rotate-180");
    } else {
      dropdown.classList.remove("hidden");
      arrow.classList.add("rotate-180");
    }
  }

  function selectOption(value) {
    document.getElementById("customSelectValue").textContent = value;
    document.getElementById("customSelectInput").value = value;
    toggleCustomSelect();
  }

  // Optional: close on click outside
  document.addEventListener('click', function(event) {
    const dropdown = document.getElementById("customSelectDropdown");
    const button = document.getElementById("customSelectButton");
    if (!button.contains(event.target) && !dropdown.contains(event.target)) {
      dropdown.classList.add("hidden");
      document.getElementById("selectArrow").classList.remove("rotate-180");
    }
  });
    </script>
  </body>
</html>
