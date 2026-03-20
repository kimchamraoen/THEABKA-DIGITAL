<div id="cookie-consent-banner" style="display:none; position:fixed; left:16px; right:16px; bottom:16px; z-index:99999;">
    <div style="max-width:1080px; margin:0 auto; background:rgba(16, 24, 39, 0.72); border:1px solid rgba(255,255,255,0.18); border-radius:16px; backdrop-filter:blur(12px); -webkit-backdrop-filter:blur(12px); box-shadow:0 10px 35px rgba(0,0,0,0.35); padding:14px 16px; color:#f3f4f6; font-family:inherit;">
        <div style="display:flex; gap:14px; align-items:center; justify-content:space-between; flex-wrap:wrap;">
            <p style="margin:0; line-height:1.5; font-size:14px; flex:1 1 540px; color:#e5e7eb;">
                We use cookies to improve your experience. By continuing to use this site, you agree to our Cookie Policy, <a href="/terms" style="color:#93c5fd; text-decoration:underline;">Terms &amp; Conditions</a>, and <a href="/privacy" style="color:#93c5fd; text-decoration:underline;">Privacy Policy</a>.
            </p>
            <div style="display:flex; gap:10px; flex:0 0 auto;">
                <button id="cookie-consent-manage" type="button" style="border:1px solid rgba(255,255,255,0.25); background:rgba(255,255,255,0.08); color:#e5e7eb; border-radius:10px; font-size:13px; font-weight:600; padding:9px 12px; cursor:pointer;">
                    Manage Preferences
                </button>
                <button id="cookie-consent-accept" type="button" style="border:1px solid rgba(125, 211, 252, 0.45); background:rgba(56, 189, 248, 0.25); color:#e0f2fe; border-radius:10px; font-size:13px; font-weight:700; padding:9px 12px; cursor:pointer;">
                    Accept All
                </button>
            </div>
        </div>
    </div>
</div>

<div id="cookie-preferences-overlay" style="display:none; position:fixed; inset:0; z-index:100000; background:rgba(2, 6, 23, 0.72); backdrop-filter:blur(6px); -webkit-backdrop-filter:blur(6px);">
    <div style="position:absolute; top:50%; left:50%; width:min(92vw, 460px); transform:translate(-50%, -50%); background:rgba(15, 23, 42, 0.82); border:1px solid rgba(255,255,255,0.2); border-radius:16px; box-shadow:0 18px 45px rgba(0,0,0,0.45); color:#e2e8f0; font-family:inherit; padding:18px;">
        <h3 style="margin:0 0 12px; font-size:20px; color:#f8fafc;">Cookie Preferences</h3>

        <label style="display:flex; gap:10px; align-items:flex-start; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.08);">
            <input type="checkbox" checked disabled style="margin-top:3px; width:16px; height:16px; accent-color:#38bdf8;">
            <span>
                <strong style="display:block; color:#f1f5f9; font-size:14px;">Essential Cookies</strong>
                <span style="display:block; margin-top:2px; font-size:12px; color:#94a3b8;">Required for the site to work</span>
            </span>
        </label>

        <label style="display:flex; gap:10px; align-items:flex-start; padding:10px 0; border-bottom:1px solid rgba(255,255,255,0.08); cursor:pointer;">
            <input id="cookie-pref-analytics" type="checkbox" style="margin-top:3px; width:16px; height:16px; accent-color:#38bdf8;">
            <span>
                <strong style="display:block; color:#f1f5f9; font-size:14px;">Analytics Cookies</strong>
                <span style="display:block; margin-top:2px; font-size:12px; color:#94a3b8;">Help us understand how visitors use the site</span>
            </span>
        </label>

        <label style="display:flex; gap:10px; align-items:flex-start; padding:10px 0; cursor:pointer;">
            <input id="cookie-pref-marketing" type="checkbox" style="margin-top:3px; width:16px; height:16px; accent-color:#38bdf8;">
            <span>
                <strong style="display:block; color:#f1f5f9; font-size:14px;">Marketing Cookies</strong>
                <span style="display:block; margin-top:2px; font-size:12px; color:#94a3b8;">Used to show relevant advertisements</span>
            </span>
        </label>

        <div style="margin-top:14px; display:flex; gap:8px; flex-wrap:wrap; justify-content:flex-end;">
            <button id="cookie-pref-cancel" type="button" style="border:1px solid rgba(255,255,255,0.26); background:rgba(255,255,255,0.08); color:#e2e8f0; border-radius:10px; font-size:13px; font-weight:600; padding:8px 12px; cursor:pointer;">
                Cancel
            </button>
            <button id="cookie-pref-accept-all" type="button" style="border:1px solid rgba(103, 232, 249, 0.5); background:rgba(34, 211, 238, 0.22); color:#cffafe; border-radius:10px; font-size:13px; font-weight:700; padding:8px 12px; cursor:pointer;">
                Accept All
            </button>
            <button id="cookie-pref-save" type="button" style="border:1px solid rgba(125, 211, 252, 0.5); background:rgba(56, 189, 248, 0.25); color:#e0f2fe; border-radius:10px; font-size:13px; font-weight:700; padding:8px 12px; cursor:pointer;">
                Save Preferences
            </button>
        </div>
    </div>
