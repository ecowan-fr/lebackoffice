export default (function () {
    document.createCookieWelcome = () => {
        localStorage.setItem('lightboxWelcome', "yes");
    }

    document.addEventListener("turbo:load", function () {
        //Affichage du message de bienvenue
        if (!localStorage.lightboxWelcome) {
            Lightbox.S('welcome')
        }
    })
})()