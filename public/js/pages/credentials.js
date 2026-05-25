// Credentials page
(async function() {
    document.getElementById('pageTitle').textContent = 'Credentials';

    let fullAK = '', fullSK = '';
    let akRevealed = false, skRevealed = false;
    let maskedAK = '•••••••••••••••••••••••••••••••••••••••••••••••••••••', maskedSK = '•••••••••••••••••••••••••••••••••••••••••••••••••';
    let akEl = null, skEl = null, revealAKBtn = null, copyAKBtn = null, copySKBtn = null, regenModal = null, confirmRegenBtn = null;
    let regenShowTimer = null;

    function showAK(val, iconHtml) {
        if (akEl) akEl.textContent = val;
        if (revealAKBtn && iconHtml !== undefined) revealAKBtn.innerHTML = iconHtml;
    }
    function showSK(val) {
        if (skEl) skEl.textContent = val;
    }

    /**
     * After a fresh regen, show full keys while the backend window is open.
     * Backend returns can_reveal_sk_until as a unix timestamp.
     * When that window closes, SK is gone forever — only way to get it back is regen.
     */
    function showKeysOnceAfterRegen(canRevealUntil) {
        const now = Math.floor(Date.now() / 1000);
        const windowMs = Math.max(0, (canRevealUntil - now) * 1000);

        // Show full keys immediately
        showAK(fullAK, '<i class="fas fa-eye-slash"></i> Showing...');
        showSK(fullSK);
        akRevealed = true;

        // Disable AK reveal button during the window
        if (revealAKBtn) {
            revealAKBtn.disabled = true;
            revealAKBtn.classList.add('opacity-50', 'cursor-not-allowed');
        }

        // Enable SK copy button for the reveal window
        if (copySKBtn) {
            copySKBtn.disabled = false;
            copySKBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            copySKBtn.innerHTML = '<i class="fas fa-copy"></i> Copy Secret Key';
        }

        // Banner turns urgent
        const banner = document.getElementById('credBanner');
        if (banner) {
            banner.className = 'bg-red-50 border border-red-300 rounded-xl p-4 mb-6 flex items-start gap-3';
            banner.innerHTML = `
                <i class="fas fa-exclamation-triangle text-red-600 mt-0.5"></i>
                <div class="text-sm text-red-800">
                    <p class="font-bold">Your keys are shown below — copy and store them now.</p>
                    <p class="text-red-700 mt-0.5">This is the only time they will be visible. Reload or leave this page and they are gone forever. You must regenerate to see them again.</p>
                </div>`;
        }

        // When the window closes, mask SK forever
        clearTimeout(regenShowTimer);
        regenShowTimer = setTimeout(() => {
            maskedAK = fullAK.replace(/.(?=.{4})/g, '•');
            maskedSK = fullSK.replace(/.(?=.{4})/g, '•');
            showAK(maskedAK, '<i class="fas fa-eye"></i> Reveal');
            showSK(maskedSK);
            akRevealed = false;

            // Re-enable AK controls (AK is less sensitive, can be revealed anytime)
            if (revealAKBtn) {
                revealAKBtn.disabled = false;
                revealAKBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }
            if (copyAKBtn) {
                copyAKBtn.disabled = false;
                copyAKBtn.classList.remove('opacity-50', 'cursor-not-allowed');
            }

            // SK is gone — disable copy button
            if (copySKBtn) {
                copySKBtn.disabled = true;
                copySKBtn.classList.add('opacity-50', 'cursor-not-allowed');
                copySKBtn.innerHTML = '<i class="fas fa-lock"></i> Secret Key Burned';
            }

            // Clear the full SK from memory
            fullSK = '';

            // Banner reverts to info state
            const banner2 = document.getElementById('credBanner');
            if (banner2) {
                banner2.className = 'bg-blue-50 border border-blue-200 rounded-xl p-4 mb-6 flex items-start gap-3';
                banner2.innerHTML = `
                    <i class="fas fa-info-circle text-blue-600 mt-0.5"></i>
                    <div class="text-sm text-blue-800">
                        <p class="font-medium">Store these credentials securely</p>
                        <p class="text-blue-700 mt-0.5">Your Secret Key was shown once and cannot be revealed again. Regenerate credentials to obtain a new key pair.</p>
                    </div>`;
            }
        }, windowMs);
    }

    async function loadCredentials() {
        try {
            const res  = await API.get('/credentials');
            const cred = res.data || {};
            fullAK = cred.access_key || '';
            fullSK = ''; // Backend never returns secret_key after page load
            maskedAK = cred.masked_ak || fullAK.replace(/.(?=.{4})/g, '•');
            maskedSK = cred.masked_sk || '••••••••••••••••••••••••••••••••';

            // Cache DOM refs
            akEl       = document.getElementById('akValue');
            skEl       = document.getElementById('skValue');
            revealAKBtn = document.getElementById('revealAK');
            copyAKBtn  = document.getElementById('copyAK');
            copySKBtn  = document.getElementById('copySK');
            regenModal = document.getElementById('regenModal');
            confirmRegenBtn = document.getElementById('confirmRegen');

            // SK copy button disabled on load — only enabled during 10s window after regen
            if (copySKBtn) {
                copySKBtn.disabled = true;
                copySKBtn.classList.add('opacity-50', 'cursor-not-allowed');
                copySKBtn.innerHTML = '<i class="fas fa-lock"></i> Secret Key Burned';
            }

            // Always show masked on load (keys only visible right after regen)
            showAK(maskedAK);
            showSK(maskedSK);
            akRevealed = false;

            document.getElementById('credBucket').textContent = cred.bucket_name || '--';
            document.getElementById('credCreated').textContent = UI.formatDate(cred.created_at) || '--';
            document.getElementById('credRegenerated').textContent = cred.last_regenerated ? UI.formatDate(cred.last_regenerated) : 'Never';

            attachListeners();
        } catch (err) {
            Toast.show('Failed to load credentials: ' + err.message, 'error');
        }
    }

    function attachListeners() {
        // Reveal/hide AK — one time reveal per page load (before regen)
        if (revealAKBtn) {
            revealAKBtn.addEventListener('click', () => {
                if (!akRevealed) {
                    showAK(fullAK, '<i class="fas fa-eye-slash"></i> Hide');
                    akRevealed = true;
                } else {
                    showAK(maskedAK, '<i class="fas fa-eye"></i> Reveal');
                    akRevealed = false;
                }
            });
        }

        // Copy AK — always works
        if (copyAKBtn) {
            copyAKBtn.addEventListener('click', () => {
                if (!fullAK) {
                    Toast.show('Access key not loaded yet', 'error');
                    return;
                }
                navigator.clipboard.writeText(fullAK)
                    .then(() => Toast.show('Access Key copied!', 'success'))
                    .catch(() => Toast.show('Copy failed', 'error'));
            });
        }

        // Copy SK — only works right after a fresh regen
        if (copySKBtn) {
            copySKBtn.addEventListener('click', () => {
                if (!fullSK) {
                    Toast.show('Secret key not loaded yet', 'error');
                    return;
                }
                if (copySKBtn.disabled) {
                    Toast.show('Secret Key is no longer available. Regenerate to get a new one.', 'warning');
                    return;
                }
                navigator.clipboard.writeText(fullSK)
                    .then(() => Toast.show('Secret Key copied!', 'success'))
                    .catch(() => Toast.show('Copy failed', 'error'));
            });
        }

        // Open regenerate modal
        const regenBtn = document.getElementById('regenerateBtn');
        if (regenBtn && regenModal) {
            regenBtn.addEventListener('click', () => {
                regenModal.classList.remove('hidden');
            });
        }

        const cancelBtn = document.getElementById('cancelRegen');
        if (cancelBtn && regenModal) {
            cancelBtn.addEventListener('click', () => {
                regenModal.classList.add('hidden');
            });
        }

        if (regenModal) {
            regenModal.addEventListener('click', e => {
                if (e.target === regenModal) regenModal.classList.add('hidden');
            });
        }

        // Confirm regenerate
        if (confirmRegenBtn) {
            confirmRegenBtn.addEventListener('click', async () => {
                confirmRegenBtn.disabled = true;
                confirmRegenBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Regenerating...';
                try {
                    const res = await API.post('/credentials/regenerate', {});
                    const data = res.data || {};

                    // Full keys ONLY available in the regen response — this is the only time
                    fullAK = data.access_key || '';
                    fullSK = data.secret_key || '';
                    maskedAK = data.masked_ak || fullAK.replace(/.(?=.{4})/g, '•');
                    maskedSK = data.masked_sk || fullSK.replace(/.(?=.{4})/g, '•');

                    if (regenModal) regenModal.classList.add('hidden');

                    // Show full keys once — the only time they are ever visible
                    showKeysOnceAfterRegen(data.can_reveal_sk_until);
                    Toast.show('Credentials regenerated! Copy and store your keys now.', 'warning');

                    document.getElementById('credRegenerated').textContent = UI.formatDate(new Date().toISOString());
                } catch (err) {
                    Toast.show('Regenerate failed: ' + err.message, 'error');
                } finally {
                    confirmRegenBtn.disabled = false;
                    confirmRegenBtn.innerHTML = '<i class="fas fa-check"></i> Yes, Regenerate';
                }
            });
        }
    }

    await loadCredentials();
})();
