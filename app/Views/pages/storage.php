<?php $this->extend('layouts/main'); ?>

<?php $this->section('content'); ?>
<div id="storagePage" class="fade-in">
    <!-- Header -->
    <div class="flex items-center justify-between mb-6">
        <div>
            <h3 class="text-xl font-bold text-slate-800">My Storage</h3>
            <p class="text-sm text-slate-500 mt-0.5">Manage your storage quota and rentals</p>
        </div>
        <button id="rentStorageBtn" class="bg-blue-600 hover:bg-blue-700 text-white px-5 py-2.5 rounded-lg text-sm font-semibold transition-colors flex items-center gap-2">
            <i class="fas fa-plus"></i> Rent More Storage
        </button>
    </div>

    <!-- Storage overview cards -->
    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Base Quota</p>
            <p class="text-xl font-bold text-slate-800" id="sBaseQuota">-- GB</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Rented Storage</p>
            <p class="text-xl font-bold text-blue-600" id="sRented">+-- GB</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Total Quota</p>
            <p class="text-xl font-bold text-slate-800" id="sTotalQuota">-- GB</p>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <p class="text-xs text-slate-500 mb-1">Remaining</p>
            <p class="text-xl font-bold text-green-600" id="sRemaining">-- GB</p>
        </div>
    </div>

    <!-- Usage bar -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100 mb-6">
        <div class="flex justify-between items-center mb-3">
            <h3 class="font-semibold text-slate-800">Storage Usage</h3>
            <span class="text-sm font-bold" id="sUsagePercent">--%</span>
        </div>
        <div class="w-full bg-slate-100 rounded-full h-4 mb-3">
            <div id="sProgressBar" class="h-4 rounded-full transition-all duration-500" style="width:0%"></div>
        </div>
        <div class="flex justify-between text-sm">
            <span class="text-slate-500"><span id="sUsed">--</span> GB used</span>
            <span class="text-slate-500"><span id="sTotal">--</span> GB total</span>
        </div>
        <div id="sWarningMsg" class="hidden mt-3 p-3 bg-amber-50 border border-amber-200 rounded-lg text-sm text-amber-700 flex items-center gap-2">
            <i class="fas fa-exclamation-triangle"></i>
            <span id="sWarningText">Storage usage above 80%</span>
        </div>
    </div>

    <!-- Rental history -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
        <h3 class="font-semibold text-slate-800 mb-4">Rental History</h3>
        <div id="rentalHistory">
            <div class="text-center py-8 text-slate-400">
                <i class="fas fa-spinner fa-spin text-xl mb-2"></i>
                <p class="text-sm">Loading...</p>
            </div>
        </div>
    </div>
</div>

<!-- Rent Modal -->
<div id="rentModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-md p-6">
        <div class="flex items-center justify-between mb-5">
            <h3 class="text-lg font-bold text-slate-800">Rent Additional Storage</h3>
            <button id="closeRentModal" class="w-8 h-8 bg-slate-100 hover:bg-slate-200 rounded-lg flex items-center justify-center text-slate-500 transition-colors">
                <i class="fas fa-times"></i>
            </button>
        </div>

        <div class="mb-5">
            <label class="block text-sm font-medium text-slate-700 mb-2">Amount (GB)</label>
            <input type="number" id="rentAmount" min="1" max="100" value="10"
                class="w-full px-4 py-3 border border-slate-300 rounded-lg text-lg font-bold text-center focus:outline-none focus:ring-2 focus:ring-blue-500">
            <div class="flex justify-between text-xs text-slate-400 mt-1">
                <span>1 GB</span>
                <span>100 GB max</span>
            </div>
        </div>

        <div class="bg-blue-50 rounded-lg p-4 mb-5">
            <div class="flex justify-between text-sm mb-1">
                <span class="text-slate-600">Price per GB</span>
                <span class="font-medium text-slate-800">$0.10 / GB / month</span>
            </div>
            <div class="flex justify-between text-sm font-bold">
                <span class="text-slate-700">Total Cost</span>
                <span class="text-blue-700" id="rentTotalCost">$1.00</span>
            </div>
        </div>

        <button id="confirmRentBtn"
            class="w-full bg-blue-600 hover:bg-blue-700 text-white font-semibold py-3 rounded-lg transition-colors flex items-center justify-center gap-2">
            <i class="fas fa-check"></i> Confirm Rental
        </button>
        <div id="rentError" class="hidden mt-3 p-3 bg-red-50 border border-red-200 rounded-lg text-sm text-red-600 text-center"></div>
    </div>
</div>
<?php $this->endSection(); ?>