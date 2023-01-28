const addSpinToBtn = (btn) => {
    btn.innerHTML = '<i class=\'fad animate-spin fa-spinner-third\'></i>'
    btn.disabled = true
}

const buttonsLoader = () => {
    document.querySelectorAll('.btn-load').forEach(btn => {
        btn.addEventListener('click', (event) => {
            addSpinToBtn(btn)
        })
    })

    document.querySelectorAll('form').forEach(form => {
        form.addEventListener('submit', (event) => {
            var btn = form.querySelector('button[type="submit"]')
            addSpinToBtn(btn)
        })
    })
}

export default buttonsLoader