<details class="patch-notes-container">
    <x-elegan.header-container :version="$info['version']" :title="$info['title']" :date="$info['date']" />

    @if (isset($info['content']))
        @foreach ($info['content'] as $content)
            @php
                if (empty($content['tag'])) {
                    $content['tag'] = '';
                }
                
                if (empty($content['description'])) {
                    $content['description'] = '';
                }
                
                if (empty($content['routes'])) {
                    $content['routes'] = [];
                }
            @endphp
            <x-elegan.content :tag="$content['tag']" :description="$content['description']" :routes="$content['routes']" />
        @endforeach
    @else
        <span>No informations...</span>
    @endif

</details>
