<template>
    <div class="card card-default">
        <div class="card-header" :class="getCategorySlugs()">
            <h1 v-html="post.title"></h1>
            <p>Categories: {{ getCategories() }}</p>
        </div>
        <div class="card-body">
            <span v-html="post.content"></span>
        </div>
    </div>
</template>

<script>
    export default {
        props: ['post'],

        methods: {
            getCategories() {
                return this.post.categories.map(category => category.name).join(', ');
            },

            getCategorySlugs() {
                return this.post.categories.map(category => category.slug).join(' ');
            }
        }
    }
</script>