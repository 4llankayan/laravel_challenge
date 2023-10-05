<table class="patch-notes-content-routes">
    <tr>
        <th>
            Decription
        </th>
        <th>
            Action
        </th>
        <th>
            Method
        </th>
        <th>
            Endpoint
        </th>
    </tr>
    @foreach ($routes as $route)
        @if (!empty($route['description']))
            @php
                $description = $route['description'];
            @endphp
        @else
            @php
                $description = '---';
            @endphp
        @endif

        @if (!empty($route['action']))
            @php
                $action = $route['action'];
            @endphp
        @else
            @php
                $action = '---';
            @endphp
        @endif

        @if (!empty($route['method']))
            @php
                $method = $route['method'];
            @endphp
        @else
            @php
                $method = '---';
            @endphp
        @endif

        @if (!empty($route['endpoint']))
            @php
                $endpoint = $route['endpoint'];
            @endphp
        @else
            @php
                $endpoint = '---';
            @endphp
        @endif

        <x-elegan.routes :description="$description" :action="$action" :method="$method" :endpoint="$endpoint" />
    @endforeach
</table>
