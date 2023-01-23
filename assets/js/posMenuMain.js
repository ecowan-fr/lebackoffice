const posMenuMain = () => {
    document.querySelectorAll('div.mainMenuItems a').forEach(a => {
        if (!a.classList.contains('active')) {
            a.children[0].classList.replace('fa-solid', 'fa-light')
        }
    })

    let activeMenu = document.querySelector('div.mainMenuItems a.active')
    activeMenu.children[0].classList.replace('fa-light', 'fa-solid')
    document.querySelector('#posMenuMain').style.top = (activeMenu.getBoundingClientRect().top + 5) + 'px'

}

export default posMenuMain;