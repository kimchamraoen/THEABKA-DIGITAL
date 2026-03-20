<x-app-layout>
    <div class="settings-wrap">
        <div class="settings-head">
            <h1>Icon Manager</h1>
            <p>Manage sidebar, landing, and auth icons. Fallback SVGs remain active where configured in views.</p>
        </div>

        @if (session('status'))
            <div class="notice success">{{ session('status') }}</div>
        @endif

        <div class="toolbar">
            <a href="{{ route('admin.settings', ['section' => 'sidebar']) }}" class="link-btn">Back to Settings</a>
            <a href="{{ route('admin.settings.nav-labels') }}" class="link-btn">Navigation Labels</a>
        </div>

        <div class="icon-table-wrap">
            <table class="icon-table">
                <thead>
                    <tr>
                        <th>Preview</th>
                        <th>Key</th>
                        <th>Label</th>
                        <th>Page</th>
                        <th>Type</th>
                        <th>Value</th>
                        <th>Action</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($icons as $icon)
                        <tr>
                            <td class="preview-cell">{!! get_icon($icon->key, '<span>?</span>') !!}</td>
                            <td><code>{{ $icon->key }}</code></td>
                            <td>{{ $icon->label }}</td>
                            <td>{{ $icon->page }}</td>
                            <td>{{ $icon->icon_type }}</td>
                            <td class="value-cell"><code>{{ \Illuminate\Support\Str::limit((string) $icon->icon_value, 80) }}</code></td>
                            <td>
                                <button type="button" class="link-btn js-toggle" data-target="editor-{{ $icon->id }}">Edit</button>
                            </td>
                        </tr>
                        <tr id="editor-{{ $icon->id }}" class="editor-row" hidden>
                            <td colspan="7">
                                <form method="POST" action="{{ route('admin.settings.icons.update', $icon->key) }}" enctype="multipart/form-data" class="editor-grid js-editor">
                                    @csrf
                                    <label>
                                        <span>Label</span>
                                        <input type="text" name="label" value="{{ $icon->label }}" required>
                                    </label>

                                    <label>
                                        <span>Page</span>
                                        <input type="text" name="page" value="{{ $icon->page }}" required>
                                    </label>

                                    <fieldset>
                                        <legend>Type</legend>
                                        <label><input type="radio" name="icon_type" value="css_class" {{ $icon->icon_type === 'css_class' ? 'checked' : '' }}> CSS Class</label>
                                        <label><input type="radio" name="icon_type" value="svg" {{ $icon->icon_type === 'svg' ? 'checked' : '' }}> SVG Code</label>
                                        <label><input type="radio" name="icon_type" value="emoji" {{ $icon->icon_type === 'emoji' ? 'checked' : '' }}> Emoji</label>
                                        <label><input type="radio" name="icon_type" value="image" {{ $icon->icon_type === 'image' ? 'checked' : '' }}> Upload Image</label>
                                    </fieldset>

                                    <label class="js-value-wrap" data-type="css_class">
                                        <span>CSS Class</span>
                                        <input class="js-value" type="text" name="icon_value" value="{{ $icon->icon_value }}" placeholder="fas fa-home">
                                    </label>

                                    <label class="js-value-wrap" data-type="svg">
                                        <span>SVG Code</span>
                                        <textarea class="js-value" name="icon_value" rows="4" placeholder="<svg>...</svg>">{{ $icon->icon_value }}</textarea>
                                    </label>

                                    <label class="js-value-wrap" data-type="emoji">
                                        <span>Emoji</span>
                                        <input class="js-value" type="text" name="icon_value" value="{{ $icon->icon_value }}" placeholder="🏠">
                                    </label>

                                    <label class="js-value-wrap" data-type="image">
                                        <span>Image Upload</span>
                                        <input type="file" name="icon_image" class="js-image-input" accept="image/png,image/jpeg,image/jpg,image/webp">
                                        <small class="help-text">Allowed: PNG/JPG/WEBP, max 512KB, max 128x128px. Recommended: square 24x24 or 32x32.</small>
                                    </label>

                                    <div class="live-preview">
                                        <span>Live Preview</span>
                                        <div class="preview-box js-live-preview">{!! get_icon($icon->key, '<span>?</span>') !!}</div>
                                    </div>

                                    <div class="actions">
                                        <button type="submit">Save</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <style>
        .settings-wrap { padding: 24px; }
        .settings-head h1 { margin: 0 0 6px; font-size: 24px; }
        .settings-head p { margin: 0 0 18px; opacity: 0.7; }
        .notice { padding: 10px 12px; border-radius: 8px; margin-bottom: 14px; }
        .notice.success { background: #102a1a; border: 1px solid #2f7d4f; color: #9ad5b0; }
        .toolbar { display: flex; gap: 10px; margin-bottom: 14px; }
        .link-btn { display: inline-block; padding: 7px 12px; border: 1px solid #4a5568; border-radius: 8px; color: #e2e8f0; background: #1a202c; text-decoration: none; cursor: pointer; }
        .icon-table-wrap { overflow: auto; border: 1px solid #2d3748; border-radius: 10px; }
        .icon-table { width: 100%; border-collapse: collapse; min-width: 980px; }
        .icon-table th, .icon-table td { border-bottom: 1px solid #2d3748; padding: 10px; text-align: left; vertical-align: top; }
        .icon-table th { font-size: 12px; text-transform: uppercase; letter-spacing: 0.04em; opacity: 0.8; }
        .preview-cell { font-size: 20px; width: 70px; }
        .value-cell code { white-space: pre-wrap; word-break: break-word; }
        .editor-row td { background: rgba(255,255,255,0.03); }
        .editor-grid { display: grid; gap: 12px; grid-template-columns: repeat(2, minmax(220px, 1fr)); }
        .editor-grid label, .editor-grid fieldset { display: flex; flex-direction: column; gap: 6px; border: 1px solid #2d3748; border-radius: 8px; padding: 10px; }
        .editor-grid legend { padding: 0 4px; font-size: 12px; opacity: 0.8; }
        .editor-grid input[type="text"], .editor-grid textarea { width: 100%; background: #0f172a; border: 1px solid #334155; color: #e2e8f0; border-radius: 6px; padding: 7px 9px; }
        .editor-grid fieldset label { border: 0; padding: 0; flex-direction: row; align-items: center; gap: 8px; }
        .live-preview { border: 1px dashed #475569; border-radius: 8px; padding: 10px; }
        .preview-box { display: inline-flex; align-items: center; justify-content: center; min-width: 44px; min-height: 44px; font-size: 24px; }
        .actions { grid-column: 1 / -1; }
        .actions button { padding: 8px 16px; border-radius: 8px; border: 1px solid #3b82f6; background: #1d4ed8; color: #fff; cursor: pointer; }
        .help-text { font-size: 12px; opacity: 0.75; }
        @media (max-width: 720px) {
            .editor-grid { grid-template-columns: 1fr; }
        }
    </style>

    <script>
        document.querySelectorAll('.js-toggle').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var row = document.getElementById(btn.dataset.target);
                row.hidden = !row.hidden;
            });
        });

        function renderPreview(type, value) {
            if (!value) return '?';
            if (type === 'css_class') return '<i class="' + value.replace(/"/g, '&quot;') + '"></i>';
            if (type === 'emoji') return value;
            if (type === 'svg') return value;
            if (type === 'image') return '<img src="' + value + '" alt="icon preview" style="width:32px;height:32px;object-fit:contain;display:block;" />';
            return '?';
        }

        document.querySelectorAll('.js-editor').forEach(function (form) {
            var radios = form.querySelectorAll('input[name="icon_type"]');
            var wraps = form.querySelectorAll('.js-value-wrap');
            var preview = form.querySelector('.js-live-preview');
            var imageInput = form.querySelector('.js-image-input');

            function currentType() {
                var selected = form.querySelector('input[name="icon_type"]:checked');
                return selected ? selected.value : 'svg';
            }

            function activeValue() {
                var type = currentType();
                var target = form.querySelector('.js-value-wrap[data-type="' + type + '"] .js-value');
                return target ? target.value : '';
            }

            function refreshFields() {
                var type = currentType();
                wraps.forEach(function (wrap) {
                    wrap.style.display = wrap.dataset.type === type ? 'flex' : 'none';
                });
                if (type === 'image') {
                    var current = activeValue();
                    if (current && !/^https?:\/\//i.test(current)) {
                        current = '/storage/' + current.replace(/^\/+/, '');
                    }
                    preview.innerHTML = current ? renderPreview('image', current) : '?';
                    return;
                }
                preview.innerHTML = renderPreview(type, activeValue());
            }

            radios.forEach(function (r) { r.addEventListener('change', refreshFields); });
            form.querySelectorAll('.js-value').forEach(function (input) {
                input.addEventListener('input', function () {
                    preview.innerHTML = renderPreview(currentType(), activeValue());
                });
            });

            if (imageInput) {
                imageInput.addEventListener('change', function () {
                    if (!imageInput.files || !imageInput.files[0]) {
                        refreshFields();
                        return;
                    }

                    var file = imageInput.files[0];
                    var reader = new FileReader();
                    reader.onload = function (e) {
                        preview.innerHTML = renderPreview('image', e.target.result);
                    };
                    reader.readAsDataURL(file);
                });
            }

            refreshFields();
        });
    </script>
</x-app-layout>
