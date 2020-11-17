@extends('layouts.app')

@section('content')
    <div class="container">
        <div class="row mt-4">
            <div class="col-md-8">

                @include('threads._list')

                {{ $threads->render() }}
            </div>

            <div class="col-md-4">
                <div class="card border-0 shadow-sm mb-3">
                    {{-- <div class="card-header bg-white">
                        Search
                    </div> --}}

                    <div class="card-body">
                        <form method="GET" action="/threads/search">
                            <div class="form-group">
                                <input type="text" 
                                    placeholder="Search for something..." 
                                    name="q" 
                                    class="form-control">
                            </div>

                            <div class="form-group">
                                <button class="btn btn-primary btn-block" type="submit">     Search
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

                @if (count($trending))
                    <div class="card border-0 shadow-sm">
                        <div class="card-header bg-white">
                            Trending Threads
                        </div>

                        @foreach ($trending as $thread)
                            
                            <div class="card-body">
                                <div class="card-title">
                                    <a href="{{ url($thread->path) }}" style="text-decoration:none;">
                                        {{ $thread->title }}
                                    </a>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>
        </div>
    </div>
@endsection
