global.Lightbox = {
    init: function () {
        document.querySelectorAll('*[data-lightbox]').forEach(item => {
            if (item.dataset.lightboxinit == undefined) {
                let data = item.dataset.lightbox.split(':')
                let taille = data[0]
                let module = data[1]
                let page = data[2]
                let bgcolor = data[3] != undefined ? data[2] : '#FFFFFF'
                item.dataset.lightboxinit = true
                item.addEventListener('click', function (e) {
                    return Lightbox[taille](module, page, bgcolor)
                })
            }
        })
        var li = document.getElementById('lightbox');
        if (li != undefined) {
            document.getElementById('lightbox').style.display = "block"
        }
    },
    S: function (module, page, bgcolor = '#FFFFFF') {
        return this.box('600', module, page, bgcolor)
    },
    M: function (module, page, bgcolor = '#FFFFFF') {
        return this.box('768', module, page, bgcolor)
    },
    L: function (module, page, bgcolor = '#FFFFFF') {
        return this.box('992', module, page, bgcolor)
    },
    box: function (taille, module, page, bgcolor) {
        return document.getElementById('lightbox').classList.remove('-z-50'), document.getElementById('lightbox').classList.add('z-50'), document.getElementById('lightbox').style.width = taille + "px", this.center(taille), this.api(module, page), document.getElementById('lightbox').style.visibility = "visible", document.getElementById('lightbox').style.opacity = 1
    },
    center: function (taille) {
        var elem = document.getElementById('lightbox')
        var e = (this.getPageDimensions()[0] - taille) / 2;
        if ("Microsoft Internet Explorer" == navigator.appName) o = 1;
        else var o = 0;
        o || (e -= 9), e = e < 0 ? 0 : e, elem.style.left = e + 'px', elem
    },
    getPageDimensions: function () {
        var i, t, e, o;
        return window.innerHeight && window.scrollMaxY ? (i = document.body.scrollWidth, t = window.innerHeight + window.scrollMaxY) : document.body.scrollHeight > document.body.offsetHeight ? (i = document.body.scrollWidth, t = document.body.scrollHeight) : (i = document.body.offsetWidth, t = document.body.offsetHeight), self.innerHeight ? (e = self.innerWidth, o = self.innerHeight) : document.documentElement && document.documentElement.clientHeight ? (e = document.documentElement.clientWidth, o = document.documentElement.clientHeight) : document.body && (e = document.body.clientWidth, o = document.body.clientHeight), pageHeight = t < o ? o : t, pageWidth = i < e ? e : i, arrayPageSize = new Array(e, o, pageWidth, pageHeight), arrayPageSize
    },
    close: function (id) {
        return document.getElementById('lightbox').classList.add('-z-50'), document.getElementById('lightbox').classList.remove('z-50'), document.getElementById('lightbox').style.visibility = "hidden", document.getElementById('lightbox').style.opacity = 0
    },
    api: function (module, page) {
        fetch("/api/lightbox/" + module + "/" + page, {
            method: 'GET',
            headers: new Headers({
                "Content-Type": "application/json",
                "Authorization": "Bearer " + document.env.API_KEY
            })
        }).then((response) => {
            if (response.status != 200) {
                throw new Error("HTTP status " + response.status);
            }
            return response.json();
        }).then((data) => {
            document.getElementById('lightbox').innerHTML = data
            var scripts = document.querySelectorAll("#lightbox script");
            scripts.forEach(element => { eval(element.textContent) });
        })
    }
}

module.exports = Lightbox