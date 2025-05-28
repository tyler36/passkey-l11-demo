import './bootstrap';

import Alpine from 'alpinejs';
import {browserSupportsWebAuthn, startAuthentication, startRegistration} from '@simplewebauthn/browser';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
  Alpine.data('registerPasskey', () => ({
    name: '',
    errors: null,
    browserSupportsWebAuthn,

    async register(form) {
      this.errors = null

      // CHECK: Return early if browser does NOT support WebAuthn
      if (! this.browserSupportsWebAuthn()) {
        return;
      }

      // Get the Passkeys options via API.
      const options =  await axios.get('/api/passkeys/register', {
        params: { name: this.name },
        // 422 (validation erros), are allowed to 'pass', we'll catch them later.
        validateStatus: (status) => [200, 422].includes(status)
      });


      // CHECK: Validation failed so exit early.
      if (options.status === 422) {
        this.errors = options.data.errors;
        return;
      }

      let passkey;
      try {
        // Catch 'cancel' events and timeouts.
        passkey = await startRegistration(options.data);
      } catch (error) {
        this.errors = { name: ['Passkey creation failed. Please try again.']};
        return;
      }

      form.addEventListener('formdata', ({formData}) => {
        // Mutate 'passkey' data
        formData.set('passkey', JSON.stringify(passkey))
      })

      form.submit()
    }
  }))

  Alpine.data('authenticatePasskey', () => ({
    async authenticate() {
      const options = await axios.get('/api/passkeys/authenticate')
      const answer = await startAuthentication(options.data)
    }
  }))
})

Alpine.start();
