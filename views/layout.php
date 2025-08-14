<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($pageTitle ?? 'BC Attendance System') ?></title>
    
    <!-- Material Design 3 CSS -->
    <link rel="stylesheet" href="/assets/css/style.css">
    
    <!-- Material Icons -->
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Favicon -->
    <link rel="icon" type="image/x-icon" href="/assets/icons/favicon.ico">
</head>
<body>
    <!-- Top Navigation Bar -->
    <header class="top-nav">
        <div class="nav-container">
            <div class="nav-left">
                <button class="menu-toggle" id="menuToggle">
                    <span class="material-icons">menu</span>
                </button>
                <h1 class="app-title">BC Attendance System</h1>
            </div>
            
            <div class="nav-center">
                <?php if (isset($showDatePicker) && $showDatePicker): ?>
                <div class="date-picker">
                    <input type="date" id="attendanceDate" value="<?= date('Y-m-d') ?>" class="date-input">
                </div>
                <?php endif; ?>
            </div>
            
            <div class="nav-right">
                <div class="search-container">
                    <input type="text" id="globalSearch" placeholder="Search..." class="search-input">
                    <span class="material-icons search-icon">search</span>
                </div>
                
                <?php if (isset($user) && $user): ?>
                <div class="user-menu">
                    <button class="user-button" id="userMenuToggle">
                        <span class="material-icons">account_circle</span>
                        <span class="user-name"><?= htmlspecialchars($user['full_name'] ?: $user['username']) ?></span>
                        <span class="material-icons">arrow_drop_down</span>
                    </button>
                    <div class="user-dropdown" id="userDropdown">
                        <a href="/settings" class="dropdown-item">
                            <span class="material-icons">settings</span>
                            Settings
                        </a>
                        <a href="/logout" class="dropdown-item">
                            <span class="material-icons">logout</span>
                            Logout
                        </a>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Side Navigation -->
    <nav class="side-nav" id="sideNav">
        <div class="nav-header">
            <h2>Navigation</h2>
            <button class="nav-close" id="navClose">
                <span class="material-icons">close</span>
            </button>
        </div>
        
        <ul class="nav-menu">
            <li class="nav-item">
                <a href="/dashboard" class="nav-link <?= $currentPage === 'dashboard' ? 'active' : '' ?>">
                    <span class="material-icons">dashboard</span>
                    <span>Dashboard</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/attendance/mark" class="nav-link <?= $currentPage === 'attendance' ? 'active' : '' ?>">
                    <span class="material-icons">how_to_reg</span>
                    <span>Mark Attendance</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/attendance" class="nav-link <?= $currentPage === 'attendance-list' ? 'active' : '' ?>">
                    <span class="material-icons">list_alt</span>
                    <span>Attendance List</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/reports" class="nav-link <?= $currentPage === 'reports' ? 'active' : '' ?>">
                    <span class="material-icons">assessment</span>
                    <span>Reports</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/constituencies" class="nav-link <?= $currentPage === 'master-data' ? 'active' : '' ?>">
                    <span class="material-icons">storage</span>
                    <span>Master Data</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/import" class="nav-link <?= $currentPage === 'import' ? 'active' : '' ?>">
                    <span class="material-icons">upload_file</span>
                    <span>Import/Export</span>
                </a>
            </li>
            <li class="nav-item">
                <a href="/settings" class="nav-link <?= $currentPage === 'settings' ? 'active' : '' ?>">
                    <span class="material-icons">settings</span>
                    <span>Settings</span>
                </a>
            </li>
        </ul>
    </nav>

    <!-- Main Content -->
    <main class="main-content" id="mainContent">
        <!-- Flash Messages -->
        <?php if (isset($flash) && !empty($flash)): ?>
        <div class="flash-messages">
            <?php foreach ($flash as $type => $message): ?>
            <div class="alert alert-<?= $type ?> alert-dismissible">
                <span class="alert-message"><?= htmlspecialchars($message) ?></span>
                <button class="alert-close" onclick="this.parentElement.remove()">
                    <span class="material-icons">close</span>
                </button>
            </div>
            <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- Page Content -->
        <div class="content-wrapper">
            <?= $content ?>
        </div>
    </main>

    <!-- Overlay for mobile menu -->
    <div class="nav-overlay" id="navOverlay"></div>

    <!-- JavaScript -->
    <script src="/assets/js/app.js"></script>
    <script src="/assets/js/table.js"></script>
    
    <?php if (isset($pageScripts)): ?>
        <?php foreach ($pageScripts as $script): ?>
        <script src="<?= $script ?>"></script>
        <?php endforeach; ?>
    <?php endif; ?>
</body>
</html>
