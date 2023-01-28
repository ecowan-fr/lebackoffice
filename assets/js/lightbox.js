export default window.Lightbox = {
    init: function () {
        document.querySelectorAll('*[data-lightbox]').forEach(item => {
            if (item.dataset.lightboxinit == undefined) {
                let data = item.dataset.lightbox.split(':')
                let taille = data[0]
                let page = data[1]
                let bgcolor = data[2] != undefined ? data[2] : '#FFFFFF'
                item.dataset.lightboxinit = true
                item.addEventListener('click', function (e) {
                    return Lightbox[taille](page, bgcolor)
                })
            }
        })
        var li = document.getElementById('lightbox');
        if (li != undefined) {
            document.getElementById('lightbox').style.display = "block"
        }
    },
    S: function (page, bgcolor = '#FFFFFF') {
        return this.box('600', page, bgcolor)
    },
    M: function (page, bgcolor = '#FFFFFF') {
        return this.box('960', page, bgcolor)
    },
    L: function (page, bgcolor = '#FFFFFF') {
        return this.box('1280', page, bgcolor)
    },
    box: function (taille, page, bgcolor) {
        var li = document.getElementById('lightbox');
        if (li != undefined) {
            return li.classList.remove('-z-50'), li.classList.add('z-50'), li.style.width = taille + "px", this.scrollToTop(), this.api(page), li.style.visibility = "visible", li.style.opacity = 1
        }
    },
    scrollToTop: function () {
        window.scrollTo({ top: 0, behavior: 'smooth' });
    },
    close: function (id) {
        return document.getElementById('lightbox').classList.add('-z-50'), document.getElementById('lightbox').classList.remove('z-50'), document.getElementById('lightbox').style.visibility = "hidden", document.getElementById('lightbox').style.opacity = 0
    },
    api: function (page) {
        fetch("/api/lightbox/" + page, {
            method: 'GET',
            headers: new Headers({
                "Content-Type": "application/json"
            })
        }).then((response) => {
            if (response.status != 200) {
                throw new Error("HTTP status " + response.status);
            }
            return response.json();
        }).then((data) => {
            document.getElementById('lightbox').innerHTML = data.html
            var scripts = document.querySelectorAll("#lightbox script");
            scripts.forEach(element => { eval(element.textContent) });
        })
    }
}