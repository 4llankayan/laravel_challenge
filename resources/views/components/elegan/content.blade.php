<details>
    @if (!empty($tag))
        <summary class="patch-notes-content-title">
            Tag:
            <span class="patch-notes-content-tag">
                {{ $tag }}
            </span>
        </summary>
    @endif

    @if (!empty($description))
        <p class="patch-notes-description">
            {{ $description }}
        </p>
    @endif

    @php($hasRoutes = true)
    @foreach ($routes as $route)
        @if (!empty($route['description']))
            @continue
        @endif

        @if (!empty($route['action']))
            @continue
        @endif

        @if (!empty($route['method']))
            @continue
        @endif

        @if (!empty($route['endpoint']))
            @continue
        @endif

        @php($hasRoutes = false)
    @endforeach

    @if ($hasRoutes)
        <x-elegan.table-routes :routes="$routes" />
    @endif
</details>
