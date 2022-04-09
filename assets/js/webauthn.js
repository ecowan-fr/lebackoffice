import { useLogin, useRegistration } from '@web-auth/webauthn-helper';

const webauthnRegister = useRegistration({
    actionUrl: '/account/security/webauthn/add',
    optionsUrl: '/account/security/webauthn/add/options',
});

window.WebAuthn = {
    register: (element) => {
        webauthnRegister({
            attestation: 'direct',
            authenticatorSelection: {
                requireResidentKey: true,
                userVerification: 'required'
            }
        }).then(response => {
            Turbo.visit(element.dataset.redirect)
        }).catch(error => {
            alert(error)
            Turbo.visit(element.dataset.redirect)
        })
    }
}