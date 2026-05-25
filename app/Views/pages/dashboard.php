<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div id="dashboardPage" class="fade-in">
    <!-- Stats cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-slate-500">Storage Quota</span>
                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-hard-drive text-blue-600 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800" id="dashQuota">-- GB</p>
            <p class="text-xs text-slate-400 mt-1" id="dashQuotaUsed">-- / -- GB used</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-slate-500">Used Storage</span>
                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-chart-pie text-green-600 text-sm"></i>
                </div>
            </div>
            <p class="text-2xl font-bold text-slate-800" id="dashUsed">-- GB</p>
            <p class="text-xs text-slate-400 mt-1" id="dashUsagePercent">--% used</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-slate-500">Subscription</span>
                <div class="w-8 h-8 bg-purple-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-crown text-purple-600 text-sm"></i>
                </div>
            </div>
            <p class="text-lg font-bold text-slate-800" id="dashTier">--</p>
            <p class="text-xs text-slate-400 mt-1" id="dashTierStatus">--</p>
        </div>

        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-3">
                <span class="text-sm text-slate-500">Bucket</span>
                <div class="w-8 h-8 bg-orange-50 rounded-lg flex items-center justify-center">
                    <i class="fas fa-database text-orange-600 text-sm"></i>
                </div>
            </div>
            <p class="text-sm font-bold text-slate-800 truncate" id="dashBucket">--</p>
            <p class="text-xs text-slate-400 mt-1">MiniStack bucket</p>
        </div>
    </div>

    <!-- Storage progress + quick actions -->
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-4 mb-6">
        <!-- Storage progress -->
        <div class="lg:col-span-2 bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <h3 class="font-semibold text-slate-800">Storage Usage</h3>
                <a href="#/storage" class="text-blue-600 text-sm hover:underline">Manage →</a>
            </div>
            <div id="storageProgressContainer">
                <div class="flex justify-between text-sm mb-2">
                    <span class="text-slate-600" id="storageProgressLabel">-- GB / -- GB</span>
                    <span class="font-semibold" id="storageProgressPercent">--%</span>
                </div>
                <div class="w-full bg-slate-100 rounded-full h-3">
                    <div id="storageProgressBar" class="h-3 rounded-full transition-all duration-500" style="width:0%"></div>
                </div>
            </div>
            <div class="grid grid-cols-3 mt-4 gap-4">
                <div class="text-center p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500 mb-1">Base Quota</p>
                    <p class="text-sm font-bold text-slate-700" id="dashBaseQuota">-- GB</p>
                </div>
                <div class="text-center p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500 mb-1">Rented</p>
                    <p class="text-sm font-bold text-slate-700" id="dashRented">+-- GB</p>
                </div>
                <div class="text-center p-3 bg-slate-50 rounded-lg">
                    <p class="text-xs text-slate-500 mb-1">Remaining</p>
                    <p class="text-sm font-bold text-green-600" id="dashRemaining">-- GB</p>
                </div>
            </div>
        </div>

        <!-- Quick actions -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <h3 class="font-semibold text-slate-800 mb-4">Quick Actions</h3>
            <div class="space-y-3">
                <a href="#/storage" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-blue-300 hover:bg-blue-50 transition-colors">
                    <div class="w-9 h-9 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-plus text-blue-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-700">Rent Storage</p>
                        <p class="text-xs text-slate-400">Expand your quota</p>
                    </div>
                </a>
                <a href="#/credentials" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-orange-300 hover:bg-orange-50 transition-colors">
                    <div class="w-9 h-9 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-key text-orange-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-700">View Credentials</p>
                        <p class="text-xs text-slate-400">Access Key & Secret Key</p>
                    </div>
                </a>
                <a href="#/logs" class="flex items-center gap-3 p-3 rounded-lg border border-slate-200 hover:border-purple-300 hover:bg-purple-50 transition-colors">
                    <div class="w-9 h-9 bg-purple-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-list text-purple-600"></i>
                    </div>
                    <div>
                        <p class="text-sm font-medium text-slate-700">Activity Logs</p>
                        <p class="text-xs text-slate-400">View recent actions</p>
                    </div>
                </a>
            </div>
        </div>
    </div>

    <!-- Recent activity -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-center justify-between mb-4">
            <h3 class="font-semibold text-slate-800">Recent Activity</h3>
            <a href="#/logs" class="text-blue-600 text-sm hover:underline">View all →</a>
        </div>
        <div id="recentActivity">
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-spinner fa-spin text-2xl mb-2"></i>
                <p class="text-sm">Loading activity...</p>
            </div>
        </div>
    </div>
</div>
<?php $this->endSection(); ?>