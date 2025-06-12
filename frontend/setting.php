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
    <title>Taskify | Setting</title>
  </head>
  <body class="bg-primary">
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
                <div class="flex mt-[52px] w-full items-center">
                    <div class="">
                        <h1 class="font-mont text-[32px] font-semibold tracking-[-2.88px] text-accent">Nugraha Aji Saputra</h1>
                        <h1 class="font-mont text-[16px] font-normal tracking-[-1.28px] text-inactive -mt-2">ajiselebew@gmail.com</h1>
                    </div>
                </div>
            </div>
            <div class="mt-12">
                <div class="flex items-center justify-between">
                    <h1 class="font-mont text-2xl text-accent font-semibold tracking-[-1.92px]">Account info</h1>
                    <a href="edit-profile.php" class="cursor-pointer">
                        <img class="" src="../src/asset/icon/edit.svg" alt="edit">
                    </a>
                </div>
                <div class="grid grid-cols-2 gap-x-[60px] gap-y-[18px] mt-4">
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Username</label>
                        <input type="text" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]" disabled />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Bio</label>
                        <input type="email" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]" disabled />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Email</label>
                        <input type="text" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]" disabled />
                    </div>
                    <div>
                        <label class="block font-dm text-[18px] text-shade tracking-[-1.26px] mb-1 font-[300px]">Password</label>
                        <input type="password" class="w-full border-b border-black outline-none focus:ring-0 h-8  text-[18px]" disabled />
                    </div>
                </div>
                <div class="space-y-3 mt-6">
                    <button class="py-1 pr-2 flex cursor-pointer">
                        <img src="../src/asset/icon/logout.svg" alt="">
                        <span class="font-mont text-[18px] font-normal tracking-[-1.26px] text-warn">Log out</span>
                    </button>
                    <button class="py-1 pr-2 flex cursor-pointer">
                        <img src="../src/asset/icon/delete.svg" alt="">
                        <span class="font-mont text-[18px] font-normal tracking-[-1.26px] text-warn">Delete account</span>
                    </button>
                </div>
            </div>
        </div>
  </body>
</html>
