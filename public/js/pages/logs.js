// Activity Logs page
(async function() {
    document.getElementById('pageTitle').textContent = 'Activity Logs';

    let currentPage    = 1;
    const LIMIT        = 20;
    let currentFilter  = '';

    const STATUS_CLASSES = {
        completed: 'bg-green-100 text-green-700',
        failed:    'bg-red-100 text-red-700',
        pending:   'bg-amber-100 text-amber-700',
    };

    const ACTION_ICONS = {
        user_registered:          'fa-user-plus',
        subscription_selected:    'fa-credit-card',
        subscription_changed:    'fa-credit-card',
        storage_rented:           'fa-hard-drive',
        credentials_generated:   'fa-key',
        credentials_regenerated: 'fa-key',
        login:                   'fa-right-to-bracket',
        logout:                  'fa-right-from-bracket',
    };

    const ACTION_LABELS = {
        user_registered:          'Account Registered',
        subscription_selected:    'Subscription Selected',
        subscription_changed:    'Subscription Changed',
        storage_rented:          'Storage Rented',
        credentials_generated:   'Credentials Generated',
        credentials_regenerated: 'Credentials Regenerated',
        login:                   'Login',
        logout:                  'Logout',
    };

    async function loadLogs() {
        const tbody = document.getElementById('logsTableBody');
        tbody.innerHTML = `<tr><td colspan="4" class="text-center py-12 text-slate-400">
            <i class="fas fa-spinner fa-spin text-xl mb-2"></i><p class="text-sm">Loading...</p>
        </td></tr>`;

        try {
            const path = `/logs?page=${currentPage}&limit=${LIMIT}${currentFilter ? '&action_type=' + currentFilter : ''}`;
            const res  = await API.get(path);
            const logs  = res.data;
            const meta  = res.meta;

            if (!logs || logs.length === 0) {
                tbody.innerHTML = `<tr><td colspan="4" class="text-center py-12 text-slate-400">
                    <i class="fas fa-inbox text-3xl mb-2"></i><p class="text-sm">No logs found</p>
                </td></tr>`;
            } else {
                tbody.innerHTML = logs.map(log => `
                    <tr class="border-b border-slate-50 hover:bg-slate-50 transition-colors">
                        <td class="px-5 py-3.5 text-sm text-slate-600 whitespace-nowrap">
                            ${UI.formatDate(log.created_at)}
                        </td>
                        <td class="px-5 py-3.5">
                            <div class="flex items-center gap-2">
                                <div class="w-7 h-7 bg-blue-100 rounded-full flex items-center justify-center flex-shrink-0">
                                    <i class="fas ${ACTION_ICONS[log.action] || 'fa-circle'} text-blue-600 text-xs"></i>
                                </div>
                                <span class="text-sm font-medium text-slate-700">
                                    ${log.action_label || ACTION_LABELS[log.action] || log.action}
                                </span>
                            </div>
                        </td>
                        <td class="px-5 py-3.5 text-sm text-slate-600">${log.details || '—'}</td>
                        <td class="px-5 py-3.5">
                            <span class="text-xs px-2 py-1 rounded-full font-medium
                                ${STATUS_CLASSES[log.status] || 'bg-slate-100 text-slate-600'}">
                                ${log.status || '--'}
                            </span>
                        </td>
                    </tr>`).join('');
            }

            document.getElementById('logsCount').textContent =
                `Showing ${logs?.length || 0} of ${meta.total} entries`;
            document.getElementById('prevPage').disabled = meta.current_page <= 1;
            document.getElementById('nextPage').disabled = meta.current_page >= meta.page_count;

        } catch (err) {
            tbody.innerHTML = `<tr><td colspan="4" class="text-center py-12 text-red-400">
                <i class="fas fa-exclamation-triangle text-xl mb-2"></i>
                <p class="text-sm">Failed to load logs</p>
            </td></tr>`;
        }
    }

    // Populate action type filter on first load
    const filterEl = document.getElementById('filterActionType');
    if (filterEl && filterEl.options.length <= 1) {
        Object.entries(ACTION_LABELS).forEach(([val, label]) => {
            const opt = document.createElement('option');
            opt.value = val;
            opt.textContent = label;
            filterEl.appendChild(opt);
        });
    }

    document.getElementById('applyLogFilter')?.addEventListener('click', () => {
        currentFilter = document.getElementById('filterActionType')?.value || '';
        currentPage   = 1;
        loadLogs();
    });

    document.getElementById('prevPage')?.addEventListener('click', () => {
        if (currentPage > 1) { currentPage--; loadLogs(); }
    });

    document.getElementById('nextPage')?.addEventListener('click', () => {
        currentPage++; loadLogs();
    });

    await loadLogs();
})();
