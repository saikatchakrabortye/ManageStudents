<div class="sidebar" id="sidebar">
    <div class="profile-section" id="profileSection"></div>
    <!-- DASHBOARD -->
    <?php if (!empty($sidebar['dashboard'])): ?>
        <a href="<?= site_url($sidebar['dashboard']->routeKey); ?>"
           class="sidebar-item <?= is_active_page($sidebar['dashboard']->routeKey) ? 'active' : ''; ?>">
            <i class="fas <?= $sidebar['dashboard']->iconName; ?>"></i>
            <span><?= $sidebar['dashboard']->name; ?></span>
        </a>
    <?php endif; ?>

    <!-- GROUPED MENUS -->
    <?php if (!empty($sidebar['groups'])): ?>
        <?php foreach ($sidebar['groups'] as $group_name => $group_items): ?>

            <?php
                // SAFE GROUP ID (critical fix)
                $group_id = strtolower(preg_replace('/[^a-zA-Z0-9]+/', '-', trim($group_name)));
            ?>

            <div class="menu-group">

                <!-- HEADER -->
                <div class="group-header"
                     id="header-<?= $group_id; ?>"
                     data-group="<?= $group_id; ?>">
                    <i class="fas fa-chevron-right group-arrow"></i>
                    <span class="group-title"><?= $group_name; ?></span>
                </div>

                <!-- SUBMENU -->
                <div class="submenu" id="group-<?= $group_id; ?>">
                    <?php foreach ($group_items as $item): ?>
                        <a href="<?= site_url($item->routeKey); ?>"
                           class="submenu-item <?= is_active_page($item->routeKey) ? 'active' : ''; ?>">
                            <i class="fas <?= $item->iconName; ?>"></i>
                            <span><?= $item->name; ?></span>
                        </a>
                    <?php endforeach; ?>
                </div>

            </div>

        <?php endforeach; ?>
    <?php endif; ?>

    <a href="<?= base_url('Login/logout'); ?>">
        <button style="margin:20px;">Logout</button>
    </a>
</div>

<script>
document.addEventListener('DOMContentLoaded', function () {

    const headers = document.querySelectorAll('.group-header');

    headers.forEach(header => {

        const groupId = header.dataset.group;
        const submenu = document.getElementById('group-' + groupId);
        const arrow = header.querySelector('.group-arrow');

        // Restore saved state
        const isExpanded = localStorage.getItem('sidebar-group-' + groupId) === 'true';
        if (isExpanded) {
            expand(submenu, header, arrow);
        }

        header.addEventListener('click', function () {
            const expanded = submenu.classList.contains('expanded');
            expanded ? collapse(submenu, header, arrow) : expand(submenu, header, arrow);
            localStorage.setItem('sidebar-group-' + groupId, !expanded);
        });
    });

    function expand(menu, header, arrow) {
        menu.classList.add('expanded');
        menu.style.maxHeight = menu.scrollHeight + 'px';
        header.classList.add('active');
        arrow.classList.replace('fa-chevron-right', 'fa-chevron-down');
    }

    function collapse(menu, header, arrow) {
        menu.style.maxHeight = menu.scrollHeight + 'px'; // set current height
        requestAnimationFrame(() => {
            menu.style.maxHeight = '0px';
        });
        menu.classList.remove('expanded');
        header.classList.remove('active');
        arrow.classList.replace('fa-chevron-down', 'fa-chevron-right');
    }

});

function getInitials(fullName) {
            return fullName ? fullName.split(' ').map(n => n[0]).join('').toUpperCase().substring(0, 2) : '';
        }

        async function loadProfileData() {
            try {
                const response = await fetch('http://localhost/ManageStudents/Users/getProfileData');
                const user = await response.json();
                
                const profilePic = user.profilePic ? 
                    `<img src="http://localhost/ManageStudents/uploads/profile_pics/users/${user.profilePic}" class="profile-pic">` :
                    `<div class="profile-pic-fallback">${getInitials(user.name)}</div>`;
                
                document.getElementById('profileSection').innerHTML = `
                    ${profilePic}
                    <div class="profile-name">${user.name || ''}</div>
                    <div class="profile-email">${user.email || ''}</div>
                    <div class="profile-role">${user.role || ''}</div>
                `;
            } catch (error) {
                document.getElementById('profileSection').innerHTML = `
                    <div class="profile-pic-fallback">?</div>
                    <div class="profile-name">User</div>
                    <div class="profile-email">Loading...</div>
                    <div class="profile-role">User</div>
                `;
            }
        }

        document.addEventListener('DOMContentLoaded', loadProfileData);
</script>
