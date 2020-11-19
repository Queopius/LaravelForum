@extends('layouts.app')

@section ('head')
    
@endsection

@section('content')
<div class="container">
    <div class="row justify-content-center mt-4">
        <div class="col-md-9">
            <div class="card border-0 shadow-sm">
                <div class="row">
                    <div class="col-md-12">

                        <div class="card-header bg-white">
                            <h3>
                                Create a New Thread
                            </h3>
                        </div>

                        <div class="card-body">
                            <form method="POST" action="/threads">
                                @csrf

                                <div class="form-group">
                                    <label for="channel_id">
                                        Choose a Channel:
                                    </label>
                                    <select name="channel_id" 
                                        id="channel_id" 
                                        class="form-control" 
                                        required>
                                        <option value="">Choose One...</option>

                                        @foreach ($channels as $channel)
                                            <option value="{{ $channel->id }}" {{ old('channel_id') == $channel->id ? 'selected' : '' }}>
                                                {{ $channel->name }}
                                            </option>
                                        @endforeach
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="title">Title:</label>
                                    <input type="text"
                                        class="form-control @error('title') is-invalid @enderror" 
                                        id="title" 
                                        name="title"
                                        value="{{ old('title') }}" 
                                        required>
                                </div>

                                <div class="form-group">
                                    <label for="body">
                                        Body:
                                    </label>
                                    <wysiwyg name="body"></wysiwyg>
                                </div>

                                <div class="form-group">
                                    <div class="g-recaptcha" 
                                        data-callback='onSubmit' 
                                        data-action='submit'
                                        data-sitekey="6LerLeUZAAAAADhrG5ekGxP6XPUHZAUc4Bqcwicj"></div>
                                </div>

                                <div class="form-group">
                                    <button type="submit" class="btn btn-primary">
                                        Publish
                                    </button>
                                </div>

                                @if (count($errors))
                                    <ul class="alert alert-danger">
                                        @foreach ($errors->all() as $error)
                                            <li>{{ $error }}</li>
                                        @endforeach
                                    </ul>
                                @endif
                            </form>

                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('styles')

@endpush

@push('scripts')
<script src='https://www.google.com/recaptcha/api.js'></script>
<script type="text/javascript">
  var onloadCallback = function() {
    alert("grecaptcha is ready!");
  };
</script>
@endpush