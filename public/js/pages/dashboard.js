// Dashboard page
(async function() {
    document.getElementById('pageTitle').textContent = 'Dashboard';

    const ICONS = {
        user_registered:          'fa-user-plus',
        subscription_selected:   'fa-credit-card',
        subscription_changed:    'fa-credit-card',
        storage_rented:          'fa-hard-drive',
        credentials_generated:    'fa-key',
        credentials_regenerated:  'fa-key',
        login:                   'fa-right-to-bracket',
        logout:                  'fa-right-from-bracket',
    };

    try {
        const [storageRes, subRes, credRes, logsRes] = await Promise.all([
            API.get('/storage'),
            API.get('/user/subscription'),
            API.get('/credentials'),
            API.get('/logs?limit=5'),
        ]);

        const storage = storageRes.data || {};
        const sub     = subRes.data;
        const creds   = credRes.data || {};
        const logs    = logsRes.data || [];
        const meta    = logsRes.meta || {};

        // ── Stat cards ────────────────────────────────────────────
        document.getElementById('dashQuota').textContent     = `${storage.total_quota_gb ?? 0} GB`;
        document.getElementById('dashQuotaUsed').textContent = `${storage.used_gb ?? 0} / ${storage.total_quota_gb ?? 0} GB used`;
        document.getElementById('dashUsed').textContent     = `${storage.used_gb ?? 0} GB`;
        document.getElementById('dashUsagePercent').textContent = `${storage.usage_percent ?? 0}%`;
        document.getElementById('dashTier').textContent     = (sub?.tier || 'Free').toUpperCase();
        document.getElementById('dashTierStatus').textContent = sub?.status || '--';
        document.getElementById('dashBucket').textContent   = creds?.bucket_name || '--';

        // Tier badge in header
        if (sub?.tier) {
            const badge = document.getElementById('tierBadge');
            badge.textContent = sub.tier.toUpperCase();
            badge.className   = `text-xs px-2 py-1 rounded-full font-semibold ${UI.getTierBadge(sub.tier)}`;
        }

        // ── Storage progress bar ────────────────────────────────
        const bar = document.getElementById('storageProgressBar');
        bar.style.width  = `${storage.usage_percent ?? 0}%`;
        bar.className   = `h-3 rounded-full transition-all duration-500 ${UI.getProgressColor(storage.usage_percent ?? 0)}`;
        document.getElementById('storageProgressLabel').textContent = `${storage.used_gb ?? 0} GB / ${storage.total_quota_gb ?? 0} GB`;
        document.getElementById('storageProgressPercent').textContent = `${storage.usage_percent ?? 0}%`;

        // ── Quota breakdown ──────────────────────────────────────
        document.getElementById('dashBaseQuota').textContent = `${storage.base_quota_gb ?? 0} GB`;
        document.getElementById('dashRented').textContent     = `+${storage.rented_gb ?? 0} GB`;
        document.getElementById('dashRemaining').textContent  = `${storage.remaining_gb ?? 0} GB`;

        // ── Recent activity ──────────────────────────────────────
        const el = document.getElementById('recentActivity');
        if (logs && logs.length > 0) {
            el.innerHTML = logs.map(log => {
                const date = log.created_at?.date ?? log.created_at ?? '';
                return `
                <div class="flex items-start gap-3 p-3 bg-slate-50 rounded-lg">
                    <div class="w-8 h-8 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0 mt-0.5">
                        <i class="fas ${ICONS[log.action] || 'fa-circle'} text-blue-600 text-xs"></i>
                    </div>
                    <div class="flex-1 min-w-0">
                        <div class="flex justify-between items-start">
                            <p class="text-sm font-medium text-slate-700">${log.action_label || log.action}</p>
                            <span class="text-xs text-slate-400 flex-shrink-0">${UI.formatDate(date)}</span>
                        </div>
                        <p class="text-xs text-slate-500 mt-0.5">${log.details || ''}</p>
                    </div>
                </div>`;
            }).join('');
        } else {
            el.innerHTML = `<div class="text-center py-8 text-slate-400">
                <i class="fas fa-inbox text-3xl mb-2"></i>
                <p class="text-sm">No activity yet</p>
            </div>`;
        }
    } catch (err) {
        Toast.show('Failed to load dashboard: ' + err.message, 'error');
    }
})();
