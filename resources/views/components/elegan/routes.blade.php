<tr>
    <td>
        {{ $description }}
    </td>

    <td>
        {{ $action }}
    </td>

    <td>
        @switch(strtoupper($method))
            @case('POST')
                <span class='patch-notes-method patch-notes-method-post'>
                    {{ $method }}
                </span>
            @break

            @case('PUT')
                <span class='patch-notes-method patch-notes-method-put'>
                    {{ $method }}
                </span>
            @break

            @case('GET')
                <span class='patch-notes-method patch-notes-method-get'>
                    {{ $method }}
                </span>
            @break

            @case('DELETE')
                <span class='patch-notes-method patch-notes-method-delete'>
                    {{ $method }}
                </span>
            @break

            @default
                <span class='patch-notes-method patch-notes-method-default'>
                    {{ $method }}
                </span>
        @endswitch
    </td>

    <td>
        @if (str_contains($endpoint, ':'))
            @php
                $explodePath = explode('/', $endpoint);
                $formatedEndpointText = '';
                
                foreach ($explodePath as $currentPath) {
                    if (str_contains($currentPath, ':')) {
                        $formatedEndpointText .= "<span class='patch-note-endpoint-param'>$currentPath</span>/";
                
                        continue;
                    }
                
                    $formatedEndpointText .= $currentPath . '/';
                }
                
                $lastSlashPosition = strripos($formatedEndpointText, '/');
                $endpointTextSplitted = str_split($formatedEndpointText, $lastSlashPosition);
                
                echo $endpointTextSplitted[0];
                
            @endphp
        @else
            {{ $endpoint }}
        @endif
    </td>
</tr>
