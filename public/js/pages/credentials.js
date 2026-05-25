// Credentials page
(async function() {
    document.getElementById('pageTitle').textContent = 'Credentials';

    let fullAK = '', fullSK = '';
    let akRevealed = false, skRevealed = false;

    async function loadCredentials() {
        try {
            const res  = await API.get('/credentials');
            const cred = res.data;
            fullAK = cred.access_key || '';
            fullSK = cred.secret_key || '';

            document.getElementById('akValue').textContent      = cred.access_key;
            document.getElementById('skValue').textContent      = cred.secret_key;
            document.getElementById('credBucket').textContent   = cred.bucket_name || '--';
            document.getElementById('credCreated').textContent  = UI.formatDate(cred.created_at);
            document.getElementById('credRegenerated').textContent = cred.last_regenerated
                ? UI.formatDate(cred.last_regenerated)
                : 'Never';
        } catch (err) {
            Toast.show('Failed to load credentials', 'error');
        }
    }

    // Reveal AK
    document.getElementById('revealAK')?.addEventListener('click', async () => {
        if (!akRevealed) {
            document.getElementById('akValue').textContent = fullAK;
            document.getElementById('revealAK').innerHTML   = '<i class="fas fa-eye-slash"></i> Hide';
            akRevealed = true;
        } else {
            document.getElementById('akValue').textContent = 'AK•••••••••••••••XXXX';
            document.getElementById('revealAK').innerHTML   = '<i class="fas fa-eye"></i> Reveal';
            akRevealed = false;
        }
    });

    // Reveal SK
    document.getElementById('revealSK')?.addEventListener('click', async () => {
        if (!skRevealed) {
            document.getElementById('skValue').textContent = fullSK;
            document.getElementById('revealSK').innerHTML   = '<i class="fas fa-eye-slash"></i> Hide';
            skRevealed = true;
        } else {
            document.getElementById('skValue').textContent = 'SK•••••••••••••••XYZ';
            document.getElementById('revealSK').innerHTML   = '<i class="fas fa-eye"></i> Reveal';
            skRevealed = false;
        }
    });

    // Copy buttons
    document.getElementById('copyAK')?.addEventListener('click', () =>
        navigator.clipboard.writeText(fullAK).then(() => Toast.show('Access Key copied!', 'success'))
    );
    document.getElementById('copySK')?.addEventListener('click', () =>
        navigator.clipboard.writeText(fullSK).then(() => Toast.show('Secret Key copied!', 'success'))
    );

    // Regenerate modal
    const regenModal = document.getElementById('regenModal');
    document.getElementById('regenerateBtn')?.addEventListener('click', () => regenModal.classList.remove('hidden'));
    document.getElementById('cancelRegen')?.addEventListener('click',  () => regenModal.classList.add('hidden'));
    document.getElementById('confirmRegen')?.addEventListener('click', async () => {
        try {
            const res = await API.post('/credentials/regenerate', {});
            fullAK = res.data.access_key;
            fullSK = res.data.secret_key;
            regenModal.classList.add('hidden');
            document.getElementById('akValue').textContent = res.data.access_key;
            document.getElementById('skValue').textContent = res.data.secret_key;
            Toast.show('Credentials regenerated!', 'success');
            await loadCredentials();
        } catch (err) {
            Toast.show('Regenerate failed: ' + err.message, 'error');
        }
    });

    await loadCredentials();
})();
