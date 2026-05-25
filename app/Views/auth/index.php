<?php
// Standalone auth page — no layout wrapper needed
$is_auth_page = true;
?><!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>IaaS Portal</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <style>body { font-family: 'Inter', system-ui, sans-serif; }</style>
</head>
<body>
<!-- Toast notification -->
<div id="toast" class="fixed top-4 right-4 z-50 hidden">
    <div id="toastContent" class="bg-slate-800 text-white px-5 py-3 rounded-lg shadow-lg flex items-center gap-3 text-sm"></div>
</div>

<div class="min-h-screen flex items-center justify-center bg-gradient-to-br from-slate-900 via-blue-900 to-slate-900 px-4">
    <div class="w-full max-w-md">
        <!-- Logo -->
        <div class="text-center mb-8">
            <div class="w-14 h-14 bg-blue-600 rounded-2xl flex items-center justify-center mx-auto mb-4 shadow-lg shadow-blue-600/30">
                <i class="fas fa-cloud text-white text-2xl"></i>
            </div>
            <h1 class="text-2xl font-bold text-white">IaaS Portal</h1>
            <p class="text-slate-400 text-sm mt-1">Cloud Infrastructure Service Portal</p>
        </div>

        <!-- Card -->
        <div class="bg-white rounded-2xl shadow-2xl p-8">
            <!-- Tabs -->
            <div class="flex gap-1 mb-7 bg-slate-100 p-1 rounded-xl">
                <button id="tabLogin" class="flex-1 py-2 text-sm font-medium rounded-lg transition-all bg-white text-slate-800 shadow-sm">Sign In</button>
                <button id="tabRegister" class="flex-1 py-2 text-sm font-medium rounded-lg transition-all text-slate-500 hover:text-slate-700">Create Account</button>
            </div>

            <!-- Error/Success alerts -->
            <div id="authAlert" class="hidden mb-4 p-3 rounded-lg text-sm flex items-center gap-2"></div>

            <!-- Login Form -->
            <form id="loginForm" class="space-y-4">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" required placeholder="you@example.com"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="••••••••"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                </div>
                <div class="flex items-center">
                    <input type="checkbox" name="remember" id="rememberCheck" class="w-4 h-4 text-blue-600 border-slate-300 rounded focus:ring-blue-500">
                    <label for="rememberCheck" class="ml-2 text-sm text-slate-600">Keep me signed in</label>
                </div>
                <button type="submit" id="loginBtn"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                    Sign In <i class="fas fa-arrow-right text-xs"></i>
                </button>
            </form>

            <!-- Register Form -->
            <form id="registerForm" class="space-y-4 hidden">
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Username</label>
                    <input type="text" name="username" required minlength="3" maxlength="50" placeholder="johndoe"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Email Address</label>
                    <input type="email" name="email" required placeholder="you@example.com"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Password</label>
                    <input type="password" name="password" required placeholder="Min 8 chars, 1 uppercase, 1 number"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                    <p class="text-xs text-slate-400 mt-1">Min 8 chars, 1 uppercase, 1 number</p>
                </div>
                <div>
                    <label class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password</label>
                    <input type="password" name="confirm_password" required placeholder="••••••••"
                        class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent transition-shadow">
                </div>
                <button type="submit" id="registerBtn"
                    class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-2.5 rounded-lg transition-colors text-sm flex items-center justify-center gap-2">
                    Create Account <i class="fas fa-user-plus text-xs"></i>
                </button>
            </form>
        </div>

        <p class="text-center text-slate-500 text-xs mt-6">
            &copy; <?= date('Y') ?> IaaS Portal — Powered by MiniStack
        </p>
    </div>
</div>

<script type="module" src="/js/app.js"></script>
</body>
</html>