@extends('layouts.app')

@section('head')
    <link rel="stylesheet" href="{{ asset('/css/vendor/jquery.atwho.css') }}">
@endsection

@section('content')
    <thread-view :thread="{{ $thread }}" inline-template>
        <div class="container">
            <div class="row mt-4">
                <div class="col-md-8" v-cloak>
                    @include ('threads._question')

                    <replies @added="repliesCount++" @removed="repliesCount--"></replies>
                </div>

                <div class="col-md-4">
                    <div class="card border-0 shadow-sm">
                        <div class="card-body">
                            <p>
                                This thread was published {{ $thread->created_at->diffForHumans() }} by
                                <a href="#">{{ $thread->creator->name }}</a>, and currently
                                has 
                                <span v-text="repliesCount"></span> {{ \Illuminate\Support\Str::plural('comment', $thread->replies_count) }}
                                .
                            </p>

                            <p>
                                <subscribe-button :active="@json($thread->isSubscribedTo)" v-if="signedIn"></subscribe-button>
                                
                                {{-- <subscribe-button :active="{{ json_encode($thread->isSubscribedTo) }}" v-if="signedIn"></subscribe-button> --}}

                                <button class="btn btn-primary"
                                        v-if="authorize('isAdmin')"
                                        @click="toggleLock"
                                        v-text="locked ? 'Unlock' : 'Lock'"></button>
                            </p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </thread-view>
@endsection
