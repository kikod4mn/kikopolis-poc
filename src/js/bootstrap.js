window.axios = require('axios');
window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

require('./components/alert.js');
require('./components/console.js');

let token = document.head.querySelector('meta[name="x-csrf-token"]');

if (token) {
    window.axios.defaults.headers.common['X-CSRF-TOKEN'] = token.content;
    console.log('testing token');
    console.log(token.content);
} else {
    console.error('X-CSRF Token not found');
}