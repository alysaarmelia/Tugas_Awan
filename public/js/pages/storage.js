// Storage page
(async function() {
    document.getElementById('pageTitle').textContent = 'My Storage';

    const modal       = document.getElementById('rentModal');
    const amountInput = document.getElementById('rentAmount');

    // Modal controls
    document.getElementById('rentStorageBtn')?.addEventListener('click', () => modal.classList.remove('hidden'));
    document.getElementById('closeRentModal')?.addEventListener('click', () => modal.classList.add('hidden'));
    modal?.addEventListener('click', e => { if (e.target === modal) modal.classList.add('hidden'); });

    // Live cost calculation
    amountInput?.addEventListener('input', () => {
        const cost = ((parseInt(amountInput.value) || 0) * 0.10).toFixed(2);
        document.getElementById('rentTotalCost').textContent = `$${cost}`;
    });

    async function loadStorage() {
        try {
            const res  = await API.get('/storage');
            const data = res.data || {};

            document.getElementById('sBaseQuota').textContent  = `${data.base_quota_gb ?? 0} GB`;
            document.getElementById('sRented').textContent      = `+${data.rented_gb ?? 0} GB`;
            document.getElementById('sTotalQuota').textContent  = `${data.total_quota_gb ?? 0} GB`;
            document.getElementById('sRemaining').textContent   = `${data.remaining_gb ?? 0} GB`;
            document.getElementById('sUsed').textContent        = `${data.used_gb ?? 0}`;
            document.getElementById('sTotal').textContent       = `${data.total_quota_gb ?? 0}`;
            document.getElementById('sUsagePercent').textContent = `${data.usage_percent ?? 0}%`;

            const pct   = data.usage_percent ?? 0;
            const bar   = document.getElementById('sProgressBar');
            bar.style.width = `${pct}%`;
            bar.className   = `h-4 rounded-full transition-all duration-500 ${UI.getProgressColor(pct)}`;

            const warn = document.getElementById('sWarningMsg');
            if (pct >= 80) {
                warn.classList.remove('hidden');
                document.getElementById('sWarningText').textContent = `Storage usage at ${pct}% — consider renting more space`;
            } else {
                warn.classList.add('hidden');
            }
        } catch (err) {
            Toast.show('Failed to load storage data', 'error');
        }
    }

    async function loadRentals() {
        const el = document.getElementById('rentalHistory');
        try {
            const res = await API.get('/storage/rentals');
            const rentals = res.data || [];

            if (rentals.length === 0) {
                el.innerHTML = `<div class="text-center py-8 text-slate-400">
                    <i class="fas fa-inbox text-3xl mb-2"></i>
                    <p class="text-sm">No rental history yet</p>
                </div>`;
                return;
            }

            el.innerHTML = `<table class="w-full text-sm">
                <thead>
                    <tr class="border-b border-slate-100">
                        <th class="text-left px-4 py-3 text-xs text-slate-500 font-semibold">Date</th>
                        <th class="text-left px-4 py-3 text-xs text-slate-500 font-semibold">Amount</th>
                        <th class="text-left px-4 py-3 text-xs text-slate-500 font-semibold">Price/GB</th>
                        <th class="text-left px-4 py-3 text-xs text-slate-500 font-semibold">Total Cost</th>
                    </tr>
                </thead>
                <tbody>
                    ${rentals.map(r => {
                        const date = r.created_at?.date ?? r.created_at ?? '';
                        return `<tr class="border-b border-slate-50 hover:bg-slate-50">
                            <td class="px-4 py-3 text-slate-600">${UI.formatDate(date)}</td>
                            <td class="px-4 py-3 font-medium text-slate-800">+${r.gb_amount} GB</td>
                            <td class="px-4 py-3 text-slate-600">$${parseFloat(r.price_per_gb).toFixed(2)} / GB</td>
                            <td class="px-4 py-3 font-medium text-green-700">$${parseFloat(r.gb_amount * r.price_per_gb).toFixed(2)}</td>
                        </tr>`;
                    }).join('')}
                </tbody>
            </table>`;
        } catch (err) {
            el.innerHTML = `<div class="text-center py-8 text-red-400">
                <i class="fas fa-exclamation-circle text-2xl mb-2"></i>
                <p class="text-sm">Failed to load rental history</p>
            </div>`;
        }
    }

    document.getElementById('confirmRentBtn')?.addEventListener('click', async () => {
        const amount     = parseInt(amountInput.value) || 0;
        const errorEl    = document.getElementById('rentError');
        const confirmBtn = document.getElementById('confirmRentBtn');

        if (amount < 1 || amount > 100) {
            errorEl.textContent = 'Please enter a number between 1 and 100 GB.';
            errorEl.classList.remove('hidden');
            return;
        }

        errorEl.classList.add('hidden');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            await API.post('/storage/rent', { amount_gb: amount });
            Toast.show(`Successfully rented ${amount} GB!`, 'success');
            modal.classList.add('hidden');
            amountInput.value = '10';
            document.getElementById('rentTotalCost').textContent = '$1.00';
            await loadStorage();
            await loadRentals();
        } catch (err) {
            errorEl.textContent = err.message;
            errorEl.classList.remove('hidden');
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Rental';
        }
    });

    // Load everything on page init
    await loadStorage();
    await loadRentals();
})();
