import './app.scss';

//Librairies externes
import * as Turbo from "@hotwired/turbo"
import Waves from "node-waves"
import Alpine from 'alpinejs'

//Scripts Lebackoffice
import posMenuMain from './js/posMenuMain';
import WebAuthn from "./js/webauthn";
import Lightbox from "./js/lightbox";
import lightboxWelcome from "./js/lightboxWelcome"
import buttonsLoader from "./js/buttonsLoader";
import radioTesla from './js/radioTesla';
import setTheme from './js/setTheme';
import checkAllCheckboxOfTable from './js/checkAllCheckboxOfTable';
import customSelects from './js/customSelects';

Alpine.start()
lightboxWelcome()

document.addEventListener("turbo:load", function () {
    Waves.init()
    Waves.attach('button.transparent')
    Waves.attach('a.button.transparent')
    Waves.attach('button', ['waves-light'])
    Waves.attach('a.button', ['waves-light'])

    posMenuMain()
    Lightbox.init()
    buttonsLoader()
    radioTesla()
    setTheme()
    customSelects()
})

document.addEventListener("turbo:before-render", function () {
    clearTimeout(document.timeoutFlash)
})