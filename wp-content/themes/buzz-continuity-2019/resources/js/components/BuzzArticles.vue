<template>
    <div>
        <buzz-article v-for="post in posts" :key="post.id" :post="post"></buzz-article>
    </div>
</template>

<script>
    import BuzzArticle from './BuzzArticle.vue';
    import {throttle} from 'lodash'

    export default {
        
        components: {BuzzArticle},

        props: {
            site: { 
                type: String,
                required: true 
            },
            newsletter: { 
                type: Number,
                required: true 
            },
        },

        mounted() {
            this.fetch();
            window.addEventListener('scroll', this.beginFetching);
        },

        data() {
            return {
                posts: [],
                page: 1,
                perPage: 1,
                totalPosts: null,
                loading: false,
            }
        },

        computed: {
            hasMore() {
                return this.page * this.perPage <= this.totalPosts;
            },
        },

        methods: {

            beginFetching: throttle(function(e) {

                if(! this.hasMore) {
                    window.removeEventListener('scroll', this.beginFetching);
                    console.log('removing event listener');
                    return;
                }
                
                let distance = this.$el.offsetTop + window.scrollY;
                let threshold = this.$el.clientHeight - 200;
                console.log(this.$el.offsetTop, this.$el.clientHeight, window.innerHeight);
                if(! this.loading && distance >= threshold) {
                    this.fetch();
                }
            }, 300),

            fetch() {
                const endpoint = `${this.site}/wp-json/buzz/v1/newsletter/${this.newsletter}/articles?`;
                
                this.loading = true;
                                
                axios.get(`${endpoint}per_page=${this.perPage}&page=${this.page}`)
                    .then(({data, headers}) => {
                        this.loading = false;
                        this.posts = [...this.posts, ...data.posts];

                        if(! this.totalPosts) {
                            this.totalPosts = data.total_posts;
                        }

                        this.page++;
                    })
                    .catch(error => {
                        console.error(error);
                    })
            },
        }
    }
</script>