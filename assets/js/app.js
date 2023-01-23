export default (function () {
    let gestionSelect = () => {
        let selects = document.querySelectorAll('div.select');
        selects.forEach(element => {
            let affichage = element.children[0];
            let input = element.children[1];
            let btn = element.children[2];
            let dataSelect = element.children[3];
            if (input.value !== '') {
                let liEncours = document.querySelectorAll('div.select li[value=' + input.value + ']')[0]
                if (liEncours) {
                    affichage.innerHTML = liEncours.children[0].children[0].children[0].innerHTML
                    liEncours.children[0].children[0].children[1].innerHTML = '<i class="fa-solid fa-check"></i>'
                }
            }
            element.addEventListener('click', event => {
                event.stopPropagation()
                dataSelect.classList.toggle('hidden')
            })
            let lis = dataSelect.children
            for (let index = 0; index < lis.length; index++) {
                const li = lis[index];
                li.addEventListener('click', event2 => {
                    event2.stopPropagation()
                    for (let index2 = 0; index2 < lis.length; index2++) {
                        const li2 = lis[index2];
                        li2.children[0].children[0].children[1].innerHTML = '';
                    }
                    li.children[0].children[0].children[1].innerHTML = '<i class="fa-solid fa-check"></i>'
                    input.attributes.value.value = li.attributes.value.value
                    affichage.innerHTML = li.children[0].children[0].children[0].innerHTML
                    dataSelect.classList.add('hidden')
                })
            }
        });
    }

    document.addEventListener('click', event => {
        let uls = document.querySelectorAll('div.select ul')
        uls.forEach(element => {
            element.classList.add('hidden')
        });
    })

    //Gestion du mod nuit
    global.setTheme = (theme = undefined) => {
        let logo = document.querySelector('img.logoprincipal');
        if (logo === null) { return false }
        let logo_light = logo.dataset.logolight != undefined ? logo.dataset.logolight : null
        let logo_dark = logo.dataset.logodark
        if (!logo_light || !logo_dark) {
            return false;
        }
        let themeSelected = 'light';
        let radioLight = document.querySelector('input#theme-light')
        let radioDark = document.querySelector('input#theme-dark')
        let circle = document.querySelector('#circle')
        let crescent = document.querySelector('#crescent')
        if (theme === 'dark' || theme == undefined && localStorage.theme == 'dark') {
            themeSelected = 'dark';
        }

        if (themeSelected === 'dark') {
            document.documentElement.classList.add('dark')
            if (radioDark) {
                radioDark.checked = true;
                autoSelectRadio()
            }
            if (circle) {
                circle.style.background = "linear-gradient(40deg, #8983F7, #A3DAFB 70%)";
            }
            if (crescent) {
                crescent.style.transform = "scale(1)";
            }
            if (logo) {
                logo.src = logo_dark;
            }
        } else {
            document.documentElement.classList.remove('dark')
            if (radioLight) {
                radioLight.checked = true;
                autoSelectRadio()
            }
            if (circle) {
                circle.style.background = "linear-gradient(40deg, #FF0080,#FF8C00 70%)";
            }
            if (crescent) {
                crescent.style.transform = "scale(0)";
            }
            if (logo) {
                logo.src = logo_light;
            }
        }
        localStorage.setItem('theme', themeSelected)
    }

    //Checkbox select all checkbox of table
    global.checkAllCheckboxOfTable = (event) => {
        var input = event.path[0];
        var tbody = event.path[4].children[1];
        tbody.querySelectorAll("input[type='checkbox']").forEach(checkbox => {
            checkbox.checked = input.checked
        })
    }

    // Gestion des radios tesla
    let autoSelectRadio = (labelSelected = null) => {
        document.querySelectorAll('.radio-group').forEach(radioGroup => {
            radioGroup.querySelectorAll('label').forEach(label => {
                label.classList.remove('colorSelected')
                if (labelSelected === null) {
                    label.addEventListener('click', () => {
                        autoSelectRadio(label)
                    })
                }
            })

            let labelActif = radioGroup.parentNode.querySelector('input[type="radio"].radio-tesla:checked').labels[0]
            if (labelSelected) { labelActif = labelSelected }

            labelActif.parentNode.querySelector('.selected').style.left = labelActif.offsetLeft + 'px'
            labelActif.classList.add('colorSelected')
        })
    }



    let btnLoad = () => {
        document.querySelectorAll('.btn-load').forEach(a => {
            a.addEventListener('click', () => {
                a.innerHTML = '<i class=\'fad animate-spin fa-spinner-third\'></i>'
                a.disabled = true
            })
        })
    }

    document.addEventListener("turbo:load", function () {
        Lightbox.init()
        Waves.init()

        Waves.attach('button.transparent')
        Waves.attach('a.button.transparent')

        Waves.attach('button', ['waves-light'])
        Waves.attach('a.button', ['waves-light'])

        gestionSelect()

        autoSelectRadio()

        setTheme()

        btnLoad()
    })

    document.addEventListener("turbo:before-render", function () {
        clearTimeout(document.timeoutFlash)
    })
})()