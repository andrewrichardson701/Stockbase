@if (isset($response_handling))
    @if (isset($section) && $section !== null)
        @if (isset($response_handling['section'])) 
            @if ($section == $response_handling['section'])
            {!! $response_handling['response'] !!}
            @endif
        @endif
    @else
        {{-- show the data. no section marked --}}
        @if ($response_handling['section'] == null)
            {!! $response_handling['response'] !!}
        @endif
    @endif
@endif
