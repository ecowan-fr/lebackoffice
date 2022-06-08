import './app.scss';

import * as Turbo from "@hotwired/turbo"
import Lightbox from "./js/lightbox.js";
import App from "./js/app.js";
import submitButtonLoader from "./js/submitButtonLoader.js";
import lightboxWelcome from "./js/lightboxWelcome.js"
import Waves from "node-waves"
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()
import WebAuthn from "./js/webauthn";