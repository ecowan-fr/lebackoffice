export default (function () {
    document.addEventListener("turbo:load", function () {
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', (event) => {
                var btn = form.querySelector('button[type="submit"]')
                btn.innerHTML = '<i class=\'fad animate-spin fa-spinner-third\'></i>'
                btn.disabled = true
            })
        })
    })
})();