</div>

<script>
(function () {
    var KEY = 'cookie_consent';
    var PREFS_KEY = 'cookie_consent_preferences';
    var banner = document.getElementById('cookie-consent-banner');
    var acceptBtn = document.getElementById('cookie-consent-accept');
    var manageBtn = document.getElementById('cookie-consent-manage');
    var overlay = document.getElementById('cookie-preferences-overlay');
    var analyticsCheckbox = document.getElementById('cookie-pref-analytics');
    var marketingCheckbox = document.getElementById('cookie-pref-marketing');
    var savePrefsBtn = document.getElementById('cookie-pref-save');
    var acceptAllPrefsBtn = document.getElementById('cookie-pref-accept-all');
    var cancelPrefsBtn = document.getElementById('cookie-pref-cancel');

    if (!banner || !acceptBtn || !manageBtn || !overlay || !analyticsCheckbox || !marketingCheckbox || !savePrefsBtn || !acceptAllPrefsBtn || !cancelPrefsBtn) {
        return;
    }

    function setCookie(name, value, days) {
        var expires = new Date(Date.now() + days * 24 * 60 * 60 * 1000).toUTCString();
        document.cookie = name + '=' + encodeURIComponent(value) + '; expires=' + expires + '; path=/; SameSite=Lax';
    }

    function getCookie(name) {
        var escaped = name.replace(/[.*+?^${}()|[\]\\]/g, '\\$&');
        var match = document.cookie.match(new RegExp('(?:^|; )' + escaped + '=([^;]*)'));
        return match ? decodeURIComponent(match[1]) : null;
    }

    function persistConsent(value) {
        localStorage.setItem(KEY, value);
        setCookie(KEY, value, 365);
        banner.style.display = 'none';
        overlay.style.display = 'none';
    }

    function persistPreferences(prefs) {
        var payload = JSON.stringify(prefs);
        localStorage.setItem(PREFS_KEY, payload);
        setCookie(PREFS_KEY, payload, 365);
    }

    function readPreferences() {
        var fromStorage = localStorage.getItem(PREFS_KEY);
        var parsed = null;

        if (fromStorage) {
            try {
                parsed = JSON.parse(fromStorage);
            } catch (e) {
                parsed = null;
            }
        }

        if (!parsed) {
            var fromCookie = getCookie(PREFS_KEY);
            if (fromCookie) {
                try {
                    parsed = JSON.parse(fromCookie);
                } catch (e) {
                    parsed = null;
                }
            }
        }

        if (!parsed || typeof parsed !== 'object') {
            parsed = {
                essential: true,
                analytics: false,
                marketing: false
            };
        }

        return parsed;
    }

    function syncPreferenceInputs() {
        var prefs = readPreferences();
        analyticsCheckbox.checked = !!prefs.analytics;
        marketingCheckbox.checked = !!prefs.marketing;
    }

    function openPreferences() {
        syncPreferenceInputs();
        overlay.style.display = 'block';
    }

    function closePreferences() {
        overlay.style.display = 'none';
    }

    var localValue = localStorage.getItem(KEY);
    var cookieValue = getCookie(KEY);
    var existing = localValue || cookieValue;

    if (existing === 'accepted' || existing === 'declined' || existing === 'custom') {
        if (localValue !== existing) {
            localStorage.setItem(KEY, existing);
        }
        if (cookieValue !== existing) {
            setCookie(KEY, existing, 365);
        }
        return;
    }

    banner.style.display = 'block';

    acceptBtn.addEventListener('click', function () {
        persistPreferences({
            essential: true,
            analytics: true,
            marketing: true
        });
        persistConsent('accepted');
    });

    manageBtn.addEventListener('click', function () {
        openPreferences();
    });

    savePrefsBtn.addEventListener('click', function () {
        persistPreferences({
            essential: true,
            analytics: !!analyticsCheckbox.checked,
            marketing: !!marketingCheckbox.checked
        });
        persistConsent('custom');
    });

    acceptAllPrefsBtn.addEventListener('click', function () {
        analyticsCheckbox.checked = true;
        marketingCheckbox.checked = true;
        persistPreferences({
            essential: true,
            analytics: true,
            marketing: true
        });
        persistConsent('accepted');
    });

    cancelPrefsBtn.addEventListener('click', function () {
        closePreferences();
    });

    overlay.addEventListener('click', function (event) {
        if (event.target === overlay) {
            closePreferences();
        }
    });
})();
</script>
