<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?? 'IaaS Portal' ?></title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { font-family: 'Inter', system-ui, sans-serif; }
        .sidebar-link.active { background: #1e40af; color: #fff; }
        .progress-green { background: #22c55e; }
        .progress-yellow { background: #eab308; }
        .progress-red { background: #ef4444; }
        .fade-in { animation: fadeIn 0.3s ease-in; }
        @keyframes fadeIn { from { opacity: 0; transform: translateY(4px); } to { opacity: 1; transform: translateY(0); } }
        .spinner { border: 3px solid #e5e7eb; border-top-color: #1d4ed8; border-radius: 50%; width: 24px; height: 24px; animation: spin 0.7s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }
    </style>
</head>
<body class="bg-slate-50 min-h-screen">

<!-- Toast notification -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div id="toastContent" class="bg-slate-800 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 text-sm fade-in"></div>
</div>

<?php if (isset($is_auth_page) && $is_auth_page): ?>
    <!-- Auth pages — use renderSection so auth/index.php section renders -->
    <?= $this->renderSection('content') ?>
<?php else: ?>
    <!-- Main layout with sidebar -->
    <div class="flex h-screen overflow-hidden">
        <!-- Sidebar -->
        <aside class="w-64 bg-slate-900 text-white flex flex-col flex-shrink-0">
            <div class="p-5 border-b border-slate-700">
                <div class="flex items-center gap-3">
                    <div class="w-9 h-9 bg-blue-600 rounded-lg flex items-center justify-center">
                        <i class="fas fa-cloud text-white"></i>
                    </div>
                    <div>
                        <h1 class="font-bold text-base leading-tight">IaaS Portal</h1>
                        <p class="text-slate-400 text-xs">Cloud Infrastructure</p>
                    </div>
                </div>
            </div>

            <nav class="flex-1 p-3 space-y-1">
                <a href="/dashboard" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm hover:bg-slate-800 transition-colors" data-page="dashboard">
                    <i class="fas fa-gauge-high w-4 text-center"></i> Dashboard
                </a>
                <a href="/storage" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm hover:bg-slate-800 transition-colors" data-page="storage">
                    <i class="fas fa-hard-drive w-4 text-center"></i> My Storage
                </a>
                <a href="/credentials" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm hover:bg-slate-800 transition-colors" data-page="credentials">
                    <i class="fas fa-key w-4 text-center"></i> Credentials
                </a>
                <a href="/subscription" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm hover:bg-slate-800 transition-colors" data-page="subscription">
                    <i class="fas fa-credit-card w-4 text-center"></i> Subscription
                </a>
                <a href="/logs" class="sidebar-link flex items-center gap-3 px-3 py-2.5 rounded-lg text-sm hover:bg-slate-800 transition-colors" data-page="logs">
                    <i class="fas fa-list-check w-4 text-center"></i> Activity Logs
                </a>
            </nav>

            <div class="p-4 border-t border-slate-700">
                <div class="flex items-center gap-3 mb-3">
                    <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-sm font-semibold" id="sidebarAvatar">U</div>
                    <div class="flex-1 min-w-0">
                        <p class="text-sm font-medium truncate" id="sidebarUsername">User</p>
                        <p class="text-slate-400 text-xs truncate" id="sidebarEmail">user@example.com</p>
                    </div>
                </div>
                <button id="logoutBtn" class="w-full flex items-center justify-center gap-2 bg-slate-800 hover:bg-slate-700 text-slate-300 text-sm py-2 rounded-lg transition-colors">
                    <i class="fas fa-right-from-bracket"></i> Logout
                </button>
            </div>
        </aside>

        <!-- Main content -->
        <main class="flex-1 overflow-y-auto">
            <header class="bg-white border-b border-slate-200 px-6 py-4 flex items-center justify-between sticky top-0 z-10">
                <h2 class="text-lg font-semibold text-slate-800" id="pageTitle">Dashboard</h2>
                <div class="flex items-center gap-3">
                    <span class="text-xs text-slate-400 hidden sm:inline" id="tierBadge"></span>
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center text-blue-700 text-sm font-semibold" id="headerAvatar">U</div>
                </div>
            </header>
            <div class="p-6" id="pageContent">
                <?= $pageContent ?? '' ?>
            </div>
        </main>
    </div>
<?php endif; ?>

<script type="module" src="<?= base_url('js/app.js') ?>"></script>
</body>
</html>