// Storage page
(async function() {
    document.getElementById('pageTitle').textContent = 'My Storage';

    const modal      = document.getElementById('rentModal');
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
            const res    = await API.get('/storage');
            const data   = res.data;
            const pct    = data.usage_percent;
            const color  = UI.getProgressColor(pct);

            document.getElementById('sBaseQuota').textContent  = `${data.base_quota_gb} GB`;
            document.getElementById('sRented').textContent      = `+${data.rented_gb} GB`;
            document.getElementById('sTotalQuota').textContent  = `${data.total_quota_gb} GB`;
            document.getElementById('sRemaining').textContent   = `${data.remaining_gb} GB`;
            document.getElementById('sUsed').textContent        = data.used_gb;
            document.getElementById('sTotal').textContent       = data.total_quota_gb;
            document.getElementById('sUsagePercent').textContent = `${pct}%`;

            const bar = document.getElementById('sProgressBar');
            bar.style.width = `${pct}%`;
            bar.className   = `h-4 rounded-full transition-all duration-500 ${color}`;

            // Warning
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

    document.getElementById('confirmRentBtn')?.addEventListener('click', async () => {
        const amount   = parseInt(amountInput.value) || 0;
        const errorEl  = document.getElementById('rentError');
        const confirmBtn = document.getElementById('confirmRentBtn');

        errorEl.classList.add('hidden');
        confirmBtn.disabled = true;
        confirmBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Processing...';

        try {
            const res = await API.post('/storage/rent', { amount_gb: amount });
            Toast.show(`Successfully rented ${amount} GB!`, 'success');
            modal.classList.add('hidden');
            amountInput.value = '10';
            await loadStorage();
        } catch (err) {
            errorEl.textContent = err.message;
            errorEl.classList.remove('hidden');
        } finally {
            confirmBtn.disabled = false;
            confirmBtn.innerHTML = '<i class="fas fa-check"></i> Confirm Rental';
        }
    });

    await loadStorage();
})();
