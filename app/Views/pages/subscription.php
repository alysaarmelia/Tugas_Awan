<div id="subscriptionPage" class="fade-in">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-xl font-bold text-slate-800">Subscription</h3>
        <p class="text-sm text-slate-500 mt-0.5">Choose a plan that fits your needs</p>
    </div>

    <!-- Current subscription -->
    <div id="currentSubBanner" class="bg-blue-50 border border-blue-200 rounded-xl p-5 mb-6 flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                <i class="fas fa-check-circle text-blue-600"></i>
            </div>
            <div>
                <p class="text-sm text-blue-600 font-medium">Current Plan</p>
                <p class="text-lg font-bold text-blue-900" id="currentTierLabel">--</p>
            </div>
        </div>
        <div class="text-right">
            <p class="text-2xl font-bold text-blue-900" id="currentTierPrice">--</p>
            <p class="text-xs text-blue-500">per month</p>
        </div>
    </div>

    <!-- Tier cards -->
    <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6" id="tiersGrid">
        <div class="flex justify-center items-center py-12 text-slate-400"><i class="fas fa-spinner fa-spin text-2xl"></i></div>
    </div>

    <!-- Subscription features -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
        <h3 class="font-semibold text-slate-800 mb-4">All Plans Include</h3>
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-3">
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                <i class="fas fa-check text-green-500"></i>
                <span class="text-sm text-slate-600">Dashboard monitoring</span>
            </div>
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                <i class="fas fa-check text-green-500"></i>
                <span class="text-sm text-slate-600">Activity logs</span>
            </div>
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                <i class="fas fa-check text-green-500"></i>
                <span class="text-sm text-slate-600">API credentials</span>
            </div>
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                <i class="fas fa-check text-green-500"></i>
                <span class="text-sm text-slate-600">Storage quota management</span>
            </div>
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                <i class="fas fa-check text-green-500"></i>
                <span class="text-sm text-slate-600">MiniStack bucket</span>
            </div>
            <div class="flex items-center gap-3 p-3 bg-slate-50 rounded-lg">
                <i class="fas fa-check text-green-500"></i>
                <span class="text-sm text-slate-600">Secure JWT authentication</span>
            </div>
        </div>
    </div>
</div>