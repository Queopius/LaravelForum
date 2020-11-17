@forelse ($threads as $thread)
    <div class="card border-0 shadow-sm mb-3">
        <div class="card-header bg-white">
            <div>
                <h3>
                    <a class="text-dark" href="{{ $thread->path() }}" style="text-decoration:none;">
                        @if (auth()->check() && $thread->hasUpdatesFor(auth()->user()))
                            <strong>
                                {{ $thread->title }}
                            </strong>
                        @else
                            {{ $thread->title }}
                        @endif
                    </a>
                </h3>

                <h6>
                    Posted By: <a href="{{ route('profile', $thread->creator) }}" style="text-decoration:none;">
                        {{ $thread->creator->name }}
                    </a>
                </h6>
                <h6>
                    Created at: {{ $thread->created_at->format('d/m/Y') }}
                </h6>
{{-- 
                <a href="{{ $thread->path() }}" style="text-decoration:none;">
                    {{ $thread->replies_count }} {{ \Illuminate\Support\Str::plural('reply', $thread->replies_count) }}
                </a> --}}
            </div>
        </div>

        <div class="card-body">
            <div class="body">
                {!! $thread->body !!}
            </div>
        </div>

        <div class="card-footer bg-white">
            {{-- {{ $thread->visits }} Visits --}}
            <div class="btn-group btn-group-sm shadow-sm" role="group" aria-label="Basic example" style="text-decoration:none;">
                <button type="button" class="btn btn-light btn-sm">
                    @if(!$thread == 0)
                        {{ $thread->visits }}                    
                    @else
                        0
                    @endif
                </button>
                <button type="button" class="btn btn-light btn-sm" >
                    Visits
                </button>
            </div>

            <a class="ml-2" href="{{ $thread->path() }}" style="text-decoration:none;">
                <div class="btn-group btn-group-sm shadow-sm" role="group" aria-label="Basic example">
                    @if($thread->replies_count == 0)
                        <button type="button" class="btn btn-light btn-sm">
                            0
                        </button>
                    @else 
                        <button type="button" class="btn btn-success btn-sm">
                            {{ $thread->replies_count }} 
                        </button>
                    @endif
                    <button type="button" class="btn btn-dark btn-sm">
                        {{ \Illuminate\Support\Str::plural('reply', $thread->replies_count) }}
                    </button>
                </div>
             </a> 
        </div>
    </div>
@empty
    <p>There are no relevant results at this time.</p>
@endforelse