{{-- Editing the question. --}}
<div class="card border-0 shadow-sm" v-if="editing">
    <div class="card-header bg-white">
        <div class="level">
            <input type="text" class="form-control" v-model="form.title">
        </div>
    </div>

    <div class="card-body">
        <div class="form-group">
            <wysiwyg v-model="form.body"></wysiwyg>
        </div>
    </div>

    <div class="card-footer bg-white">
        <div class="level">
            <button class="btn btn-sm level-item" @click="editing = true" v-show="! editing">Edit</button>
            <button class="btn btn-primary btn-sm level-item" @click="update">Update</button>
            <button class="btn btn-sm level-item" @click="resetForm">Cancel</button>

            @can ('update', $thread)
                <form action="{{ $thread->path() }}" method="POST" class="ml-a">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-link">Delete Thread</button>
                </form>
            @endcan

        </div>
    </div>
</div>


{{-- Viewing the question. --}}
<div class="card border-0 shadow-sm mt-3 mb-3" v-else>
    <div class="card-header bg-white">
        <div class="level">
            <img src="{{ $thread->creator->avatar_path }}"
                 alt="{{ $thread->creator->name }}"
                 width="25"
                 height="25"
                 class="mr-1">

            <span class="flex">
                <a href="{{ route('profile', $thread->creator) }}">{{ $thread->creator->name }}</a> posted: <span v-text="title"></span>
            </span>
        </div>
    </div>

    <div class="card-body" v-html="body"></div>

    <div class="card-footer bg-white" v-if="authorize('owns', thread)">
        <button class="btn btn-sm" @click="editing = true">Edit</button>
    </div>
</div>