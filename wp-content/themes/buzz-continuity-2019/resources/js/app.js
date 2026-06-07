window.axios = require('axios');
window.Vue = require('vue');

Vue.component('buzz-articles', require('./components/BuzzArticles.vue'));

const app = new Vue({
    el: '#app'
});