import { browserSupportsWebAuthn, startRegistration, startAuthentication } from '@simplewebauthn/browser';

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

const redirectUrl = (element) => {
    return (element.dataset.redirect == '' || element.dataset.redirect == undefined) ? '/' : element.dataset.redirect
}

const runWebAuthnCeremoni = async (element, optionUrl, verificationUrl, type) => {
    if (!browserSupportsWebAuthn()) {
        sendFlashMessage('error', 'It seems this browser does not support WebAuthn...', redirectUrl(element))
        return;
    }

    const option = await fetch(optionUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            attestation: 'direct',
            authenticatorSelection: {
                requireResidentKey: true,
                userVerification: 'required'
            }
        }),
    })

    let dataFromToken
    try {
        if (type == 'register') {
            dataFromToken = await startRegistration(await option.json());
        } else {
            dataFromToken = await startAuthentication(await option.json());
        }
    } catch (error) {
        if (error.name === 'InvalidStateError' && type == 'register') {
            sendFlashMessage('error', 'Error: Authenticator was probably already registered by user', redirectUrl(element))
        } else {
            sendFlashMessage('error', error, redirectUrl(element))
        }
    }

    const verification = await fetch(verificationUrl, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(dataFromToken),
    }).then(data => {
        if (data.ok && type == 'register') {
            sendFlashMessage('success', "Added your security token successfully", redirectUrl(element), "account")
        } else if (data.ok) {
            Turbo.visit(redirectUrl(element))
        } else {
            return data.json()
        }
    }).then(error => {
        error && sendFlashMessage('error', error.errorMessage, redirectUrl(element))
    })
}

export default window.WebAuthn = {
    register: async (element) => {
        return await runWebAuthnCeremoni(element, '/account/security/webauthn/add/options', '/account/security/webauthn/add', 'register')
    },
    login: async (element) => {
        return await runWebAuthnCeremoni(element, '/login/webauthn/options', '/login/webauthn', 'login')
    }
}