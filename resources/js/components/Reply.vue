<template>
    <div :id="'reply-'+id" 
        class="card border-0 shadow-sm mt-3" 
        :class="isBest ? 'border-success': 'card-default'"
        >
        <div class="card-header bg-white d-flex align-items-center">

            <div>
                <a :href="'/profiles/' + reply.owner.name"
                    v-text="reply.owner.name">
                </a> said <span v-text="ago"></span>

                <div v-if="signedIn">
                    <favorite :reply="reply"></favorite>
                </div>
            </div>

        </div>

        <div class="card-body">
            <div v-if="editing">
                <div class="form-group">
                    <form @submit.prevent="update">
                        <div class="form-group">
                            <wysiwyg v-model="body"></wysiwyg>
                        </div>

                        <button class="btn btn-sm btn-primary">
                            Update
                        </button>
                        <button class="btn btn-sm btn-link" 
                            @click="editing = false" 
                            type="button"
                            >
                            Cancel
                        </button>
                    </form>
                </div>
            </div>

            <article v-html="body" v-else></article>
        </div>

        <div class="card-footer bg-white d-flex justify-content-between" v-if="authorize('owns', reply) || authorize('owns', reply.thread)">
            <div v-if="authorize('owns', reply)">
                <button class="btn mr-1" 
                    @click="editing = true" 
                    v-if="! editing"
                    >
                    <i class="far fa-edit fa-lg"></i>
                </button>
                <button class="btn mr-1" 
                    @click="destroy"
                    >    
                    <i class="far fa-trash-alt fa-lg text-danger"></i>
                </button>
            </div>

            <button class="btn btn-sm btn-primary ml-a" 
                @click="markBestReply" 
                v-if="authorize('owns', reply.thread)">
                Best Reply?
            </button>
        </div>
    </div>
</template>

<script>
    import Favorite from './Favorite';
    import moment from 'moment';
    //import VTooltip from 'v-tooltip'

    export default {
        props: ['reply'],

        components: {Favorite},

        data() {
            return {
                editing: false,
                id: this.reply.id,
                body: this.reply.body,
                isBest: this.reply.isBest,
            };
        },

        computed: {
            ago() {
                return moment(moment.utc(this.reply.created_at)).fromNow();
            },
            signedIn() {
                return window.app.signedIn;
            },

            canUpdate() {
                return this.authorize(user => this.data.user_id == user.id)
            }
            // ago() {
            //     return moment(this.reply.created_at).fromNow() + '...';
            // }
        },

        created () {
            window.events.$on('best-reply-selected', id => {
                this.isBest = (id === this.id);
            });
        },

        methods: {
            update() {
                axios.patch(
                    '/replies/' + this.id, {
                        body: this.body
                    })
                    .catch(error => {
                        flash(error.response.data, 'danger');
                    });

                this.editing = false;
                flash('Updated!');
            },
            destroy() {
                axios.delete('/replies/' + this.id);

                this.$emit('deleted', this.id);
            },
            markBestReply() {
                axios.post('/replies/' + this.id + '/best');
                window.events.$emit('best-reply-selected', this.id);
            }
        }
    };
</script>
