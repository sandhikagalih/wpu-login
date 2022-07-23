<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

    <!-- Sidebar - Brand -->
    <a class="sidebar-brand d-flex align-items-center justify-content-center" href="<?= base_url(); ?>">
        <div class="sidebar-brand-icon">
            <i class="fas fa-code"></i>
        </div>
        <div class="sidebar-brand-text mx-3">WPU Admin Pro</div>
    </a>

    <!-- Divider -->
    <hr class="sidebar-divider">

    <!-- Looping Menu -->
    <?php for ($i = 0; $i < count($menu); $i++) : ?>
        <div class="sidebar-heading">
            <?= $menu[$i]['menu']; ?>
        </div>

        <?php foreach ($subMenu[$i] as $sm) : ?>
            <li class="nav-item <?php if ($title == $sm['title']) echo "active" ?>">
                <a class="nav-link pb-0" href="<?= base_url($sm['url']); ?>">
                    <i class="<?= $sm['icon']; ?>"></i>
                    <span><?= $sm['title']; ?></span></a>
            </li>
        <?php endforeach; ?>

        <!-- Divider -->
        <hr class="sidebar-divider mt-3">
    <?php endfor; ?>

    <li class="nav-item">
        <a class="nav-link" href="<?= base_url('auth/logout'); ?>">
            <i class="fas fa-fw fa-sign-out-alt"></i>
            <span>Log out</span></a>
    </li>

    <!-- Divider -->
    <hr class="sidebar-divider d-none d-md-block">

    <!-- Sidebar Toggler (Sidebar) -->
    <div class="text-center d-none d-md-inline">
        <button class="rounded-circle border-0" id="sidebarToggle"></button>
    </div>

</ul>
<!-- End of Sidebar -->