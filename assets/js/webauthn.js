import { useLogin, useRegistration } from '@web-auth/webauthn-helper';

const webauthnRegister = useRegistration({
    actionUrl: '/account/security/webauthn/add',
    optionsUrl: '/account/security/webauthn/add/options',
});

const webauthnLogin = useLogin({
    actionUrl: '/login/webauthn',
    optionsUrl: '/login/webauthn/options',
});

const sendFlashMessage = async function (type, message, redirectUrl, domain) {
    let body;
    if (domain !== null) {
        body = '{ "type": "' + type + '", "message": "' + message + '", "domainTranslation": "' + domain + '"}'
    } else {
        body = '{ "type": "' + type + '", "message": "' + message + '"}'
    }
    await fetch("/api/flash", {
        method: 'POST',
        body: body,
        headers: new Headers({
            "Content-Type": "application/json"
        })
    }).then(response => {
        Turbo.visit(redirectUrl)
    })
}

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
            if (response.status == 'ok') {
                sendFlashMessage('success', "Added your security token successfully", redirectUrl, "account")
            } else {
                sendFlashMessage('error', response.errorMessage, redirectUrl)
            }
        }).catch(error => {
            if (error.toString() === '[object Response]') {
                return error.json();
            } else {
                sendFlashMessage('error', error.toString(), redirectUrl)
            }
        }).then(json => {
            sendFlashMessage('error', json.errorMessage, redirectUrl)
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
            if (response.status == 'ok') {
                Turbo.visit(redirectUrl)
            } else {
                sendFlashMessage('error', response.errorMessage, redirectUrl)
            }
        }).catch(error => {
            if (error.toString() === '[object Response]') {
                return error.json();
            } else {
                sendFlashMessage('error', error.toString(), redirectUrl)
            }
        }).then(json => {
            sendFlashMessage('error', json.errorMessage, redirectUrl)
        })
    }
}