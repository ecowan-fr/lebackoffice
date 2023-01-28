const lightboxWelcome = () => {
    document.createCookieWelcome = () => {
        localStorage.setItem('lightboxWelcome', "yes");
    }

    document.addEventListener("turbo:load", function () {
        //Affichage du message de bienvenue
        if (!localStorage.lightboxWelcome) {
            Lightbox.S('welcome')
        }
    })
}

export default lightboxWelcome