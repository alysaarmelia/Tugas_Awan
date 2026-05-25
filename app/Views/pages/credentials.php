<div id="credentialsPage" class="fade-in">
    <!-- Header -->
    <div class="mb-6">
        <h3 class="text-xl font-bold text-slate-800">API Credentials</h3>
        <p class="text-sm text-slate-500 mt-0.5">Your Access Key and Secret Key for the IaaS API</p>
    </div>

    <!-- Info banner -->
    <div id="credBanner" class="bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3">
        <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
        <div class="text-sm text-blue-800">
            <p class="font-medium">Store these credentials securely</p>
            <p class="text-blue-700 mt-0.5">Your Secret Key is only shown once. Copy it now and store it safely. You can regenerate credentials anytime, but the old ones will be invalidated.</p>
        </div>
    </div>

    <!-- Credentials cards -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <!-- Access Key -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-blue-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-key text-blue-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-800">Access Key</h4>
                        <p class="text-xs text-slate-400">Can be revealed on this page</p>
                    </div>
                </div>
                <button id="revealAK" class="text-blue-600 hover:text-blue-700 text-xs font-medium flex items-center gap-1">
                    <i class="fas fa-eye"></i> Reveal
                </button>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 font-mono text-sm break-all" id="akValue">
                <i class="fas fa-spinner fa-spin text-slate-400"></i> Loading...
            </div>
            <button id="copyAK" class="mt-3 w-full bg-slate-100 hover:bg-slate-200 text-slate-700 py-2 rounded-lg text-sm font-medium transition-colors flex items-center justify-center gap-2">
                <i class="fas fa-copy"></i> Copy Access Key
            </button>
        </div>

        <!-- Secret Key -->
        <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center gap-3">
                    <div class="w-10 h-10 bg-orange-100 rounded-lg flex items-center justify-center">
                        <i class="fas fa-lock text-orange-600"></i>
                    </div>
                    <div>
                        <h4 class="font-semibold text-slate-800">Secret Key</h4>
                        <p class="text-xs text-slate-400">Shown only once after regen</p>
                    </div>
                </div>
            </div>
            <div class="bg-slate-50 rounded-lg p-4 font-mono text-sm break-all" id="skValue">
                <i class="fas fa-spinner fa-spin text-slate-400"></i> Loading...
            </div>
            <button id="copySK" class="mt-3 w-full bg-slate-100 text-slate-400 py-2 rounded-lg text-sm font-medium flex items-center justify-center gap-2 opacity-50 cursor-not-allowed" disabled>
                <i class="fas fa-copy"></i> Copy Secret Key <span class="text-xs">(only after regen)</span>
            </button>
        </div>
    </div>

    <!-- Bucket info + Timestamps -->
    <div class="grid grid-cols-1 lg:grid-cols-2 gap-6 mb-6">
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <h4 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
                <i class="fas fa-database text-slate-400"></i> Bucket Name
            </h4>
            <div class="bg-slate-50 rounded-lg p-3">
                <code class="text-sm font-mono text-slate-700" id="credBucket">--</code>
            </div>
        </div>
        <div class="bg-white rounded-xl p-5 shadow-sm border border-slate-100">
            <h4 class="font-semibold text-slate-700 mb-3 flex items-center gap-2">
                <i class="fas fa-clock text-slate-400"></i> Timestamps
            </h4>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-slate-500">Created</span>
                    <span class="text-slate-700" id="credCreated">--</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-slate-500">Last Regenerated</span>
                    <span class="text-slate-700" id="credRegenerated">Never</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Regenerate -->
    <div class="bg-white rounded-xl p-6 shadow-sm border border-slate-100">
        <div class="flex items-start justify-between">
            <div>
                <h4 class="font-semibold text-slate-800 mb-1">Regenerate Credentials</h4>
                <p class="text-sm text-slate-500">This will invalidate your current Access Key and Secret Key. Any applications using the old keys will stop working.</p>
            </div>
            <button id="regenerateBtn"
                class="ml-4 bg-red-50 hover:bg-red-100 text-red-600 px-4 py-2 rounded-lg text-sm font-medium transition-colors flex items-center gap-2 border border-red-200 flex-shrink-0">
                <i class="fas fa-rotate"></i> Regenerate
            </button>
        </div>
    </div>

    <!-- Regenerate confirm modal -->
    <div id="regenModal" class="hidden fixed inset-0 bg-black/50 flex items-center justify-center z-50 px-4">
        <div class="bg-white rounded-2xl shadow-2xl w-full max-w-sm p-6">
            <div class="w-12 h-12 bg-red-100 rounded-full flex items-center justify-center mx-auto mb-4">
                <i class="fas fa-exclamation-triangle text-red-600 text-lg"></i>
            </div>
            <h3 class="text-lg font-bold text-slate-800 text-center mb-2">Regenerate Credentials?</h3>
            <p class="text-sm text-slate-500 text-center mb-6">Your current keys will be permanently invalidated.</p>
            <div class="flex gap-3">
                <button id="cancelRegen" class="flex-1 bg-slate-100 hover:bg-slate-200 text-slate-700 py-2.5 rounded-lg font-medium transition-colors">Cancel</button>
                <button id="confirmRegen" class="flex-1 bg-red-600 hover:bg-red-700 text-white py-2.5 rounded-lg font-medium transition-colors">Yes, Regenerate</button>
            </div>
        </div>
    </div>
</div>