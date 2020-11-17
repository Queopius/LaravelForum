@foreach ($threads as $thread)
    <div class="btn-group" role="group" aria-label="Basic example">
        <button type="button" class="btn btn-light btn-sm">
            @if(!$thread == 0)
                {{ $thread->visits }}
            @else 
                0
            @endif
        </button>
        <button type="button" class="btn btn-secondary btn-sm" style="text-decoration:none">
            Visits
        </button>
    </div>
@endforeach

{{-- <div class="btn-group" role="group" aria-label="Basic example">
    <button type="button" class="btn btn-light btn-sm">
        
            {{ $thread->visits }}
       
    </button>
    <button type="button" class="btn btn-secondary btn-sm" style="text-decoration:none">
        Visits
    </button>
</div>

<div class="btn-group" role="group" aria-label="Basic example">
    <button type="button" class="btn btn-light btn-sm">
        1k
    </button>
    <button type="button" class="btn btn-secondary btn-sm" style="text-decoration:none">
        Replies
    </button>
</div>


<a href="{{ $thread->path() }}" style="text-decoration:none;">
                    {{ $thread->replies_count }} {{ \Illuminate\Support\Str::plural('reply', $thread->replies_count) }}
                </a> --}}