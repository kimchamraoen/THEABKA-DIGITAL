<x-app-layout>
    {{-- Marked.js for Markdown + Highlight.js for code --}}
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/styles/github-dark.min.css">
    <script src="https://cdnjs.cloudflare.com/ajax/libs/marked/12.0.0/marked.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/highlight.js/11.9.0/highlight.min.js"></script>

    <style>
        /* ===== Chatbot Layout ===== */
        .chatbot-container { display: flex; height: calc(100vh - 5rem); overflow: hidden; }

        /* ===== Sidebar ===== */
        .chat-sidebar { width: 300px; min-width: 300px; display: flex; flex-direction: column; background: rgba(0,0,0,0.2); border-right: 1px solid rgba(255,255,255,0.06); overflow: hidden; }
        .chat-sidebar-header { padding: 20px 16px 12px; display: flex; flex-direction: column; gap: 12px; }
        .chat-sidebar-header .sidebar-top { display: flex; align-items: center; justify-content: space-between; }
        .chat-sidebar-header h2 { font-size: 12px; font-weight: 700; margin: 0; text-transform: uppercase; letter-spacing: 0.5px; opacity: 0.4; }
        .btn-new-chat { background: var(--color-primary, #6366f1); color: #fff; border: none; border-radius: 10px; padding: 8px 16px; font-size: 13px; font-weight: 600; cursor: pointer; transition: all .2s; display: flex; align-items: center; gap: 6px; }
        .btn-new-chat:hover { opacity: .85; transform: translateY(-1px); }
        .sidebar-search { position: relative; }
        .sidebar-search input { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.08); border-radius: 10px; padding: 8px 12px 8px 34px; color: inherit; font-size: 13px; outline: none; box-sizing: border-box; transition: border-color .2s; }
        .sidebar-search input:focus { border-color: rgba(255,255,255,0.2); }
        .sidebar-search input::placeholder { opacity: 0.35; }
        .sidebar-search svg { position: absolute; left: 10px; top: 50%; transform: translateY(-50%); opacity: 0.3; }
        .conversation-list { flex: 1; overflow-y: auto; padding: 8px; }
        .conversation-list::-webkit-scrollbar { width: 4px; }
        .conversation-list::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 4px; }
        .conversation-item { display: flex; align-items: center; justify-content: space-between; padding: 10px 12px; border-radius: 10px; cursor: pointer; transition: all .15s; margin-bottom: 2px; font-size: 13px; gap: 8px; border: 1px solid transparent; }
        .conversation-item:hover { background: rgba(255,255,255,0.06); }
        .conversation-item.active { background: rgba(var(--color-primary-rgb, 99, 102, 241), 0.12); border-color: rgba(var(--color-primary-rgb, 99, 102, 241), 0.2); }
        .conversation-item .conv-icon { width: 32px; height: 32px; border-radius: 8px; background: rgba(255,255,255,0.06); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .conversation-item .conv-info { flex: 1; min-width: 0; }
        .conversation-item .conv-title { display: block; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; font-weight: 500; }
        .conversation-item .conv-meta { font-size: 11px; opacity: 0.35; margin-top: 2px; }
        .conversation-item .conv-delete { opacity: 0; border: none; background: none; color: #ef4444; cursor: pointer; font-size: 18px; padding: 2px 6px; border-radius: 6px; transition: all .15s; flex-shrink: 0; }
        .conversation-item:hover .conv-delete { opacity: 0.6; }
        .conversation-item .conv-delete:hover { opacity: 1; background: rgba(239,68,68,0.1); }

        /* ===== Main Chat Area ===== */
        .chat-main { flex: 1; display: flex; flex-direction: column; overflow: hidden; min-width: 0; position: relative; }
        .chat-topbar { padding: 12px 20px; border-bottom: 1px solid rgba(255,255,255,0.06); display: flex; align-items: center; gap: 12px; flex-shrink: 0; background: rgba(0,0,0,0.1); }
        .chat-topbar .conv-title-display { font-size: 15px; font-weight: 600; flex: 1; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; }
        .topbar-actions { display: flex; align-items: center; gap: 8px; }
        .model-badge { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: inherit; padding: 6px 12px; border-radius: 8px; font-size: 12px; font-weight: 500; display: flex; align-items: center; gap: 6px; }
        .model-badge .dot { width: 6px; height: 6px; border-radius: 50%; background: #10b981; }
        .model-select { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: inherit; padding: 6px 10px; border-radius: 8px; font-size: 12px; cursor: pointer; outline: none; }
        .model-select option { background: #1e1e2e; color: #e0e0e0; }
        .provider-select { background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); color: inherit; padding: 6px 10px; border-radius: 8px; font-size: 12px; cursor: pointer; outline: none; }
        .provider-select option { background: #1e1e2e; color: #e0e0e0; }
        .btn-icon { background: none; border: none; color: inherit; cursor: pointer; padding: 6px; border-radius: 8px; opacity: .5; transition: all .15s; display: flex; align-items: center; justify-content: center; }
        .btn-icon:hover { opacity: 1; background: rgba(255,255,255,0.06); }

        /* ===== Messages Area ===== */
        .chat-messages { flex: 1; overflow-y: auto; padding: 24px; display: flex; flex-direction: column; gap: 4px; }
        .chat-messages::-webkit-scrollbar { width: 6px; }
        .chat-messages::-webkit-scrollbar-thumb { background: rgba(255,255,255,0.1); border-radius: 6px; }

        /* ===== Empty State ===== */
        .chat-empty { flex: 1; display: flex; align-items: center; justify-content: center; }
        .empty-content { text-align: center; max-width: 420px; }
        .empty-icon { width: 80px; height: 80px; border-radius: 24px; background: linear-gradient(135deg, rgba(99,102,241,0.15), rgba(139,92,246,0.15)); display: flex; align-items: center; justify-content: center; margin: 0 auto 20px; }
        .empty-content h3 { font-size: 18px; font-weight: 700; margin: 0 0 8px; }
        .empty-content p { font-size: 13px; opacity: 0.4; margin: 0 0 24px; line-height: 1.5; }
        .quick-prompts { display: grid; grid-template-columns: 1fr 1fr; gap: 8px; }
        .quick-prompt { background: rgba(255,255,255,0.04); border: 1px solid rgba(255,255,255,0.06); border-radius: 12px; padding: 12px; text-align: left; cursor: pointer; transition: all .15s; font-size: 12px; line-height: 1.4; color: inherit; }
        .quick-prompt:hover { background: rgba(255,255,255,0.08); border-color: rgba(255,255,255,0.12); }
        .quick-prompt .qp-title { font-weight: 600; margin-bottom: 2px; font-size: 13px; }
        .quick-prompt .qp-desc { opacity: 0.4; }

        /* ===== Message Bubbles ===== */
        .msg-row { display: flex; gap: 12px; padding: 12px 0; align-items: flex-start; }
        .msg-row.msg-row-user { flex-direction: row-reverse; }
        .msg-avatar { width: 32px; height: 32px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; font-size: 14px; }
        .msg-row-assistant .msg-avatar { background: linear-gradient(135deg, #6366f1, #8b5cf6); }
        .msg-row-user .msg-avatar { background: rgba(255,255,255,0.1); }
        .msg-content { max-width: 75%; min-width: 0; }
        .msg-bubble { padding: 12px 16px; border-radius: 16px; font-size: 14px; line-height: 1.7; word-wrap: break-word; }
        .msg-row-user .msg-bubble { background: var(--color-primary, #6366f1); color: #fff; border-bottom-right-radius: 4px; white-space: pre-wrap; }
        .msg-row-assistant .msg-bubble { background: rgba(255,255,255,0.06); border-bottom-left-radius: 4px; }
        .msg-time { font-size: 11px; opacity: 0.3; margin-top: 4px; padding: 0 4px; }
        .msg-row-user .msg-time { text-align: right; }

        /* ===== Markdown Content Styling ===== */
        .msg-row-assistant .msg-bubble p { margin: 0 0 10px; }
        .msg-row-assistant .msg-bubble p:last-child { margin-bottom: 0; }
        .msg-row-assistant .msg-bubble h1,
        .msg-row-assistant .msg-bubble h2,
        .msg-row-assistant .msg-bubble h3,
        .msg-row-assistant .msg-bubble h4 { margin: 16px 0 8px; font-weight: 700; }
        .msg-row-assistant .msg-bubble h1 { font-size: 1.3em; }
        .msg-row-assistant .msg-bubble h2 { font-size: 1.15em; }
        .msg-row-assistant .msg-bubble h3 { font-size: 1.05em; }
        .msg-row-assistant .msg-bubble ul,
        .msg-row-assistant .msg-bubble ol { margin: 8px 0; padding-left: 20px; }
        .msg-row-assistant .msg-bubble li { margin-bottom: 4px; }
        .msg-row-assistant .msg-bubble blockquote { margin: 8px 0; padding: 8px 14px; border-left: 3px solid rgba(255,255,255,0.2); opacity: 0.8; }
        .msg-row-assistant .msg-bubble table { border-collapse: collapse; width: 100%; margin: 10px 0; font-size: 13px; }
        .msg-row-assistant .msg-bubble th,
        .msg-row-assistant .msg-bubble td { border: 1px solid rgba(255,255,255,0.1); padding: 6px 10px; text-align: left; }
        .msg-row-assistant .msg-bubble th { background: rgba(255,255,255,0.06); font-weight: 600; }
        .msg-row-assistant .msg-bubble a { color: #93c5fd; text-decoration: underline; }
        .msg-row-assistant .msg-bubble strong { font-weight: 700; }
        .msg-row-assistant .msg-bubble em { font-style: italic; }
        .msg-row-assistant .msg-bubble hr { border: none; border-top: 1px solid rgba(255,255,255,0.1); margin: 12px 0; }

        /* Inline code */
        .msg-row-assistant .msg-bubble code:not(pre code) {
            background: rgba(255,255,255,0.1);
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 0.9em;
            font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace;
        }

        /* Code blocks */
        .code-block-wrapper {
            position: relative;
            margin: 10px 0;
            border-radius: 10px;
            overflow: hidden;
            background: #0d1117;
            border: 1px solid rgba(255,255,255,0.08);
        }
        .code-block-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 8px 14px;
            background: rgba(255,255,255,0.06);
            font-size: 12px;
            font-weight: 600;
            opacity: 0.6;
            font-family: 'JetBrains Mono', 'Fira Code', monospace;
        }
        .code-copy-btn {
            background: rgba(255,255,255,0.08);
            border: 1px solid rgba(255,255,255,0.1);
            color: inherit;
            padding: 4px 10px;
            border-radius: 6px;
            font-size: 11px;
            cursor: pointer;
            transition: all .15s;
            display: flex;
            align-items: center;
            gap: 4px;
            opacity: 0.7;
        }
        .code-copy-btn:hover { opacity: 1; background: rgba(255,255,255,0.15); }
        .code-copy-btn.copied { color: #10b981; border-color: rgba(16,185,129,0.3); }
        .code-block-wrapper pre {
            margin: 0 !important;
            padding: 14px !important;
            background: transparent !important;
            border: none !important;
            overflow-x: auto;
        }
        .code-block-wrapper pre code {
            font-size: 13px !important;
            line-height: 1.6 !important;
            font-family: 'JetBrains Mono', 'Fira Code', 'Consolas', monospace !important;
        }

        /* Message actions bar */
        .msg-actions {
            display: flex;
            gap: 4px;
            margin-top: 6px;
            opacity: 0;
            transition: opacity .15s;
        }
        .msg-row:hover .msg-actions { opacity: 1; }
        .msg-action-btn {
            background: none;
            border: none;
            color: inherit;
            cursor: pointer;
            padding: 4px 8px;
            border-radius: 6px;
            font-size: 11px;
            opacity: 0.4;
            transition: all .15s;
            display: flex;
            align-items: center;
            gap: 4px;
        }
        .msg-action-btn:hover { opacity: 1; background: rgba(255,255,255,0.06); }
        .msg-action-btn.copied { opacity: 1; color: #10b981; }

        /* ===== Typing Indicator ===== */
        .typing-row { display: none; gap: 12px; padding: 12px 0; align-items: flex-start; }
        .typing-row.visible { display: flex; }
        .typing-bubble { display: flex; gap: 5px; padding: 14px 20px; background: rgba(255,255,255,0.06); border-radius: 16px; border-bottom-left-radius: 4px; }
        .typing-dot { width: 8px; height: 8px; border-radius: 50%; background: rgba(255,255,255,0.3); animation: typingBounce .6s infinite alternate; }
        .typing-dot:nth-child(2) { animation-delay: .2s; }
        .typing-dot:nth-child(3) { animation-delay: .4s; }
        @keyframes typingBounce { from { opacity: .3; transform: translateY(0); } to { opacity: 1; transform: translateY(-5px); } }

        /* ===== Input Area ===== */
        .chat-input-area { padding: 16px 24px 20px; display: flex; gap: 10px; align-items: flex-end; flex-shrink: 0; background: rgba(0,0,0,0.1); }
        .input-wrapper { flex: 1; position: relative; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 14px; transition: border-color .2s; }
        .input-wrapper:focus-within { border-color: var(--color-primary, #6366f1); }
        .input-wrapper textarea { width: 100%; resize: none; background: transparent; border: none; padding: 12px 16px; color: inherit; font-size: 14px; font-family: inherit; min-height: 44px; max-height: 120px; outline: none; box-sizing: border-box; }
        .btn-send { background: var(--color-primary, #6366f1); color: #fff; border: none; border-radius: 12px; width: 46px; height: 46px; cursor: pointer; display: flex; align-items: center; justify-content: center; transition: all .2s; flex-shrink: 0; }
        .btn-send:hover { opacity: .85; transform: translateY(-1px); }
        .btn-send:disabled { opacity: .3; cursor: not-allowed; transform: none; }

        /* ===== Attachment Buttons ===== */
        .input-actions { display: flex; align-items: center; gap: 2px; padding: 4px 8px; }
        .btn-attach, .btn-image-gen { background: none; border: none; color: inherit; cursor: pointer; padding: 8px; border-radius: 8px; opacity: .45; transition: all .15s; display: flex; align-items: center; justify-content: center; }
        .btn-attach:hover, .btn-image-gen:hover { opacity: 1; background: rgba(255,255,255,0.06); }
        .btn-attach.has-files { opacity: 1; color: var(--color-primary, #6366f1); }

        /* ===== Attachment Preview Bar ===== */
        .attachment-preview-bar { display: none; padding: 8px 12px; gap: 8px; flex-wrap: wrap; border-top: 1px solid rgba(255,255,255,0.06); }
        .attachment-preview-bar.visible { display: flex; }
        .attachment-preview-item { position: relative; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 4px; display: flex; align-items: center; gap: 8px; max-width: 200px; }
        .attachment-preview-item img { width: 48px; height: 48px; object-fit: cover; border-radius: 6px; }
        .attachment-preview-item .file-icon { width: 48px; height: 48px; border-radius: 6px; background: rgba(255,255,255,0.08); display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .attachment-preview-item .att-info { flex: 1; min-width: 0; padding-right: 8px; }
        .attachment-preview-item .att-name { font-size: 11px; font-weight: 500; overflow: hidden; text-overflow: ellipsis; white-space: nowrap; display: block; }
        .attachment-preview-item .att-size { font-size: 10px; opacity: .4; }
        .attachment-preview-item .att-remove { position: absolute; top: -6px; right: -6px; width: 20px; height: 20px; border-radius: 50%; background: #ef4444; color: #fff; border: 2px solid var(--glass-bg, #1e1e2e); cursor: pointer; display: flex; align-items: center; justify-content: center; font-size: 12px; line-height: 1; }
        .attachment-preview-item .att-uploading { position: absolute; inset: 0; background: rgba(0,0,0,0.5); border-radius: 10px; display: flex; align-items: center; justify-content: center; }
        .att-spinner { width: 20px; height: 20px; border: 2px solid rgba(255,255,255,0.2); border-top-color: #fff; border-radius: 50%; animation: spin .6s linear infinite; }
        @keyframes spin { to { transform: rotate(360deg); } }

        /* ===== Message Attachments ===== */
        .msg-attachments { display: flex; flex-wrap: wrap; gap: 8px; margin-bottom: 8px; }
        .msg-attachment-img { max-width: 320px; max-height: 300px; border-radius: 10px; cursor: pointer; transition: transform .15s; border: 1px solid rgba(255,255,255,0.1); }
        .msg-attachment-img:hover { transform: scale(1.02); }
        .msg-attachment-file { display: flex; align-items: center; gap: 8px; background: rgba(255,255,255,0.06); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 8px 12px; font-size: 12px; text-decoration: none; color: inherit; transition: background .15s; }
        .msg-attachment-file:hover { background: rgba(255,255,255,0.1); }
        .msg-generated-images { display: flex; flex-wrap: wrap; gap: 8px; margin-top: 8px; }
        .msg-generated-img { max-width: 400px; max-height: 400px; border-radius: 12px; border: 1px solid rgba(255,255,255,0.1); }

        /* ===== Image Lightbox ===== */
        .image-lightbox { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.85); z-index: 3000; align-items: center; justify-content: center; cursor: pointer; }
        .image-lightbox.visible { display: flex; }
        .image-lightbox img { max-width: 90vw; max-height: 90vh; border-radius: 12px; box-shadow: 0 20px 60px rgba(0,0,0,0.5); }

        /* ===== Image Gen Modal ===== */
        .image-gen-modal { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
        .image-gen-modal.visible { display: flex; }
        .image-gen-box { background: var(--glass-bg, #1e1e2e); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 28px; width: 100%; max-width: 480px; box-shadow: 0 24px 64px rgba(0,0,0,0.5); }
        .image-gen-box h3 { margin: 0 0 8px; font-size: 18px; font-weight: 700; display: flex; align-items: center; gap: 8px; }
        .image-gen-box .modal-desc { font-size: 13px; opacity: 0.4; margin: 0 0 20px; }
        .image-gen-box textarea { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 12px 14px; color: inherit; font-size: 14px; font-family: inherit; outline: none; box-sizing: border-box; min-height: 80px; resize: vertical; transition: border-color .2s; }
        .image-gen-box textarea:focus { border-color: var(--color-primary, #6366f1); }

        /* ===== Drag & Drop Overlay ===== */
        .drop-overlay { display: none; position: absolute; inset: 0; background: rgba(99,102,241,0.1); border: 2px dashed var(--color-primary, #6366f1); border-radius: 16px; z-index: 100; align-items: center; justify-content: center; pointer-events: none; }
        .drop-overlay.visible { display: flex; }
        .drop-overlay-content { text-align: center; opacity: .7; }
        .drop-overlay-content svg { margin-bottom: 8px; }
        .drop-overlay-content p { font-size: 14px; font-weight: 600; margin: 0; }

        /* ===== Toast / Popup ===== */
        .chat-toast {
            position: fixed;
            top: 80px;
            right: 24px;
            z-index: 2000;
            background: #1e1e2e;
            border: 1px solid rgba(255,255,255,0.1);
            border-radius: 14px;
            padding: 16px 20px;
            max-width: 420px;
            box-shadow: 0 16px 48px rgba(0,0,0,0.5);
            transform: translateX(140%);
            transition: transform .3s ease;
            display: flex;
            align-items: flex-start;
            gap: 12px;
        }
        .chat-toast.visible { transform: translateX(0); }
        .chat-toast.toast-error { border-color: rgba(239,68,68,0.3); }
        .chat-toast.toast-error .toast-icon { background: rgba(239,68,68,0.15); color: #ef4444; }
        .chat-toast.toast-warning { border-color: rgba(245,158,11,0.3); }
        .chat-toast.toast-warning .toast-icon { background: rgba(245,158,11,0.15); color: #f59e0b; }
        .chat-toast.toast-success { border-color: rgba(16,185,129,0.3); }
        .chat-toast.toast-success .toast-icon { background: rgba(16,185,129,0.15); color: #10b981; }
        .toast-icon { width: 36px; height: 36px; border-radius: 10px; display: flex; align-items: center; justify-content: center; flex-shrink: 0; }
        .toast-body { flex: 1; min-width: 0; }
        .toast-title { font-size: 14px; font-weight: 700; margin-bottom: 2px; }
        .toast-msg { font-size: 12px; opacity: 0.6; line-height: 1.5; }
        .toast-close { background: none; border: none; color: inherit; cursor: pointer; opacity: 0.3; padding: 4px; font-size: 18px; line-height: 1; transition: opacity .15s; }
        .toast-close:hover { opacity: 0.8; }

        /* ===== Settings Modal ===== */
        .modal-overlay { display: none; position: fixed; inset: 0; background: rgba(0,0,0,0.6); backdrop-filter: blur(4px); z-index: 1000; align-items: center; justify-content: center; }
        .modal-overlay.visible { display: flex; }
        .modal-box { background: var(--glass-bg, #1e1e2e); border: 1px solid rgba(255,255,255,0.1); border-radius: 20px; padding: 28px; width: 100%; max-width: 440px; box-shadow: 0 24px 64px rgba(0,0,0,0.5); }
        .modal-box h3 { margin: 0 0 8px; font-size: 18px; font-weight: 700; }
        .modal-box .modal-desc { font-size: 13px; opacity: 0.4; margin: 0 0 20px; }
        .modal-field { margin-bottom: 16px; }
        .modal-field label { display: block; font-size: 12px; font-weight: 600; margin-bottom: 6px; opacity: .6; text-transform: uppercase; letter-spacing: 0.3px; }
        .modal-field input, .modal-field select { width: 100%; background: rgba(255,255,255,0.05); border: 1px solid rgba(255,255,255,0.1); border-radius: 10px; padding: 10px 14px; color: inherit; font-size: 14px; outline: none; box-sizing: border-box; transition: border-color .2s; }
        .modal-field input:focus, .modal-field select:focus { border-color: var(--color-primary, #6366f1); }
        .modal-field select option { background: #1e1e2e; color: #e0e0e0; }
        .modal-actions { display: flex; gap: 10px; justify-content: flex-end; margin-top: 24px; }
        .modal-actions button { padding: 10px 20px; border-radius: 10px; font-size: 13px; font-weight: 600; cursor: pointer; border: none; transition: all .15s; }
        .btn-modal-cancel { background: rgba(255,255,255,0.06); color: inherit; }
        .btn-modal-cancel:hover { background: rgba(255,255,255,0.1); }
        .btn-modal-save { background: var(--color-primary, #6366f1); color: #fff; }
        .btn-modal-save:hover { opacity: .85; }

        /* ===== Mobile ===== */
        .chat-sidebar-toggle { display: none; background: none; border: none; color: inherit; cursor: pointer; padding: 4px; }
        @media (max-width: 768px) {
            .chat-sidebar { position: fixed; left: 0; top: 0; bottom: 0; z-index: 500; transform: translateX(-100%); transition: transform .25s; background: var(--glass-bg, #1e1e2e); }
            .chat-sidebar.open { transform: translateX(0); }
            .chat-sidebar-toggle { display: block; }
            .msg-content { max-width: 85%; }
            .quick-prompts { grid-template-columns: 1fr; }
        }
    </style>

    <div class="chatbot-container" id="chatbot">
        {{-- Sidebar --}}
        <aside class="chat-sidebar" id="chatSidebar">
            <div class="chat-sidebar-header">
                <div class="sidebar-top">
                    <h2>Conversations</h2>
                    <button class="btn-new-chat" onclick="newConversation()">
                        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5"><path d="M12 5v14M5 12h14"/></svg>
                        New
                    </button>
                </div>
                <div class="sidebar-search">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.35-4.35"/></svg>
                    <input type="text" placeholder="Search conversations..." id="convSearch" oninput="filterConversations(this.value)">
                </div>
            </div>
            <div class="conversation-list" id="conversationList">
                @foreach($conversations as $conv)
                    <div class="conversation-item" data-id="{{ $conv->id }}" onclick="loadConversation({{ $conv->id }})">
                        <div class="conv-icon">
                            <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity="0.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                        </div>
                        <div class="conv-info">
                            <span class="conv-title">{{ $conv->title }}</span>
                            <div class="conv-meta">{{ $conv->updated_at->diffForHumans() }}</div>
                        </div>
                        <button class="conv-delete" onclick="event.stopPropagation(); deleteConversation({{ $conv->id }})" title="Delete">&times;</button>
                    </div>
                @endforeach
            </div>
        </aside>

        {{-- Main --}}
        <div class="chat-main">
            <div class="chat-topbar">
                <button class="chat-sidebar-toggle" onclick="document.getElementById('chatSidebar').classList.toggle('open')">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M3 12h18M3 6h18M3 18h18"/></svg>
                </button>
                <span class="conv-title-display" id="activeConvTitle">Select or start a conversation</span>

                <div class="topbar-actions">
                    <div class="model-badge" id="modelBadge" style="display:none;">
                        <span class="dot"></span>
                        <span id="modelBadgeText">{{ $defaultProvider === 'gemini' ? 'Gemini' : 'OpenAI' }}</span>
                    </div>

                    {{-- Model selector for users --}}
                    <select class="model-select" id="modelSelect" style="display:none;" onchange="updateModelPreference(this.value)">
                        <option value="">Auto</option>
                        <optgroup label="Gemini">
                            <option value="gemini-2.5-flash">gemini-2.5-flash</option>
                            <option value="gemini-2.5-pro">gemini-2.5-pro</option>
                            <option value="gemini-2.0-flash">gemini-2.0-flash</option>
                        </optgroup>
                        <optgroup label="OpenAI">
                            <option value="gpt-4o-mini">gpt-4o-mini</option>
                            <option value="gpt-4o">gpt-4o</option>
                            <option value="gpt-4-turbo">gpt-4-turbo</option>
                        </optgroup>
                    </select>

                    @if($allowUserKey)
                    <select class="provider-select" id="providerSelect" onchange="updateProviderPreference(this.value)">
                        <option value="" {{ !$userSettings || !$userSettings->provider ? 'selected' : '' }}>Auto</option>
                        <option value="openai" {{ $userSettings && $userSettings->provider === 'openai' ? 'selected' : '' }}>OpenAI</option>
                        <option value="gemini" {{ $userSettings && $userSettings->provider === 'gemini' ? 'selected' : '' }}>Gemini</option>
                    </select>
                    <button class="btn-icon" onclick="openSettingsModal()" title="API Key Settings">
                        <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                            <path d="M12.22 2h-.44a2 2 0 0 0-2 2v.18a2 2 0 0 1-1 1.73l-.43.25a2 2 0 0 1-2 0l-.15-.08a2 2 0 0 0-2.73.73l-.22.38a2 2 0 0 0 .73 2.73l.15.1a2 2 0 0 1 1 1.72v.51a2 2 0 0 1-1 1.74l-.15.09a2 2 0 0 0-.73 2.73l.22.38a2 2 0 0 0 2.73.73l.15-.08a2 2 0 0 1 2 0l.43.25a2 2 0 0 1 1 1.73V20a2 2 0 0 0 2 2h.44a2 2 0 0 0 2-2v-.18a2 2 0 0 1 1-1.73l.43-.25a2 2 0 0 1 2 0l.15.08a2 2 0 0 0 2.73-.73l.22-.39a2 2 0 0 0-.73-2.73l-.15-.08a2 2 0 0 1-1-1.74v-.5a2 2 0 0 1 1-1.74l.15-.09a2 2 0 0 0 .73-2.73l-.22-.38a2 2 0 0 0-2.73-.73l-.15.08a2 2 0 0 1-2 0l-.43-.25a2 2 0 0 1-1-1.73V4a2 2 0 0 0-2-2z"/>
                            <circle cx="12" cy="12" r="3"/>
                        </svg>
                    </button>
                    @endif
                </div>
            </div>

            <div class="chat-messages" id="chatMessages">
                <div class="chat-empty" id="chatEmpty">
                    <div class="empty-content">
                        <div class="empty-icon">
                            <svg width="36" height="36" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5" opacity="0.7">
                                <path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/>
                            </svg>
                        </div>
                        <h3>How can I help you?</h3>
                        <p>Start a new conversation or pick one from the sidebar. Try one of these:</p>
                        <div class="quick-prompts">
                            <button class="quick-prompt" onclick="quickStart('Explain how this platform works')">
                                <div class="qp-title">💡 Explain</div>
                                <div class="qp-desc">How this platform works</div>
                            </button>
                            <button class="quick-prompt" onclick="quickStart('Help me write a summary')">
                                <div class="qp-title">✍️ Write</div>
                                <div class="qp-desc">Help me write something</div>
                            </button>
                            <button class="quick-prompt" onclick="quickStart('What can you help me with?')">
                                <div class="qp-title">🤖 Capabilities</div>
                                <div class="qp-desc">What can you do?</div>
                            </button>
                            <button class="quick-prompt" onclick="quickStart('Write me a sample code in JavaScript')">
                                <div class="qp-title">💻 Code</div>
                                <div class="qp-desc">Write sample code</div>
                            </button>
                        </div>
                    </div>
                </div>
                <div class="typing-row" id="typingIndicator">
                    <div class="msg-avatar" style="background: linear-gradient(135deg, #6366f1, #8b5cf6);">
                        <svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>
                    </div>
                    <div class="typing-bubble">
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                        <div class="typing-dot"></div>
                    </div>
                </div>
            </div>

            <div class="chat-input-area" id="chatInputArea" style="display:none;">
                <div class="input-wrapper">
                    <div class="attachment-preview-bar" id="attachmentPreview"></div>
                    <textarea id="chatInput" placeholder="Type your message..." rows="1" onkeydown="handleInputKey(event)"></textarea>
                    <div class="input-actions">
                        <button class="btn-attach" id="btnAttach" onclick="document.getElementById('fileInput').click()" title="Upload file or image">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M21.44 11.05l-9.19 9.19a6 6 0 0 1-8.49-8.49l9.19-9.19a4 4 0 0 1 5.66 5.66l-9.2 9.19a2 2 0 0 1-2.83-2.83l8.49-8.48"/></svg>
                        </button>
                        <button class="btn-image-gen" id="btnImageGen" onclick="openImageGenModal()" title="Generate image with AI">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                        </button>
                    </div>
                    <input type="file" id="fileInput" multiple accept="image/*,.pdf,.txt,.csv,.md,.doc,.docx" style="display:none" onchange="handleFileSelect(event)">
                </div>
                <button class="btn-send" id="btnSend" onclick="sendMessage()" title="Send message">
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                        <path d="M22 2L11 13M22 2l-7 20-4-9-9-4 20-7z"/>
                    </svg>
                </button>
            </div>

            {{-- Drag & Drop Overlay --}}
            <div class="drop-overlay" id="dropOverlay">
                <div class="drop-overlay-content">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="1.5"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="17 8 12 3 7 8"/><line x1="12" y1="3" x2="12" y2="15"/></svg>
                    <p>Drop files here to upload</p>
                </div>
            </div>
        </div>
    </div>

    {{-- Toast Container --}}
    <div class="chat-toast" id="chatToast">
        <div class="toast-icon">
            <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="12" y1="8" x2="12" y2="12"/><line x1="12" y1="16" x2="12.01" y2="16"/></svg>
        </div>
        <div class="toast-body">
            <div class="toast-title" id="toastTitle"></div>
            <div class="toast-msg" id="toastMsg"></div>
        </div>
        <button class="toast-close" onclick="hideToast()">&times;</button>
    </div>

    {{-- Settings Modal --}}
    @if($allowUserKey)
    <div class="modal-overlay" id="settingsModal">
        <div class="modal-box">
            <h3>API Key Settings</h3>
            <p class="modal-desc">Configure your own API keys to use the chatbot.</p>
            <div class="modal-field">
                <label>Preferred Provider</label>
                <select id="modalProvider">
                    <option value="">Auto (use admin default)</option>
                    <option value="openai">OpenAI</option>
                    <option value="gemini">Gemini</option>
                </select>
            </div>
            <div class="modal-field">
                <label>OpenAI API Key</label>
                <input type="password" id="modalOpenaiKey" placeholder="sk-...">
            </div>
            <div class="modal-field">
                <label>Gemini API Key</label>
                <input type="password" id="modalGeminiKey" placeholder="AIza...">
            </div>
            <div class="modal-actions">
                <button class="btn-modal-cancel" onclick="closeSettingsModal()">Cancel</button>
                <button class="btn-modal-save" onclick="saveUserSettings()">Save</button>
            </div>
        </div>
    </div>
    @endif

    {{-- Image Lightbox --}}
    <div class="image-lightbox" id="imageLightbox" onclick="closeLightbox()">
        <img id="lightboxImg" src="" alt="Preview">
    </div>

    {{-- Image Generation Modal --}}
    <div class="image-gen-modal" id="imageGenModal">
        <div class="image-gen-box">
            <h3>🎨 Generate Image</h3>
            <p class="modal-desc">Describe the image you want to create. Works with DALL-E (OpenAI) or Imagen (Gemini).</p>
            <div class="modal-field">
                <label>Image Description</label>
                <textarea id="imageGenPrompt" placeholder="A serene mountain landscape at sunset with vibrant colors..."></textarea>
            </div>
            <div class="modal-actions">
                <button class="btn-modal-cancel" onclick="closeImageGenModal()">Cancel</button>
                <button class="btn-modal-save" id="btnGenImage" onclick="generateImage()">
                    <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg>
                    Generate
                </button>
            </div>
        </div>
    </div>

    <script>
        const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content || '{{ csrf_token() }}';
        let activeConvId = null;
        let selectedModel = localStorage.getItem('chatbot_model') || '';
        let toastTimer = null;
        let pendingAttachments = []; // { path, url, name, mime, is_image, size, uploading? }

        // Set initial model selector value
        (function() {
            const ms = document.getElementById('modelSelect');
            if (ms && selectedModel) ms.value = selectedModel;
        })();

        // Configure marked for code highlighting
        marked.setOptions({
            highlight: function(code, lang) {
                if (lang && hljs.getLanguage(lang)) {
                    return hljs.highlight(code, { language: lang }).value;
                }
                return hljs.highlightAuto(code).value;
            },
            breaks: true,
            gfm: true
        });

        function apiHeaders() {
            return { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' };
        }

        // ===== Toast Notification =====
        function showToast(title, msg, type) {
            type = type || 'error';
            const toast = document.getElementById('chatToast');
            document.getElementById('toastTitle').textContent = title;
            document.getElementById('toastMsg').textContent = msg;
            toast.className = 'chat-toast toast-' + type;
            const icon = toast.querySelector('.toast-icon');
            if (type === 'warning') {
                icon.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"/><line x1="12" y1="9" x2="12" y2="13"/><line x1="12" y1="17" x2="12.01" y2="17"/></svg>';
            } else if (type === 'error') {
                icon.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><circle cx="12" cy="12" r="10"/><line x1="15" y1="9" x2="9" y2="15"/><line x1="9" y1="9" x2="15" y2="15"/></svg>';
            } else {
                icon.innerHTML = '<svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg>';
            }
            requestAnimationFrame(function() { toast.classList.add('visible'); });
            clearTimeout(toastTimer);
            toastTimer = setTimeout(hideToast, 8000);
        }
        function hideToast() {
            document.getElementById('chatToast').classList.remove('visible');
            clearTimeout(toastTimer);
        }

        // ===== Markdown Rendering =====
        function renderMarkdown(text) {
            var html = marked.parse(text);

            // Wrap code blocks with language header + copy button
            html = html.replace(/<pre><code class="language-(\w+)">([\s\S]*?)<\/code><\/pre>/g, function(match, lang, code) {
                var id = 'code-' + Math.random().toString(36).substr(2, 9);
                return '<div class="code-block-wrapper">'
                    + '<div class="code-block-header"><span>' + lang + '</span>'
                    + '<button class="code-copy-btn" onclick="copyCodeBlock(\'' + id + '\', this)">'
                    + '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>'
                    + ' Copy</button></div>'
                    + '<pre><code id="' + id + '" class="language-' + lang + '">' + code + '</code></pre></div>';
            });

            // Handle code blocks without language
            html = html.replace(/<pre><code>([\s\S]*?)<\/code><\/pre>/g, function(match, code) {
                var id = 'code-' + Math.random().toString(36).substr(2, 9);
                return '<div class="code-block-wrapper">'
                    + '<div class="code-block-header"><span>code</span>'
                    + '<button class="code-copy-btn" onclick="copyCodeBlock(\'' + id + '\', this)">'
                    + '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>'
                    + ' Copy</button></div>'
                    + '<pre><code id="' + id + '">' + code + '</code></pre></div>';
            });

            return html;
        }

        function copyCodeBlock(id, btn) {
            var el = document.getElementById(id);
            if (!el) return;
            navigator.clipboard.writeText(el.textContent).then(function() {
                btn.classList.add('copied');
                btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Copied!';
                setTimeout(function() {
                    btn.classList.remove('copied');
                    btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg> Copy';
                }, 2000);
            });
        }

        function copyFullMessage(btn) {
            var bubble = btn.closest('.msg-content').querySelector('.msg-bubble');
            if (!bubble) return;
            navigator.clipboard.writeText(bubble.innerText).then(function() {
                btn.classList.add('copied');
                var orig = btn.innerHTML;
                btn.innerHTML = '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M20 6L9 17l-5-5"/></svg> Copied';
                setTimeout(function() { btn.classList.remove('copied'); btn.innerHTML = orig; }, 2000);
            });
        }

        // ===== Model Selection =====
        function updateModelPreference(model) {
            selectedModel = model;
            localStorage.setItem('chatbot_model', model);
        }

        // ===== Quick Start =====
        async function quickStart(message) {
            await newConversation();
            if (activeConvId) {
                document.getElementById('chatInput').value = message;
                sendMessage();
            }
        }

        // ===== Search / Filter =====
        function filterConversations(query) {
            var items = document.querySelectorAll('.conversation-item');
            var q = query.toLowerCase();
            items.forEach(function(el) {
                var title = el.querySelector('.conv-title').textContent.toLowerCase();
                el.style.display = title.includes(q) ? '' : 'none';
            });
        }

        // ===== Conversations =====
        async function newConversation() {
            var res = await fetch('{{ route("chatbot.conversation.new") }}', {
                method: 'POST', headers: apiHeaders()
            });
            var data = await res.json();
            if (data.id) {
                addConversationToList(data.id, data.title);
                loadConversation(data.id);
            }
        }

        function addConversationToList(id, title) {
            var list = document.getElementById('conversationList');
            var el = document.createElement('div');
            el.className = 'conversation-item';
            el.dataset.id = id;
            el.onclick = function() { loadConversation(id); };
            el.innerHTML = '<div class="conv-icon">'
                + '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity="0.5"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>'
                + '</div><div class="conv-info">'
                + '<span class="conv-title">' + escapeHtml(title) + '</span>'
                + '<div class="conv-meta">Just now</div>'
                + '</div><button class="conv-delete" onclick="event.stopPropagation(); deleteConversation(' + id + ')" title="Delete">&times;</button>';
            list.prepend(el);
        }

        async function loadConversation(id) {
            activeConvId = id;
            sessionStorage.setItem('chatbot_active_conv', id);

            document.querySelectorAll('.conversation-item').forEach(function(el) {
                el.classList.toggle('active', parseInt(el.dataset.id) === id);
            });

            document.getElementById('chatInputArea').style.display = 'flex';
            document.getElementById('modelBadge').style.display = 'flex';
            document.getElementById('modelSelect').style.display = '';

            var activeEl = document.querySelector('.conversation-item[data-id="' + id + '"] .conv-title');
            document.getElementById('activeConvTitle').textContent = activeEl ? activeEl.textContent : 'Chat';

            // Clear messages area — remove all msg-row elements, keep typing indicator
            var container = document.getElementById('chatMessages');
            var chatEmpty = document.getElementById('chatEmpty');
            if (chatEmpty) chatEmpty.remove();
            container.querySelectorAll('.msg-row').forEach(function(el) { el.remove(); });

            var res = await fetch('/chatbot/conversation/' + id + '/messages', { headers: apiHeaders() });
            var data = await res.json();

            if (data.messages) {
                data.messages.forEach(function(msg) { appendMessage(msg.role, msg.content, msg.attachments || []); });
            }
            scrollToBottom();
            document.getElementById('chatSidebar').classList.remove('open');
        }

        async function deleteConversation(id) {
            if (!confirm('Delete this conversation?')) return;
            await fetch('/chatbot/conversation/' + id, {
                method: 'DELETE', headers: apiHeaders()
            });
            var el = document.querySelector('.conversation-item[data-id="' + id + '"]');
            if (el) el.remove();
            if (activeConvId === id) {
                activeConvId = null;
                sessionStorage.removeItem('chatbot_active_conv');
                document.getElementById('chatInputArea').style.display = 'none';
                document.getElementById('modelBadge').style.display = 'none';
                document.getElementById('modelSelect').style.display = 'none';
                var container = document.getElementById('chatMessages');
                container.querySelectorAll('.msg-row').forEach(function(el) { el.remove(); });
                document.getElementById('activeConvTitle').textContent = 'Select or start a conversation';
            }
        }

        // ===== Messages =====
        async function sendMessage() {
            if (!activeConvId) return;
            var input = document.getElementById('chatInput');
            var message = input.value.trim();
            var attachments = pendingAttachments.filter(function(a) { return !a.uploading; });
            var stillUploading = pendingAttachments.some(function(a) { return a.uploading; });

            if (stillUploading) {
                showToast('Please Wait', 'Files are still uploading...', 'warning');
                return;
            }
            if (!message && attachments.length === 0) return;

            input.value = '';
            input.style.height = 'auto';

            // Build attachment data for display
            var displayAttachments = attachments.map(function(a) {
                return { url: a.url, name: a.name, mime: a.mime, is_image: a.is_image };
            });

            appendMessage('user', message, displayAttachments);
            pendingAttachments = [];
            renderAttachmentPreviews();
            scrollToBottom();

            document.getElementById('typingIndicator').classList.add('visible');
            document.getElementById('btnSend').disabled = true;
            scrollToBottom();

            try {
                var body = { message: message || '' };
                if (selectedModel) body.model = selectedModel;
                if (attachments.length > 0) {
                    body.attachments = attachments.map(function(a) {
                        return { path: a.path, url: a.url, name: a.name, mime: a.mime, is_image: a.is_image };
                    });
                }

                var res = await fetch('/chatbot/conversation/' + activeConvId + '/send', {
                    method: 'POST',
                    headers: apiHeaders(),
                    body: JSON.stringify(body)
                });
                var data = await res.json();

                document.getElementById('typingIndicator').classList.remove('visible');
                document.getElementById('btnSend').disabled = false;

                if (data.error) {
                    var errMsg = data.error;
                    // Detect quota/rate limit errors
                    var lower = errMsg.toLowerCase();
                    if (lower.includes('quota') || lower.includes('rate limit') || lower.includes('exceeded') || lower.includes('429') || lower.includes('resource has been exhausted')) {
                        showToast('Rate Limit Exceeded', 'You have exceeded the API quota for this model. Try a different model or wait before retrying.', 'warning');
                    } else if (lower.includes('api key')) {
                        showToast('API Key Error', errMsg, 'error');
                    } else {
                        showToast('Error', errMsg, 'error');
                    }
                    appendMessage('assistant', '\u26a0 ' + errMsg);
                } else {
                    // Build attachments for generated images
                    var replyAttachments = [];
                    if (data.generated_images && data.generated_images.length) {
                        data.generated_images.forEach(function(url) {
                            replyAttachments.push({ url: url, is_image: true, name: 'Generated Image', mime: 'image/png' });
                        });
                    }
                    appendMessage('assistant', data.reply, replyAttachments);
                    if (data.model) {
                        document.getElementById('modelBadgeText').textContent = data.model;
                    }
                    if (data.title) {
                        var titleEl = document.querySelector('.conversation-item[data-id="' + activeConvId + '"] .conv-title');
                        if (titleEl) titleEl.textContent = data.title;
                        document.getElementById('activeConvTitle').textContent = data.title;
                    }
                }
            } catch (err) {
                document.getElementById('typingIndicator').classList.remove('visible');
                document.getElementById('btnSend').disabled = false;
                showToast('Network Error', 'Could not reach the server. Please check your connection.', 'error');
                appendMessage('assistant', '\u26a0 Network error. Please try again.');
            }
            scrollToBottom();
        }

        function appendMessage(role, content, attachments) {
            attachments = attachments || [];
            var container = document.getElementById('chatMessages');
            var typing = document.getElementById('typingIndicator');
            var row = document.createElement('div');
            row.className = 'msg-row msg-row-' + role;

            var avatarIcon = role === 'assistant'
                ? '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="#fff" stroke-width="2"><path d="M21 15a2 2 0 0 1-2 2H7l-4 4V5a2 2 0 0 1 2-2h14a2 2 0 0 1 2 2z"/></svg>'
                : '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity="0.6"><path d="M20 21v-2a4 4 0 0 0-4-4H8a4 4 0 0 0-4 4v2"/><circle cx="12" cy="7" r="4"/></svg>';

            var now = new Date();
            var time = now.toLocaleTimeString([], { hour: '2-digit', minute: '2-digit' });

            // Build attachments HTML
            var attHtml = '';
            if (attachments.length > 0) {
                attHtml = '<div class="msg-attachments">';
                attachments.forEach(function(att) {
                    if (att.is_image) {
                        attHtml += '<img class="msg-attachment-img" src="' + escapeHtml(att.url) + '" alt="' + escapeHtml(att.name || 'Image') + '" onclick="openLightbox(\'' + escapeHtml(att.url) + '\')" loading="lazy">';
                    } else {
                        attHtml += '<a class="msg-attachment-file" href="' + escapeHtml(att.url) + '" target="_blank" rel="noopener">'
                            + '<svg width="16" height="16" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity=".6"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg>'
                            + escapeHtml(att.name || 'File') + '</a>';
                    }
                });
                attHtml += '</div>';
            }

            if (role === 'assistant') {
                var isError = content.charAt(0) === '\u26a0';
                var rendered = isError ? '<p>' + escapeHtml(content) + '</p>' : renderMarkdown(content);
                row.innerHTML = '<div class="msg-avatar">' + avatarIcon + '</div>'
                    + '<div class="msg-content">'
                    + attHtml
                    + '<div class="msg-bubble">' + rendered + '</div>'
                    + '<div class="msg-actions">'
                    + '<button class="msg-action-btn" onclick="copyFullMessage(this)" title="Copy message">'
                    + '<svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><rect x="9" y="9" width="13" height="13" rx="2" ry="2"/><path d="M5 15H4a2 2 0 0 1-2-2V4a2 2 0 0 1 2-2h9a2 2 0 0 1 2 2v1"/></svg>'
                    + ' Copy</button></div>'
                    + '<div class="msg-time">' + time + '</div></div>';
            } else {
                row.innerHTML = '<div class="msg-avatar">' + avatarIcon + '</div>'
                    + '<div class="msg-content">'
                    + attHtml
                    + '<div class="msg-bubble"></div>'
                    + '<div class="msg-time">' + time + '</div></div>';
                row.querySelector('.msg-bubble').textContent = content;
            }

            container.insertBefore(row, typing);
        }

        function scrollToBottom() {
            var el = document.getElementById('chatMessages');
            el.scrollTop = el.scrollHeight;
        }

        function handleInputKey(e) {
            if (e.key === 'Enter' && !e.shiftKey) {
                e.preventDefault();
                sendMessage();
            }
            var ta = e.target;
            ta.style.height = 'auto';
            ta.style.height = Math.min(ta.scrollHeight, 120) + 'px';
        }

        // ===== Settings Modal =====
        function openSettingsModal() {
            document.getElementById('settingsModal').classList.add('visible');
            var sel = document.getElementById('providerSelect');
            if (sel) document.getElementById('modalProvider').value = sel.value;
        }
        function closeSettingsModal() {
            document.getElementById('settingsModal').classList.remove('visible');
        }

        async function saveUserSettings() {
            var body = {
                provider: document.getElementById('modalProvider').value || null,
                openai_api_key: document.getElementById('modalOpenaiKey').value,
                gemini_api_key: document.getElementById('modalGeminiKey').value,
            };
            var res = await fetch('{{ route("chatbot.settings.update") }}', {
                method: 'POST', headers: apiHeaders(), body: JSON.stringify(body)
            });
            if (res.ok) {
                closeSettingsModal();
                var sel = document.getElementById('providerSelect');
                if (sel && body.provider) sel.value = body.provider;
                document.getElementById('modalOpenaiKey').value = '';
                document.getElementById('modalGeminiKey').value = '';
                showToast('Settings Saved', 'Your API key settings have been updated.', 'success');
            }
        }

        function updateProviderPreference(value) {
            fetch('{{ route("chatbot.settings.update") }}', {
                method: 'POST', headers: apiHeaders(), body: JSON.stringify({ provider: value || null })
            });
        }

        function escapeHtml(text) {
            var d = document.createElement('div');
            d.textContent = text;
            return d.innerHTML;
        }

        // ===== File / Image Upload =====
        function handleFileSelect(e) {
            var files = e.target.files;
            if (files) {
                Array.from(files).forEach(function(file) { uploadFile(file); });
            }
            e.target.value = '';
        }

        async function uploadFile(file) {
            if (pendingAttachments.length >= 5) {
                showToast('Limit Reached', 'Maximum 5 attachments per message.', 'warning');
                return;
            }

            var allowed = ['image/jpeg','image/png','image/gif','image/webp','application/pdf','text/plain','text/csv','text/markdown',
                          'application/msword','application/vnd.openxmlformats-officedocument.wordprocessingml.document'];
            if (!allowed.includes(file.type) && !file.type.startsWith('image/')) {
                showToast('Invalid File', 'Supported: images, PDF, TXT, CSV, MD, DOC.', 'error');
                return;
            }
            if (file.size > 20 * 1024 * 1024) {
                showToast('File Too Large', 'Maximum file size is 20MB.', 'error');
                return;
            }

            var tempId = 'att-' + Date.now() + '-' + Math.random().toString(36).substr(2,5);
            var isImage = file.type.startsWith('image/');
            var previewUrl = isImage ? URL.createObjectURL(file) : null;

            var att = { tempId: tempId, name: file.name, mime: file.type, is_image: isImage, size: file.size, uploading: true, previewUrl: previewUrl };
            pendingAttachments.push(att);
            renderAttachmentPreviews();

            var formData = new FormData();
            formData.append('file', file);

            try {
                var res = await fetch('{{ route("chatbot.upload-attachment") }}', {
                    method: 'POST',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: formData
                });
                var data = await res.json();

                if (res.ok) {
                    var idx = pendingAttachments.findIndex(function(a) { return a.tempId === tempId; });
                    if (idx !== -1) {
                        pendingAttachments[idx] = Object.assign(pendingAttachments[idx], data, { uploading: false });
                    }
                } else {
                    pendingAttachments = pendingAttachments.filter(function(a) { return a.tempId !== tempId; });
                    showToast('Upload Failed', data.message || 'Could not upload file.', 'error');
                }
            } catch (err) {
                pendingAttachments = pendingAttachments.filter(function(a) { return a.tempId !== tempId; });
                showToast('Upload Error', 'Network error uploading file.', 'error');
            }
            renderAttachmentPreviews();
        }

        function removeAttachment(tempId) {
            pendingAttachments = pendingAttachments.filter(function(a) { return a.tempId !== tempId; });
            renderAttachmentPreviews();
        }

        function renderAttachmentPreviews() {
            var bar = document.getElementById('attachmentPreview');
            var btn = document.getElementById('btnAttach');
            if (pendingAttachments.length === 0) {
                bar.classList.remove('visible');
                bar.innerHTML = '';
                btn.classList.remove('has-files');
                return;
            }
            btn.classList.add('has-files');
            bar.classList.add('visible');
            bar.innerHTML = pendingAttachments.map(function(att) {
                var preview = att.is_image
                    ? '<img src="' + (att.previewUrl || att.url) + '" alt="' + escapeHtml(att.name) + '">'
                    : '<div class="file-icon"><svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" opacity=".5"><path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"/><polyline points="14 2 14 8 20 8"/></svg></div>';
                var loading = att.uploading ? '<div class="att-uploading"><div class="att-spinner"></div></div>' : '';
                return '<div class="attachment-preview-item">'
                    + preview
                    + '<div class="att-info"><span class="att-name">' + escapeHtml(att.name) + '</span><span class="att-size">' + formatSize(att.size) + '</span></div>'
                    + '<button class="att-remove" onclick="removeAttachment(\'' + att.tempId + '\')">&times;</button>'
                    + loading
                    + '</div>';
            }).join('');
        }

        function formatSize(bytes) {
            if (bytes < 1024) return bytes + ' B';
            if (bytes < 1048576) return (bytes / 1024).toFixed(1) + ' KB';
            return (bytes / 1048576).toFixed(1) + ' MB';
        }

        // ===== Paste Image Support =====
        document.addEventListener('paste', function(e) {
            if (!activeConvId) return;
            var items = e.clipboardData && e.clipboardData.items;
            if (!items) return;
            for (var i = 0; i < items.length; i++) {
                if (items[i].type.startsWith('image/')) {
                    e.preventDefault();
                    var file = items[i].getAsFile();
                    if (file) uploadFile(file);
                }
            }
        });

        // ===== Drag & Drop Support =====
        (function() {
            var chatMain = document.querySelector('.chat-main');
            var overlay = document.getElementById('dropOverlay');
            var dragCounter = 0;

            chatMain.addEventListener('dragenter', function(e) {
                e.preventDefault();
                dragCounter++;
                if (activeConvId) overlay.classList.add('visible');
            });
            chatMain.addEventListener('dragleave', function(e) {
                e.preventDefault();
                dragCounter--;
                if (dragCounter <= 0) { overlay.classList.remove('visible'); dragCounter = 0; }
            });
            chatMain.addEventListener('dragover', function(e) { e.preventDefault(); });
            chatMain.addEventListener('drop', function(e) {
                e.preventDefault();
                dragCounter = 0;
                overlay.classList.remove('visible');
                if (!activeConvId) return;
                var files = e.dataTransfer.files;
                if (files) Array.from(files).forEach(function(f) { uploadFile(f); });
            });
        })();

        // ===== Image Generation =====
        function openImageGenModal() {
            if (!activeConvId) {
                showToast('No Conversation', 'Start or select a conversation first.', 'warning');
                return;
            }
            document.getElementById('imageGenModal').classList.add('visible');
            document.getElementById('imageGenPrompt').focus();
        }
        function closeImageGenModal() {
            document.getElementById('imageGenModal').classList.remove('visible');
            document.getElementById('imageGenPrompt').value = '';
        }

        async function generateImage() {
            var prompt = document.getElementById('imageGenPrompt').value.trim();
            if (!prompt) return;

            var btn = document.getElementById('btnGenImage');
            btn.disabled = true;
            btn.textContent = 'Generating...';
            closeImageGenModal();

            appendMessage('user', '🎨 Generate image: ' + prompt);
            document.getElementById('typingIndicator').classList.add('visible');
            scrollToBottom();

            try {
                var body = { prompt: prompt };
                if (selectedModel) body.model = selectedModel;

                var res = await fetch('/chatbot/conversation/' + activeConvId + '/generate-image', {
                    method: 'POST',
                    headers: apiHeaders(),
                    body: JSON.stringify(body)
                });
                var data = await res.json();
                document.getElementById('typingIndicator').classList.remove('visible');

                if (data.error) {
                    showToast('Image Generation Failed', data.error, 'error');
                    appendMessage('assistant', '⚠ ' + data.error);
                } else {
                    appendMessage('assistant', data.reply, data.attachments || []);
                }
            } catch (err) {
                document.getElementById('typingIndicator').classList.remove('visible');
                showToast('Network Error', 'Could not generate image.', 'error');
                appendMessage('assistant', '⚠ Network error generating image.');
            }
            btn.disabled = false;
            btn.innerHTML = '<svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" style="margin-right:4px"><rect x="3" y="3" width="18" height="18" rx="2" ry="2"/><circle cx="8.5" cy="8.5" r="1.5"/><polyline points="21 15 16 10 5 21"/></svg> Generate';
            scrollToBottom();
        }

        // ===== Image Lightbox =====
        function openLightbox(url) {
            document.getElementById('lightboxImg').src = url;
            document.getElementById('imageLightbox').classList.add('visible');
        }
        function closeLightbox() {
            document.getElementById('imageLightbox').classList.remove('visible');
            document.getElementById('lightboxImg').src = '';
        }
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') closeLightbox();
        });

        // ===== Restore last active conversation on page load =====
        (function() {
            var savedConvId = sessionStorage.getItem('chatbot_active_conv');
            if (savedConvId) {
                var convEl = document.querySelector('.conversation-item[data-id="' + savedConvId + '"]');
                if (convEl) {
                    loadConversation(parseInt(savedConvId));
                }
            }
        })();
    </script>
</x-app-layout>
