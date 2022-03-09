export default (function () {

    global.createCookieWelcome = () => {
        fetch("/api/cookies/welcome", {
            method: 'POST',
            headers: new Headers({
                "Content-Type": "application/json"
            })
        }).then((response) => {
            if (response.status != 201) {
                throw new Error("HTTP status " + response.status);
            }
            return response.json();
        })
    }

    document.addEventListener("turbo:load", function () {
        //Affichage du message de bienvenue
        fetch("/api/cookies/welcome", {
            method: 'GET',
            headers: new Headers({
                "Content-Type": "application/json"
            })
        }).then((response) => {
            if (response.status == 404) {
                Lightbox.S('welcome')
            }
            return response.json();
        })
    })
})()