window.Lightbox = {
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
            return li.classList.remove('-z-50'), li.classList.add('z-50'), li.style.width = taille + "px", this.center(taille), this.api(page), li.style.visibility = "visible", li.style.opacity = 1
        }
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