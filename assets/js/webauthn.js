import { useLogin, useRegistration } from '@web-auth/webauthn-helper';

const webauthnRegister = useRegistration({
    actionUrl: '/account/security/webauthn/add',
    optionsUrl: '/account/security/webauthn/add/options',
});

const webauthnLogin = useLogin({
    actionUrl: '/login/webauthn',
    optionsUrl: '/login/webauthn/options',
});

window.WebAuthn = {
    register: (element) => {
        let redirectUrl;
        if (element.dataset.redirect == '' || element.dataset.redirect == undefined) {
            redirectUrl = '/'
        } else {
            redirectUrl = element.dataset.redirect
        }

        webauthnRegister({
            attestation: 'direct',
            authenticatorSelection: {
                requireResidentKey: true,
                userVerification: 'required'
            }
        }).then(response => {
            Turbo.visit(redirectUrl)
        }).catch(error => {
            alert(error)
            console.log(error)
            Turbo.visit(redirectUrl)
        })
    },

    login: (element) => {
        let redirectUrl;
        if (element.dataset.redirect == '' || element.dataset.redirect == undefined) {
            redirectUrl = '/'
        } else {
            redirectUrl = element.dataset.redirect
        }

        webauthnLogin({
            attestation: 'direct',
            authenticatorSelection: {
                requireResidentKey: true,
                userVerification: 'required'
            }
        }).then(response => {
            Turbo.visit(redirectUrl)
        }).catch(error => {
            alert(error)
            console.log(error)
            Turbo.visit('/login')
        })
    }
}