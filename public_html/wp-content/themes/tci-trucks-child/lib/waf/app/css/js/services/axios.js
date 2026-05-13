const axios = require('axios');
const { parseJSON } = require('../utils/parseJSON');
const $ = jQuery.noConflict();

axios.interceptors.response.use((response) => {
    const r = { messages: [], ...response, ...parseJSON(response.data) }
    console.log('[RESPONSE DEBUG]', "\n=====================\n", r.debug, "\n=====================\n");
    $('#debug').html(r.debug);
    return r;
}, function (error) {
    // Do something with response error
    const response = { messages: [], ...error.response, ...parseJSON(error.response.data) }
    response.messages.push({
        status: 'danger',
        message: error.message
    });
    console.log('[ERROR RESPONSE DEBUG]', response.debug);
    $('#debug').html(response.debug);
    return Promise.reject(response);
});
module.exports = axios;