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
    <title>Taskify | Edit Profile</title>
  </head>
  <body class="bg-primary scrollbar">
        <nav class="h-[100px] bg-accent">
            <div class="container mx-auto flex justify-between">
                <div class="h-[100px] flex items-center">
                    <img src="../src/asset/icon/logo-white.svg" alt="logo">
                    <ul class="ml-[152px] flex font-mont text-[18px] font-light tracking-[-1px] text-primary gap-9 h-[100px]">
                        <li class="flex items-center border-b-4 hover:border-shade border-accent"><a href="Dashboard.php">Dashboard</a></li>
                        <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="">Workspace</a></li>
                        <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="">Dashboard</a></li>
                    </ul>
                </div>
                <div class="flex items-center">
                    <ul class="flex gap-5 hh-[100px] items-center">
                        <li><button class="flex items-center"><img src="../src/asset/icon/profile.svg" alt=""></button></li>
                        <li><a href="setting.php"><img src="../src/asset/icon/setting-white.svg" alt=""></a></li>
                        <li><button class="flex items-center"><img src="../src/asset/icon/notif.svg" alt=""></button></li>
                    </ul>
                </div>
            </div>
        </nav>
        <div>
            <img src="../src/asset/img/gradient.png" alt="">
        </div>
        <div class="max-w-[1120px] mx-auto">
            <div class="w-full space-x-6 flex -mt-[57px] ">
                <div class="flex flex-row ">
                    <div>
                        <img src="../src/asset/img/profile.svg" alt="">
                    </div>
                </div>
                <div class="flex mt-[52px] w-full justify-between items-center">
                    <div class="">
                        <h1 class="font-mont text-[32px] font-semibold tracking-[-2.88px] text-accent">Nugraha Aji Saputra</h1>
                        <h1 class="font-mont text-[16px] font-normal tracking-[-1.28px] text-inactive -mt-2">ajiselebew@gmail.com</h1>
                    </div>
                </div>
            </div>
            <div class="mt-12">
                <div class="justify-between flex items-center">
                    <h1 class="font-mont text-2xl text-accent font-semibold tracking-[-1.92px]">Edit account info</h1>
                    <a href="setting.php" class="cursor-pointer hover:bg-slate-200 rounded-full">
                        <img src="../src/asset/icon/cancel.svg" alt="cancel">
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-x-[60px] gap-y-[18px] mt-4 pb-[42px]">
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Username</label>
                        <input type="text" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]"/>
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Bio</label>
                        <input type="email" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]"/>
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Email</label>
                        <input type="text" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]"/>
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Password</label>
                        <input type="password" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]"/>
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-light">
                            Profile picture
                        </label>

                        <div class="max-h-20 max-w-20 rounded-full overflow-hidden flex items-center justify-center">
                            <div class="px-4">
                        <div id="image-preview" class="h-20 w-20 mx-auto text-center cursor-pointer">
                                <input id="upload" type="file" class="hidden" accept="image/*" />
                                <label for="upload" class="cursor-pointer h-full w-full block">
                                <img src="../src/asset/img/profile.svg" alt="gradient"
                                    class="h-full w-full object-cover" />
                                </label>
                            </div>
                            </div>
                        </div>
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-light">
                            Cover picture
                        </label>

                        <div class="relative inline-block text-left w-full">
                            <div class="relative w-full">
                            <!-- Tombol Select -->
                            <div id="customSelectButton"
                                onclick="toggleCustomSelect()"
                                class="w-full h-20 rounded-md overflow-hidden cursor-pointer relative">
                                <img id="customSelectPreview"
                                    src="../src/asset/img/gradient.png"
                                    alt="Selected"
                                    class="w-full h-full object-cover" />
                            </div>

                            <!-- Dropdown Options -->
                            <ul id="customSelectDropdown"
                                class="absolute left-0 z-10 w-full mt-1 rounded-md shadow-lg hidden max-h-48 overflow-y-scroll bg-accent p-2 space-y-2 hide-scrollbar">
                                <!-- Option Items -->
                                <li onclick="selectOption('../src/asset/img/gradient.png')"
                                    class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                <img src="../src/asset/img/gradient.png" class="w-full h-20 object-cover" alt="gradient 1" />
                                </li>
                                <li onclick="selectOption('../src/asset/img/gradient-2.png')"
                                    class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                <img src="../src/asset/img/gradient-2.png" class="w-full h-20 object-cover" alt="gradient 2" />
                                </li>
                                <li onclick="selectOption('../src/asset/img/gradient-3.png')"
                                    class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                <img src="../src/asset/img/gradient-3.png" class="w-full h-20 object-cover" alt="gradient 3" />
                                </li>
                                <li onclick="selectOption('../src/asset/img/gradient-4.png')"
                                    class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                <img src="../src/asset/img/gradient-4.png" class="w-full h-20 object-cover" alt="gradient 4" />
                                </li>
                                <li onclick="selectOption('../src/asset/img/gradient-5.png')"
                                    class="cursor-pointer hover:opacity-80 rounded overflow-hidden">
                                <img src="../src/asset/img/gradient-5.png" class="w-full h-20 object-cover" alt="gradient 5" />
                                </li>
                            </ul>

                            <!-- Hidden input for backend submission -->
                            <input type="hidden" id="customSelectInput" name="selected_option" value="../src/asset/img/gradient.png" />
                            </div>
                        </div>
                    </div>
                </div>
                <span class="flex justify-end">
                    <button type="submit" class="group bg-accent rounded-full h-20 w-20 flex items-center justify-center transition-transform duration-200 hover:scale-140">
                        <img src="../src/asset/icon/right-arrow.svg" alt="arrow" class="w-9 h-[46px]" />
                    </button>
                </span>
        </div>

        <script>
            const uploadInput = document.getElementById('upload');
            const filenameLabel = document.getElementById('filename');
            const imagePreview = document.getElementById('image-preview');

            // Check if the event listener has been added before
            let isEventListenerAdded = false;

            uploadInput.addEventListener('change', (event) => {
                const file = event.target.files[0];

                if (file) {
                filenameLabel.textContent = file.name;

                const reader = new FileReader();
                reader.onload = (e) => {
                    imagePreview.innerHTML =
                    `<img src="${e.target.result}" class="max-h-48 rounded-lg mx-auto" alt="Image preview" />`;
                    // Add event listener for image preview only once
                    if (!isEventListenerAdded) {
                    imagePreview.addEventListener('click', () => {
                        uploadInput.click();
                    });

                    isEventListenerAdded = true;
                    }
                };
                reader.readAsDataURL(file);
                } else {
                filenameLabel.textContent = '';
                imagePreview.innerHTML =
                    `<img class="h-full" src="../src/asset/img/profile.svg" alt="gradient">`;

                // Remove the event listener when there's no image
                imagePreview.removeEventListener('click', () => {
                    uploadInput.click();
                });

                isEventListenerAdded = false;
                }
            });

            uploadInput.addEventListener('click', (event) => {
                event.stopPropagation();
            });

            const selectButton = document.getElementById("customSelectButton");
            const dropdown = document.getElementById("customSelectDropdown");
            const previewImage = document.getElementById("customSelectPreview");
            const hiddenInput = document.getElementById("customSelectInput");

            function toggleCustomSelect() {
                dropdown.classList.toggle("hidden");
            }

            function selectOption(imageSrc) {
                previewImage.src = imageSrc;
                hiddenInput.value = imageSrc;
                dropdown.classList.add("hidden");
            }

            // Close dropdown if clicked outside
            document.addEventListener("click", function (event) {
                if (!selectButton.contains(event.target) && !dropdown.contains(event.target)) {
                dropdown.classList.add("hidden");
                }
            });
        </script>
  </body>
</html>
