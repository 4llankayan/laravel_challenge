<summary class="patch-notes-header" id="{{ $version }}-{{ $title }}">

    @if (!empty($version))
        <div class="patch-notes-header-version">
            Version: {{ $version }}
        </div>
    @endif

    @if (!empty($title))
        <div class="patch-notes-header-title">
            {{ $title }}
        </div>
    @endif

    @if (!empty($date))
        @php
            \Carbon\Carbon::setlocale(config('app.locale'));
        @endphp

        <div>
            <span class="patch-note-date">
                {{ \Carbon\Carbon::parse($date)->translatedFormat('Y-m-d') }}
            </span>
        </div>
    @endif
</summary>
