import { useRegistration, useLogin } from '@web-auth/webauthn-helper';

export default (function () {

    document.addEventListener("turbo:load", function () {
        let btn = document.getElementById('registerWebAuthn')
        if (btn != undefined) {
            btn.addEventListener('click', () => {
                WebAuthn.register()
            })
        }
    })

    window.WebAuthn = {
        register() {
            const registerFunction = useRegistration({
                actionUrl: '/register',
                optionsUrl: '/register/options'
            });
            registerFunction({
                username: 'thomas@tydoo.fr',
                displayName: 'JD'
            }).then((response) => {
                console.log('resgistration ok');
                console.log(response);
            }).catch((error) => {
                console.log('resgistration nok');
                console.log(error)
            });
        }
    }
})();