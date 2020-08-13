/**
 * We'll load the axios HTTP library which allows us to easily issue requests
 * to our Laravel back-end. This library automatically handles sending the
 * CSRF token as a header based on the value of the "XSRF" token cookie.
 */
window._ = require('lodash');

window.axios = require('axios');

window.axios.defaults.headers.common['X-Requested-With'] = 'XMLHttpRequest';

function sendForm() {
    let errors = document.querySelector('.errors');
    errors.innerHTML = '';
    let form = document.getElementById('login');
    let username = form.querySelector('input[name="username"]').value;
    let password = form.querySelector('input[name="password"]').value;
    let action = form.getAttribute('action');
    axios.post(action, {
        username,
        password
    }).then(response => {
        if(response.data.data) {
            window.location.href = response.data.data.redirect;
        }
    }).catch(err => {
        let p = document.createElement('p');
        p.className = 'radius'
        p.textContent = err.response.data.message;
        errors.append(p);
        console.log(`Err: ${err.response}`);
    })
}
let btnSubmit = document.getElementById('submit');
btnSubmit.onclick = sendForm;
