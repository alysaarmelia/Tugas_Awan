// Subscription page
(async function() {
    document.getElementById('pageTitle').textContent = 'Subscription';

    const TIER_ICONS = { free: 'fa-gift', pro: 'fa-star', enterprise: 'fa-crown' };
    const TIER_COLORS = { free: 'text-slate-500', pro: 'text-blue-600', enterprise: 'text-purple-600' };
    const TIER_BG     = { free: 'bg-slate-100', pro: 'bg-blue-100', enterprise: 'bg-purple-100' };

    async function loadSubscription() {
        try {
            const [tiersRes, subRes] = await Promise.all([
                API.get('/user/subscription/tiers'),
                API.get('/user/subscription'),
            ]);

            const tiers     = tiersRes.data.tiers;
            const currentSub = subRes.data;
            const currentTier = currentSub?.tier;

            // Current plan banner
            document.getElementById('currentTierLabel').textContent =
                currentTier ? currentTier[0].toUpperCase() + currentTier.slice(1) : 'No subscription';
            document.getElementById('currentTierPrice').textContent =
                (currentSub?.price_usd && currentSub.price_usd > 0)
                    ? `$${currentSub.price_usd}`
                    : 'Free';

            // Tier cards
            const grid = document.getElementById('tiersGrid');
            grid.innerHTML = tiers.map(tier => `
                <div class="relative bg-white rounded-xl p-6 shadow-sm border-2 transition-all ${tier.tier === currentTier ? 'border-blue-500' : 'border-slate-100'}">
                    ${tier.tier === currentTier
                        ? '<div class="absolute top-3 right-3 bg-blue-500 text-white text-xs px-2 py-0.5 rounded-full">Current</div>'
                        : ''}
                    <div class="flex items-center justify-between mb-3">
                        <h4 class="text-lg font-bold text-slate-800">${tier.name}</h4>
                        <div class="w-10 h-10 ${TIER_BG[tier.tier]} rounded-lg flex items-center justify-center">
                            <i class="fas ${TIER_ICONS[tier.tier]} ${TIER_COLORS[tier.tier]}"></i>
                        </div>
                    </div>
                    <div class="mb-4">
                        <span class="text-3xl font-bold text-slate-900">
                            ${tier.price_usd === 0 ? 'Free' : '$' + tier.price_usd}
                        </span>
                        ${tier.price_usd > 0 ? '<span class="text-slate-500 text-sm">/mo</span>' : ''}
                    </div>
                    <div class="text-sm text-slate-600 mb-4 font-medium">${tier.quota_gb} GB Storage</div>
                    <ul class="space-y-2 mb-6">
                        ${tier.features.map(f =>
                            `<li class="flex items-center gap-2 text-sm text-slate-600">
                                <i class="fas fa-check text-green-500 text-xs"></i>${f}
                            </li>`).join('')}
                    </ul>
                    <button
                        class="select-tier-btn w-full py-2.5 rounded-lg text-sm font-semibold transition-all
                            ${tier.tier === currentTier
                                ? 'bg-slate-100 text-slate-400 cursor-default'
                                : 'bg-blue-600 hover:bg-blue-700 text-white'}"
                        data-tier="${tier.tier}"
                        ${tier.tier === currentTier ? 'disabled' : ''}>
                        ${tier.tier === currentTier ? 'Current Plan' : 'Select Plan'}
                    </button>
                </div>`).join('');

            // Attach click handlers
            grid.querySelectorAll('.select-tier-btn:not([disabled])').forEach(btn => {
                btn.addEventListener('click', async () => {
                    btn.disabled = true;
                    btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Updating...';
                    try {
                        await API.post('/user/subscription', { tier: btn.dataset.tier });
                        Toast.show('Subscription updated!', 'success');
                        setTimeout(() => location.reload(), 1000);
                    } catch (err) {
                        Toast.show(err.message, 'error');
                        btn.disabled = false;
                        btn.innerHTML = 'Select Plan';
                    }
                });
            });
        } catch (err) {
            Toast.show('Failed to load subscription data', 'error');
        }
    }

    await loadSubscription();
})();
