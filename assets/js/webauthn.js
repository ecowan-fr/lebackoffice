import { useLogin, useRegistration } from '@web-auth/webauthn-helper';

const webauthnRegister = useRegistration({
    actionUrl: '/account/security/add',
    actionHeader: {},
    optionsUrl: '/account/security/add/options',
    optionsHeader: {},
});

window.WebAuthn = {
    register: () => {
        webauthnRegister({
            authenticatorSelection: {
                username: 'thomas@tydoo.fr',
                displayName: 'JD',
                requireResidentKey: true,
                userVerification: 'required'
            }
        }).then((response) => {
            console.log(response)
        });
    }
}