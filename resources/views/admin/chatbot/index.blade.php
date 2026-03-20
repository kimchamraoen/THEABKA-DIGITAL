<x-app-layout>
    <style>
        .chatbot-admin { max-width: 900px; margin: 0 auto; padding: 32px 20px; }
        .admin-page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 28px;
        }
        .admin-page-header h1 {
            font-size: 22px;
            font-weight: 700;
            margin: 0;
        }
        .admin-page-header .header-badge {
            font-size: 12px;
            padding: 4px 12px;
            border-radius: 20px;
            background: rgba(16,185,129,0.12);
            color: #10b981;
            font-weight: 600;
        }

        /* Tabs */
        .admin-tabs {
            display: flex;
            gap: 4px;
            margin-bottom: 24px;
            background: rgba(255,255,255,0.03);
            border-radius: 12px;
            padding: 4px;
            border: 1px solid rgba(255,255,255,0.06);
        }
        .admin-tab {
            flex: 1;
            padding: 10px 16px;
            border: none;
            background: transparent;
            color: inherit;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            border-radius: 8px;
            transition: all .2s;
            opacity: 0.5;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        .admin-tab:hover { opacity: 0.7; }
        .admin-tab.active {
            background: rgba(255,255,255,0.08);
            opacity: 1;
        }
        .tab-content { display: none; }
        .tab-content.active { display: block; }

        /* Sections */
        .admin-section {
            background: rgba(255,255,255,0.03);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 16px;
            padding: 24px;
            margin-bottom: 20px;
        }
        .section-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 20px;
            padding-bottom: 14px;
            border-bottom: 1px solid rgba(255,255,255,0.06);
        }
        .section-header h2 {
            font-size: 16px;
            font-weight: 700;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .section-header .section-icon {
            width: 32px;
            height: 32px;
            border-radius: 8px;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Form Fields */
        .form-group { margin-bottom: 18px; }
        .form-group label {
            display: block;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 6px;
            opacity: .6;
            text-transform: uppercase;
            letter-spacing: 0.3px;
        }
        .form-group input[type="text"],
        .form-group input[type="password"],
        .form-group select,
        .form-group textarea {
            width: 100%;
            background: rgba(255,255,255,0.04);
            border: 1px solid rgba(255,255,255,0.08);
            border-radius: 10px;
            padding: 10px 14px;
            color: inherit;
            font-size: 14px;
            font-family: inherit;
            outline: none;
            box-sizing: border-box;
            transition: border-color .2s;
        }
        .form-group input:focus,
        .form-group select:focus,
        .form-group textarea:focus {
            border-color: var(--color-primary, #6366f1);
        }
        .form-group textarea { resize: vertical; min-height: 100px; }
        .form-group small { display: block; margin-top: 6px; font-size: 12px; opacity: .35; line-height: 1.4; }

        /* API Key Row */
        .api-key-card {
            background: rgba(255,255,255,0.02);
            border: 1px solid rgba(255,255,255,0.06);
            border-radius: 12px;
            padding: 16px;
            margin-bottom: 16px;
        }
        .api-key-card .card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .api-key-card .card-header h3 {
            font-size: 14px;
            font-weight: 600;
            margin: 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .api-key-card .provider-logo {
            width: 24px;
            height: 24px;
            border-radius: 6px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 13px;
            font-weight: 700;
        }
        .provider-openai { background: rgba(16,163,127,0.15); color: #10a37f; }
        .provider-gemini { background: rgba(66,133,244,0.15); color: #4285f4; }

        .password-wrapper {
            position: relative;
            display: flex;
            gap: 8px;
        }
        .password-wrapper input {
            flex: 1;
            padding-right: 40px;
        }
        .password-toggle {
            position: absolute;
            right: 110px;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            opacity: .4;
            padding: 4px;
        }
        .password-toggle:hover { opacity: .7; }

        .btn-test-api {
            background: rgba(255,255,255,0.06);
            border: 1px solid rgba(255,255,255,0.1);
            color: inherit;
            padding: 10px 16px;
            border-radius: 10px;
            font-size: 13px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            display: flex;
            align-items: center;
            gap: 6px;
            white-space: nowrap;
            height: fit-content;
        }
        .btn-test-api:hover { background: rgba(255,255,255,0.1); }
        .btn-test-api.testing { opacity: .6; cursor: wait; }
        .btn-test-api.success { border-color: #10b981; color: #10b981; }
        .btn-test-api.error { border-color: #ef4444; color: #ef4444; }

        .test-result {
            margin-top: 8px;
            font-size: 12px;
            padding: 8px 12px;
            border-radius: 8px;
            display: none;
        }
        .test-result.visible { display: block; }
        .test-result.success { background: rgba(16,185,129,0.1); color: #10b981; }
        .test-result.error { background: rgba(239,68,68,0.1); color: #ef4444; }

        /* Model Selector */
        .model-selector {
            margin-top: 12px;
        }
        .model-selector .model-list {
            display: flex;
            flex-wrap: wrap;
            gap: 6px;
            margin-top: 8px;
        }
        .model-chip {
            padding: 6px 12px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 500;
            cursor: pointer;
            border: 1px solid rgba(255,255,255,0.1);
            background: rgba(255,255,255,0.03);
            color: inherit;
            transition: all .15s;
        }
        .model-chip:hover { background: rgba(255,255,255,0.08); }
        .model-chip.active {
            background: rgba(var(--color-primary-rgb, 99, 102, 241), 0.15);
            border-color: rgba(var(--color-primary-rgb, 99, 102, 241), 0.3);
            color: var(--color-primary, #6366f1);
        }
        .model-loading {
            font-size: 12px;
            opacity: .4;
            padding: 8px 0;
        }
        .clear-key-label {
            font-size: 12px;
            display: flex;
            align-items: center;
            gap: 4px;
            margin-top: 6px;
            opacity: .5;
            cursor: pointer;
        }
        .clear-key-label input { width: auto; }

        /* Toggle */
        .toggle-row {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 14px 0;
            border-bottom: 1px solid rgba(255,255,255,0.04);
        }
        .toggle-row:last-child { border-bottom: none; }
        .toggle-info { }
        .toggle-info label { margin: 0; font-size: 14px; font-weight: 500; opacity: 1; text-transform: none; letter-spacing: 0; }
        .toggle-info small { margin-top: 2px; }
        .toggle-switch {
            position: relative;
            width: 44px;
            height: 24px;
            flex-shrink: 0;
        }
        .toggle-switch input { opacity: 0; width: 0; height: 0; position: absolute; }
        .toggle-slider {
            position: absolute;
            inset: 0;
            background: rgba(255,255,255,0.12);
            border-radius: 12px;
            cursor: pointer;
            transition: background .2s;
        }
        .toggle-slider::after {
            content: '';
            position: absolute;
            width: 18px;
            height: 18px;
            border-radius: 50%;
            background: #fff;
            top: 3px;
            left: 3px;
            transition: transform .2s;
        }
        .toggle-switch input:checked + .toggle-slider { background: var(--color-primary, #6366f1); }
        .toggle-switch input:checked + .toggle-slider::after { transform: translateX(20px); }

        /* Buttons */
        .btn-primary {
            background: var(--color-primary, #6366f1);
            color: #fff;
            border: none;
            border-radius: 10px;
            padding: 11px 24px;
            font-size: 14px;
            font-weight: 600;
            cursor: pointer;
            transition: all .2s;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }
        .btn-primary:hover { opacity: .85; transform: translateY(-1px); }
        .btn-danger {
            background: rgba(239,68,68,0.12);
            color: #ef4444;
            border: 1px solid rgba(239,68,68,0.2);
            border-radius: 8px;
            padding: 6px 14px;
            font-size: 12px;
            font-weight: 600;
            cursor: pointer;
            transition: all .15s;
        }
        .btn-danger:hover { background: rgba(239,68,68,0.2); }

        /* Alert */
        .alert-success {
            background: rgba(16,185,129,0.08);
            border: 1px solid rgba(16,185,129,0.2);
            color: #10b981;
            padding: 12px 16px;
            border-radius: 10px;
            font-size: 13px;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        /* Documents */
        .doc-upload-area {
            padding: 20px;
            background: rgba(255,255,255,0.02);
            border: 1px dashed rgba(255,255,255,0.1);
            border-radius: 12px;
            margin-bottom: 20px;
        }
        .doc-table { width: 100%; border-collapse: collapse; margin-top: 16px; }
        .doc-table th,
        .doc-table td {
            text-align: left;
            padding: 12px 14px;
            border-bottom: 1px solid rgba(255,255,255,0.04);
            font-size: 13px;
        }
        .doc-table th {
            font-weight: 600;
            opacity: .4;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: .5px;
        }
        .doc-table tr:hover td { background: rgba(255,255,255,0.02); }
        .doc-actions { display: flex; gap: 8px; align-items: center; }
        .status-badge {
            font-size: 11px;
            padding: 3px 10px;
            border-radius: 9999px;
            font-weight: 600;
        }
        .status-active { background: rgba(16,185,129,0.12); color: #10b981; }
        .status-inactive { background: rgba(255,255,255,0.06); opacity: .5; }
        .empty-docs {
            text-align: center;
            padding: 32px 20px;
            opacity: .35;
            font-size: 13px;
        }
    </style>

    <div class="chatbot-admin">
        <div class="admin-page-header">
            <h1>Chatbot Settings</h1>
            @if($settings['CHATBOT_ENABLED'] === '1')
                <span class="header-badge">Active</span>
            @else
                <span class="header-badge" style="background: rgba(239,68,68,0.12); color: #ef4444;">Disabled</span>
            @endif
        </div>

        @if(session('success'))
            <div class="alert-success">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>
                {{ session('success') }}
            </div>
        @endif

        <!-- Tabs -->
        <div class="admin-tabs">
            <button class="admin-tab active" onclick="switchTab('providers')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 16V8a2 2 0 0 0-1-1.73l-7-4a2 2 0 0 0-2 0l-7 4A2 2 0 0 0 3 8v8a2 2 0 0 0 1 1.73l7 4a2 2 0 0 0 2 0l7-4A2 2 0 0 0 21 16z"/></svg>
                Providers & Models
            </button>
            <button class="admin-tab" onclick="switchTab('personality')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                AI Personality
            </button>
            <button class="admin-tab" onclick="switchTab('knowledge')">
                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                Knowledge Base
            </button>
        </div>

        <form method="POST" action="{{ route('admin.chatbot.update') }}" id="settingsForm">
            @csrf

            <!-- Tab: Providers & Models -->
            <div class="tab-content active" id="tab-providers">
                <!-- OpenAI -->
                <div class="api-key-card">
                    <div class="card-header">
                        <h3>
                            <span class="provider-logo provider-openai">O</span>
                            OpenAI
                        </h3>
                        <button type="button" class="btn-test-api" id="testOpenai" onclick="testApiKey('openai')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Test Connection
                        </button>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>API Key</label>
                        <div class="password-wrapper">
                            <input type="password" name="OPENAI_API_KEY" id="openaiKey" placeholder="{{ $settings['OPENAI_API_KEY'] ? '••••••••••••••••' : 'sk-...' }}">
                            <button type="button" class="password-toggle" onclick="togglePassword('openaiKey')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @if($settings['OPENAI_API_KEY'])
                        <label class="clear-key-label"><input type="checkbox" name="clear_OPENAI_API_KEY" value="1"> Clear existing key</label>
                        @endif
                    </div>
                    <div class="test-result" id="testResultOpenai"></div>
                    <div class="model-selector">
                        <label style="font-size:12px;font-weight:600;opacity:.6;text-transform:uppercase;letter-spacing:0.3px;">Model</label>
                        <div class="form-group" style="margin-bottom:8px;margin-top:6px;">
                            <select name="OPENAI_MODEL" id="openaiModel">
                                <option value="">Auto-detect best model</option>
                                <option value="gpt-4o-mini" {{ ($settings['OPENAI_MODEL'] ?? '') === 'gpt-4o-mini' ? 'selected' : '' }}>gpt-4o-mini</option>
                                <option value="gpt-4o" {{ ($settings['OPENAI_MODEL'] ?? '') === 'gpt-4o' ? 'selected' : '' }}>gpt-4o</option>
                                <option value="gpt-4-turbo" {{ ($settings['OPENAI_MODEL'] ?? '') === 'gpt-4-turbo' ? 'selected' : '' }}>gpt-4-turbo</option>
                                <option value="gpt-3.5-turbo" {{ ($settings['OPENAI_MODEL'] ?? '') === 'gpt-3.5-turbo' ? 'selected' : '' }}>gpt-3.5-turbo</option>
                            </select>
                        </div>
                        <button type="button" class="btn-test-api" style="font-size:12px;padding:6px 12px;" onclick="detectModels('openai')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.2-8.56"/><polyline points="21 3 21 9 15 9"/></svg>
                            Auto-detect available models
                        </button>
                        <div id="openaiModelsDetected" class="model-list" style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;"></div>
                    </div>
                </div>

                <!-- Gemini -->
                <div class="api-key-card">
                    <div class="card-header">
                        <h3>
                            <span class="provider-logo provider-gemini">G</span>
                            Google Gemini
                        </h3>
                        <button type="button" class="btn-test-api" id="testGemini" onclick="testApiKey('gemini')">
                            <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg>
                            Test Connection
                        </button>
                    </div>
                    <div class="form-group" style="margin-bottom:0;">
                        <label>API Key</label>
                        <div class="password-wrapper">
                            <input type="password" name="GEMINI_API_KEY" id="geminiKey" placeholder="{{ $settings['GEMINI_API_KEY'] ? '••••••••••••••••' : 'AIza...' }}">
                            <button type="button" class="password-toggle" onclick="togglePassword('geminiKey')">
                                <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
                            </button>
                        </div>
                        @if($settings['GEMINI_API_KEY'])
                        <label class="clear-key-label"><input type="checkbox" name="clear_GEMINI_API_KEY" value="1"> Clear existing key</label>
                        @endif
                    </div>
                    <div class="test-result" id="testResultGemini"></div>
                    <div class="model-selector">
                        <label style="font-size:12px;font-weight:600;opacity:.6;text-transform:uppercase;letter-spacing:0.3px;">Model</label>
                        <div class="form-group" style="margin-bottom:8px;margin-top:6px;">
                            <select name="GEMINI_MODEL" id="geminiModel">
                                <option value="">Auto-detect best model</option>
                                <option value="gemini-2.5-flash" {{ ($settings['GEMINI_MODEL'] ?? '') === 'gemini-2.5-flash' ? 'selected' : '' }}>gemini-2.5-flash (Free tier)</option>
                                <option value="gemini-2.5-pro" {{ ($settings['GEMINI_MODEL'] ?? '') === 'gemini-2.5-pro' ? 'selected' : '' }}>gemini-2.5-pro (Free tier)</option>
                                <option value="gemini-2.0-flash" {{ ($settings['GEMINI_MODEL'] ?? '') === 'gemini-2.0-flash' ? 'selected' : '' }}>gemini-2.0-flash (Free tier)</option>
                                <option value="gemini-2.0-flash-lite" {{ ($settings['GEMINI_MODEL'] ?? '') === 'gemini-2.0-flash-lite' ? 'selected' : '' }}>gemini-2.0-flash-lite (Free tier)</option>
                                <option value="gemini-1.5-flash" {{ ($settings['GEMINI_MODEL'] ?? '') === 'gemini-1.5-flash' ? 'selected' : '' }}>gemini-1.5-flash</option>
                                <option value="gemini-1.5-pro" {{ ($settings['GEMINI_MODEL'] ?? '') === 'gemini-1.5-pro' ? 'selected' : '' }}>gemini-1.5-pro</option>
                            </select>
                        </div>
                        <button type="button" class="btn-test-api" style="font-size:12px;padding:6px 12px;" onclick="detectModels('gemini')">
                            <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.2-8.56"/><polyline points="21 3 21 9 15 9"/></svg>
                            Auto-detect available models
                        </button>
                        <div id="geminiModelsDetected" class="model-list" style="margin-top:8px;display:flex;flex-wrap:wrap;gap:6px;"></div>
                    </div>
                </div>

                <!-- General -->
                <div class="admin-section">
                    <div class="section-header">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity="0.5"><circle cx="12" cy="12" r="3"/><path d="M12 1v6M12 17v6M4.22 4.22l4.24 4.24M15.54 15.54l4.24 4.24M1 12h6M17 12h6M4.22 19.78l4.24-4.24M15.54 8.46l4.24-4.24"/></svg>
                            General
                        </h2>
                    </div>
                    <div class="form-group">
                        <label for="defaultProvider">Default Provider</label>
                        <select name="CHATBOT_DEFAULT_PROVIDER" id="defaultProvider">
                            <option value="auto" {{ ($settings['CHATBOT_DEFAULT_PROVIDER'] ?? 'auto') === 'auto' ? 'selected' : '' }}>Auto (use first available key)</option>
                            <option value="openai" {{ ($settings['CHATBOT_DEFAULT_PROVIDER'] ?? '') === 'openai' ? 'selected' : '' }}>OpenAI</option>
                            <option value="gemini" {{ ($settings['CHATBOT_DEFAULT_PROVIDER'] ?? '') === 'gemini' ? 'selected' : '' }}>Gemini</option>
                        </select>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <label>Enable Chatbot</label>
                            <small style="display:block;margin-top:2px;">Show the chatbot feature to all users</small>
                        </div>
                        <div class="toggle-switch">
                            <input type="checkbox" name="CHATBOT_ENABLED" id="chatbotEnabled" {{ $settings['CHATBOT_ENABLED'] === '1' ? 'checked' : '' }}>
                            <span class="toggle-slider" onclick="this.previousElementSibling.click()"></span>
                        </div>
                    </div>

                    <div class="toggle-row">
                        <div class="toggle-info">
                            <label>Allow Users to Use Own API Keys</label>
                            <small style="display:block;margin-top:2px;">Users can configure their own provider and keys</small>
                        </div>
                        <div class="toggle-switch">
                            <input type="checkbox" name="CHATBOT_ALLOW_USER_API_KEY" id="allowUserKey" {{ $settings['CHATBOT_ALLOW_USER_API_KEY'] === '1' ? 'checked' : '' }}>
                            <span class="toggle-slider" onclick="this.previousElementSibling.click()"></span>
                        </div>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Settings
                </button>
            </div>

            <!-- Tab: AI Personality -->
            <div class="tab-content" id="tab-personality">
                <div class="admin-section">
                    <div class="section-header">
                        <h2>
                            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity="0.5"><circle cx="12" cy="12" r="10"/><path d="M8 14s1.5 2 4 2 4-2 4-2"/><line x1="9" y1="9" x2="9.01" y2="9"/><line x1="15" y1="9" x2="15.01" y2="9"/></svg>
                            System Prompt
                        </h2>
                    </div>
                    <div class="form-group">
                        <label for="systemPrompt">Custom Instructions</label>
                        <textarea name="CHATBOT_SYSTEM_PROMPT" id="systemPrompt" rows="6" placeholder="You are a helpful assistant for Source Share...">{{ $settings['CHATBOT_SYSTEM_PROMPT'] }}</textarea>
                        <small>Define the AI's personality and behavior. This is sent as a system message with every conversation. Be specific about tone, knowledge domain, and response format.</small>
                    </div>
                </div>

                <button type="submit" class="btn-primary">
                    <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M19 21H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11l5 5v11a2 2 0 0 1-2 2z"/><polyline points="17 21 17 13 7 13 7 21"/><polyline points="7 3 7 8 15 8"/></svg>
                    Save Settings
                </button>
            </div>
        </form>

        <!-- Tab: Knowledge Base -->
        <div class="tab-content" id="tab-knowledge">
            <div class="admin-section">
                <div class="section-header">
                    <h2>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity="0.5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>
                        Upload Document
                    </h2>
                </div>
                <p style="font-size: 13px; opacity: .4; margin: 0 0 16px; line-height: 1.5;">Active documents are used as AI knowledge context. The AI will reference these documents when answering questions.</p>

                <form method="POST" action="{{ route('admin.chatbot.documents.upload') }}" class="doc-upload-area">
                    @csrf
                    <div class="form-group">
                        <label for="docTitle">Document Title</label>
                        <input type="text" name="title" id="docTitle" placeholder="e.g. Company FAQ, Product Guide..." required>
                    </div>
                    <div class="form-group">
                        <label for="docContent">Document Content</label>
                        <textarea name="content" id="docContent" rows="6" placeholder="Paste your document text here..." required></textarea>
                    </div>
                    <button type="submit" class="btn-primary">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                        Upload Document
                    </button>
                </form>
            </div>

            @if($documents->count())
            <div class="admin-section">
                <div class="section-header">
                    <h2>
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity="0.5"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                        Documents ({{ $documents->count() }})
                    </h2>
                </div>
                <table class="doc-table">
                    <thead>
                        <tr>
                            <th>Title</th>
                            <th>Added</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($documents as $doc)
                        <tr id="doc-row-{{ $doc->id }}">
                            <td style="font-weight:500;">{{ $doc->title }}</td>
                            <td style="opacity:.5;">{{ $doc->created_at->diffForHumans() }}</td>
                            <td>
                                <span id="doc-status-{{ $doc->id }}" class="status-badge {{ $doc->is_active ? 'status-active' : 'status-inactive' }}">
                                    {{ $doc->is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td>
                                <div class="doc-actions">
                                    <button type="button" class="btn-test-api" style="font-size:12px;padding:5px 12px;" onclick="toggleDoc({{ $doc->id }})">Toggle</button>
                                    <form method="POST" action="{{ route('admin.chatbot.documents.delete', $doc->id) }}" style="margin:0;" onsubmit="return confirm('Delete this document?')">
                                        @csrf @method('DELETE')
                                        <button type="submit" class="btn-danger">Delete</button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @else
            <div class="admin-section">
                <div class="empty-docs">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" style="margin: 0 auto 8px; display: block;"><path d="M4 19.5A2.5 2.5 0 0 1 6.5 17H20"/><path d="M6.5 2H20v20H6.5A2.5 2.5 0 0 1 4 19.5v-15A2.5 2.5 0 0 1 6.5 2z"/></svg>
                    No documents uploaded yet. Upload documents above to train the AI.
                </div>
            </div>
            @endif
        </div>
    </div>

    <script>
        const csrfToken = '{{ csrf_token() }}';

        function apiHeaders() {
            return { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' };
        }

        // ===== Tabs =====
        function switchTab(tab) {
            document.querySelectorAll('.admin-tab').forEach((el, i) => {
                const tabs = ['providers', 'personality', 'knowledge'];
                el.classList.toggle('active', tabs[i] === tab);
            });
            document.querySelectorAll('.tab-content').forEach(el => el.classList.remove('active'));
            document.getElementById('tab-' + tab).classList.add('active');
        }

        // ===== Password Toggle =====
        function togglePassword(id) {
            const input = document.getElementById(id);
            input.type = input.type === 'password' ? 'text' : 'password';
        }

        // ===== Test API =====
        async function testApiKey(provider) {
            const btn = document.getElementById('test' + provider.charAt(0).toUpperCase() + provider.slice(1));
            const result = document.getElementById('testResult' + provider.charAt(0).toUpperCase() + provider.slice(1));
            const keyInput = document.getElementById(provider === 'openai' ? 'openaiKey' : 'geminiKey');

            btn.classList.add('testing');
            btn.classList.remove('success', 'error');
            result.classList.remove('visible', 'success', 'error');
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21 12a9 9 0 1 1-6.2-8.56"/><polyline points="21 3 21 9 15 9"/></svg> Testing...';

            try {
                const res = await fetch('{{ route("admin.chatbot.test-api") }}', {
                    method: 'POST',
                    headers: apiHeaders(),
                    body: JSON.stringify({
                        provider: provider,
                        api_key: keyInput.value || null
                    })
                });
                const data = await res.json();

                btn.classList.remove('testing');
                if (data.success) {
                    btn.classList.add('success');
                    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Connected!';
                    result.className = 'test-result visible success';
                    result.textContent = data.message;
                    // Auto-detect models after successful test
                    detectModels(provider);
                } else {
                    btn.classList.add('error');
                    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Failed';
                    result.className = 'test-result visible error';
                    result.textContent = data.error;
                }

                setTimeout(() => {
                    btn.classList.remove('success', 'error');
                    btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M22 11.08V12a10 10 0 1 1-5.93-9.14"/><polyline points="22 4 12 14.01 9 11.01"/></svg> Test Connection';
                }, 5000);
            } catch (err) {
                btn.classList.remove('testing');
                btn.classList.add('error');
                btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg> Error';
                result.className = 'test-result visible error';
                result.textContent = 'Network error. Please try again.';
            }
        }

        // ===== Auto-detect Models =====
        async function detectModels(provider) {
            const container = document.getElementById(provider + 'ModelsDetected');
            const keyInput = document.getElementById(provider === 'openai' ? 'openaiKey' : 'geminiKey');
            const selectEl = document.getElementById(provider === 'openai' ? 'openaiModel' : 'geminiModel');

            container.innerHTML = '<span class="model-loading">Detecting available models...</span>';

            try {
                const res = await fetch('{{ route("admin.chatbot.list-models") }}', {
                    method: 'POST',
                    headers: apiHeaders(),
                    body: JSON.stringify({
                        provider: provider,
                        api_key: keyInput.value || null
                    })
                });
                const data = await res.json();

                container.innerHTML = '';
                if (data.models && data.models.length > 0) {
                    const currentVal = selectEl.value;

                    // Update select with detected models
                    selectEl.innerHTML = '<option value="">Auto-detect best model</option>';
                    data.models.forEach(model => {
                        const opt = document.createElement('option');
                        opt.value = model;
                        opt.textContent = model;
                        if (model === currentVal) opt.selected = true;
                        selectEl.appendChild(opt);
                    });

                    // Show model chips
                    data.models.forEach(model => {
                        const chip = document.createElement('button');
                        chip.type = 'button';
                        chip.className = 'model-chip' + (model === selectEl.value ? ' active' : '');
                        chip.textContent = model;
                        chip.onclick = () => {
                            selectEl.value = model;
                            container.querySelectorAll('.model-chip').forEach(c => c.classList.remove('active'));
                            chip.classList.add('active');
                        };
                        container.appendChild(chip);
                    });
                } else {
                    container.innerHTML = '<span class="model-loading">No models detected. Check your API key.</span>';
                }
            } catch (err) {
                container.innerHTML = '<span class="model-loading">Error detecting models.</span>';
            }
        }

        // ===== Toggle Document =====
        async function toggleDoc(id) {
            const res = await fetch(`/admin/settings/chatbot/documents/${id}/toggle`, {
                method: 'POST', headers: apiHeaders()
            });
            const data = await res.json();
            const badge = document.getElementById('doc-status-' + id);
            if (data.is_active) {
                badge.textContent = 'Active';
                badge.className = 'status-badge status-active';
            } else {
                badge.textContent = 'Inactive';
                badge.className = 'status-badge status-inactive';
            }
        }
    </script>
</x-app-layout>
