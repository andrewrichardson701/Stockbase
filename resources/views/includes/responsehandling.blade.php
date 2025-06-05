@if (isset($response_handling))
    @if (isset($section) && $section !== null)
        @if (isset($response_handling['section'])) 
            @if ($section == $response_handling['section'])
            {!! $response_handling['response'] !!}
            @endif
        @endif
    @else
        {{-- show the data. no section marked --}}
        {!! $response_handling['response'] !!}
    @endif
@endif
