export default (function () {

    global.createCookieWelcome = () => {
        fetch("/api/cookies/welcome", {
            method: 'POST',
            headers: new Headers({
                "Content-Type": "application/json",
                "Authorization": "Bearer " + document.env.API_KEY
            })
        }).then((response) => {
            if (response.status != 201) {
                throw new Error("HTTP status " + response.status);
            }
            return response.json();
        })
    }

    document.addEventListener("turbo:load", function () {
        //Affichage du message de classification
        fetch("/api/cookies/welcome", {
            method: 'GET',
            headers: new Headers({
            })
        }).then((response) => {
            if (response.status == 404) {
                Lightbox.S('framework', 'welcome')
            }
            return response.json();
        })
    })
})()