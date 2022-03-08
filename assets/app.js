/*
 * Welcome to your app's main JavaScript file!
 *
 * We recommend including the built version of this JavaScript file
 * (and its CSS file) in your base layout (base.html.twig).
 */

// any CSS you import will output into a single css file (app.css in this case)
import './app.scss';

// start the Stimulus application
// import './bootstrap';

import * as Turbo from "@hotwired/turbo"
import Lightbox from "./js/lightbox.js";
import App from "./js/app.js";
import submitButtonLoader from "./js/submitButtonLoader.js";
// import lightboxWelcome from "./js/lightboxWelcome.js"
import Waves from "node-waves"
import Alpine from 'alpinejs'
window.Alpine = Alpine
Alpine.start()