{{-- Editing the question. --}}
<div class="card border-0 shadow-sm mb-3" v-if="editing">
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
        <div>
            <button class="btn btn-sm btn-primary level-item" @click="update">
                Update
            </button>

            <button class="btn btn-sm level-item ml-2" @click="resetForm">
                Cancel
            </button>

            @can ('update', $thread)
                <form action="{{ $thread->path() }}" method="POST" class="ml-a">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn">
                        <i class="far fa-trash-alt text-danger"></i>
                    </button>
                </form>
            @endcan

        </div>
    </div>
</div>

{{-- Viewing the question. --}}
<div class="card border-0 shadow-sm mb-3" v-else>
    <div class="card-header bg-white">
        <div class="level">
            <h5>
                <img src="{{ $thread->creator->avatar_path }}"
                     alt="{{ $thread->creator->name }}"
                     width="25"
                     height="25"
                     class="mr-1">

                <span class="flex">
                    <strong>
                        <a class="text-decoration-none text-dark" href="{{ route('profile', $thread->creator) }}">
                            {{ $thread->creator->name }}
                        </a> 
                    </strong>
                </span>                
            </h5>
        </div>

        <div class="level mt-2">
            <h4>
                <strong>
                    <span v-text="title"></span>
                </strong>
            </h4>
        </div>
    </div>

    <div class="card-body" v-html="body"></div>

    <div class="card-footer bg-white" v-if="authorize('owns', thread)">
        <button class="btn" @click="editing = true">
            <i class="far fa-edit fa-lg"></i>
        </button>
    </div>
</div>