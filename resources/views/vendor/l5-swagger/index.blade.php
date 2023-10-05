<html>

<head>
    <link rel="shortcut icon" href="{{ asset('elegan/favicon.ico') }}" type="image/x-icon">
    <title>{{ config('app.name') }} | Api Docs</title>
    <link href="{{ asset('elegan/assets/style.css') }}" rel="stylesheet">
    <link href="{{ asset('elegan/assets/elegan.css') }}" rel="stylesheet">
</head>

<body>
    <header>
        <nav>
            <ul>
                <li><a href="#swagger-ui">🧭 Routes</a></li>
                <li><a href="#patch-notes">🆕 Patch notes</a></li>
            </ul>
        </nav>
    </header>
    <div id="swagger-ui"></div>

    <script src="{{ asset('elegan/utils/jquery-2.1.4.min.js') }}"></script>
    <script src="{{ asset('elegan/utils/swagger-bundle.js') }}"></script>
    <script type="application/javascript">
        const ui = SwaggerUIBundle({
            url: <?php echo json_encode(asset('elegan/index.yaml')); ?>,
            dom_id: '#swagger-ui',
            deepLinking: true,
        });
    </script>

    <div>
        @inject('eleganHelper', 'Labi9\Elegan\EleganHelper')

        @php($notes = $eleganHelper->patchNoteFiles())

        @if ($notes)
            <h2 id="patch-notes" class="patch-notes-title">Patch notes</h2>

            <div class="patch-notes-wrapper">
                @foreach ($notes as $note)
                    <x-elegan.container :info="$note['info']" />
                @endforeach
            </div>
        @else
            <h2 class="patch-notes-title">No patch notes</h2>
            <h3 class="patch-notes-subtitle">
                Click
                <a href="https://gitlab.com/dev-in-commerce/elegan/-/tree/develop?ref_type=heads#comandos"
                    target="_blank">here</a>
                to see the documentation
            </h3>
        @endif
    </div>
    <footer>
        <div>
            <p>
                Developed by <a href="https://labi9.com/" target="_blank"><span>LABI9</span> - Information
                    Technology
            </p>
        </div>
    </footer>

</body>

</html>
