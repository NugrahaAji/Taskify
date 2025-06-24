<nav class="h-[100px] bg-accent">
    <div class="container mx-auto flex justify-between">
        <div class="h-[100px] flex items-center">
            <img src="../src/asset/icon/logo-white.svg" alt="logo" />
            <ul class="ml-[152px] flex font-mont text-[18px] font-light tracking-[-1px] text-primary gap-9 h-[100px]">
                <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="dashboard.php">Dashboard</a></li>
                <li class="flex items-center border-b-4 border-accent hover:border-shade transition-all duration-300"><a href="workspace.php">Workspace</a></li>
            </ul>
        </div>
        <div class="flex items-center">
            <ul class="flex gap-5 hh-[100px] items-center">
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
            </ul>
        </div>
    </div>
</nav>
