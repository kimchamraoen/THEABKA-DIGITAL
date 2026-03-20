<x-app-layout>
    <div class="labels-wrap">
        <div class="labels-head">
            <h1>Sidebar Navigation Labels</h1>
            <p>Rename, reorder, and toggle visibility of sidebar items. Changes are cached and applied globally.</p>
        </div>

        @if (session('status'))
            <div class="notice success">{{ session('status') }}</div>
        @endif

        <div class="toolbar">
            <a href="{{ route('admin.settings', ['section' => 'sidebar']) }}" class="link-btn">Back to Sidebar Settings</a>
            <a href="{{ route('admin.settings.icons') }}" class="link-btn">Open Icon Manager</a>
        </div>

        <form method="POST" action="{{ route('admin.settings.nav-labels.update') }}" id="labelsForm">
            @csrf
            <div class="list">
                @foreach ($labels as $index => $item)
                    <div class="row" data-row>
                        <input type="hidden" name="labels[{{ $index }}][key]" value="{{ $item->key }}">
                        <input class="sort-input" type="hidden" name="labels[{{ $index }}][sort_order]" value="{{ $item->sort_order }}">

                        <div class="preview">{!! get_icon($item->key, '<span>•</span>') !!}</div>
                        <code class="key">{{ $item->key }}</code>

                        <label class="label-input">
                            <span>Label</span>
                            <input type="text" name="labels[{{ $index }}][label]" value="{{ $item->label }}" required>
                        </label>

                        <label class="toggle">
                            <input type="hidden" name="labels[{{ $index }}][is_visible]" value="0">
                            <input type="checkbox" name="labels[{{ $index }}][is_visible]" value="1" {{ $item->is_visible ? 'checked' : '' }}>
                            <span>Visible</span>
                        </label>

                        <div class="sort-tools">
                            <button type="button" class="move-up">Up</button>
                            <button type="button" class="move-down">Down</button>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="actions">
                <button type="submit">Save All</button>
            </div>
        </form>
    </div>

    <style>
        .labels-wrap { padding: 24px; }
        .labels-head h1 { margin: 0 0 6px; font-size: 24px; }
        .labels-head p { margin: 0 0 16px; opacity: 0.7; }
        .notice { padding: 10px 12px; border-radius: 8px; margin-bottom: 14px; }
        .notice.success { background: #102a1a; border: 1px solid #2f7d4f; color: #9ad5b0; }
        .toolbar { display: flex; gap: 10px; margin-bottom: 14px; }
        .link-btn { display: inline-block; padding: 7px 12px; border: 1px solid #4a5568; border-radius: 8px; color: #e2e8f0; background: #1a202c; text-decoration: none; }
        .list { display: flex; flex-direction: column; gap: 10px; }
        .row { display: grid; grid-template-columns: 52px 220px 1fr 130px 120px; gap: 10px; align-items: end; border: 1px solid #2d3748; border-radius: 10px; padding: 12px; background: rgba(255,255,255,0.02); }
        .preview { font-size: 24px; min-height: 40px; display: flex; align-items: center; justify-content: center; }
        .key { padding: 10px; border: 1px dashed #334155; border-radius: 8px; font-size: 12px; }
        .label-input { display: flex; flex-direction: column; gap: 6px; }
        .label-input input { background: #0f172a; border: 1px solid #334155; color: #e2e8f0; border-radius: 6px; padding: 7px 9px; }
        .toggle { display: flex; align-items: center; gap: 8px; min-height: 38px; }
        .sort-tools { display: flex; gap: 6px; }
        .sort-tools button { padding: 8px 10px; border-radius: 6px; border: 1px solid #334155; background: #1e293b; color: #e2e8f0; cursor: pointer; }
        .actions { margin-top: 14px; }
        .actions button { padding: 9px 16px; border-radius: 8px; border: 1px solid #3b82f6; background: #1d4ed8; color: #fff; cursor: pointer; }
        @media (max-width: 900px) {
            .row { grid-template-columns: 52px 1fr; }
            .sort-tools { grid-column: span 2; }
            .key, .label-input, .toggle { grid-column: span 2; }
        }
    </style>

    <script>
        function refreshSortValues() {
            document.querySelectorAll('[data-row]').forEach(function (row, index) {
                var input = row.querySelector('.sort-input');
                input.value = (index + 1) * 10;
            });
        }

        document.querySelectorAll('.move-up').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var row = btn.closest('[data-row]');
                if (row.previousElementSibling) {
                    row.parentNode.insertBefore(row, row.previousElementSibling);
                    refreshSortValues();
                }
            });
        });

        document.querySelectorAll('.move-down').forEach(function (btn) {
            btn.addEventListener('click', function () {
                var row = btn.closest('[data-row]');
                if (row.nextElementSibling) {
                    row.parentNode.insertBefore(row.nextElementSibling, row);
                    refreshSortValues();
                }
            });
        });

        refreshSortValues();
    </script>
</x-app-layout>
