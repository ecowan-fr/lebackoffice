const reLocPosMenuMain = (activeMenu) => {
    document.querySelector('#posMenuMain').style.top = (activeMenu.getBoundingClientRect().top + 5) + 'px'
}

const posMenuMain = () => {
    const activeRoute = document.querySelector('#activeroute').dataset.route

    let activeMenu = null

    document.querySelectorAll('div.mainMenuItems a').forEach(a => {
        a.children[0].classList.replace('fa-solid', 'fa-light')
        a.classList.remove('active')

        const route = a.dataset.route

        if (activeRoute.startsWith(route)) {
            activeMenu = a
        }
    })

    activeMenu.children[0].classList.replace('fa-light', 'fa-solid')
    activeMenu.classList.add('active')


    reLocPosMenuMain(activeMenu)

    window.addEventListener('resize', (e) => {
        reLocPosMenuMain(activeMenu)
    })
}

export default posMenuMain;