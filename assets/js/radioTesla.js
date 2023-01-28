const radioTesla = (labelSelected = null) => {
    document.querySelectorAll('.radio-group').forEach(radioGroup => {
        radioGroup.querySelectorAll('label').forEach(label => {
            label.classList.remove('colorSelected')
            if (labelSelected === null) {
                label.addEventListener('click', () => {
                    radioTesla(label)
                })
            }
        })

        let labelActif = radioGroup.parentNode.querySelector('input[type="radio"].radio-tesla:checked').labels[0]
        if (labelSelected) { labelActif = labelSelected }

        labelActif.parentNode.querySelector('.selected').style.left = labelActif.offsetLeft + 'px'
        labelActif.classList.add('colorSelected')
    })
}

export default radioTesla