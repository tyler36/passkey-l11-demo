import './bootstrap';

import Alpine from 'alpinejs';
import {startRegistration} from '@simplewebauthn/browser';

window.Alpine = Alpine;

document.addEventListener('alpine:init', () => {
  Alpine.data('registerPasskey', () => ({
    name: '',
    errors: null,

    async register(form) {
      this.errors = null

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

      const passkey = await startRegistration(options.data)

      form.addEventListener('formdata', ({formData}) => {
        // Mutate 'passkey' data
        formData.set('passkey', JSON.stringify(passkey))
      })

      form.submit()
    }
  }))
})

Alpine.start();
