<section>
    <header>
        <h2
            id="managePasskeys"
            class="text-lg font-medium text-gray-900 dark:text-gray-100"
        >
            {{ __('Manage Passkeys') }}
        </h2>

        <p class="mt-1 text-sm text-gray-600 dark:text-gray-400">
            {{ __('Passkeys allow for a more secure, seamless authentication experience on supported devices.') }}
        </p>
    </header>

    <form
        x-data="registerPasskey"
        x-on:submit.prevent="register($el)"
        x-show="browserSupportsWebAuthn()"
        name="createPasskey"
        method="post"
        action="{{ route('passkeys.store') }}"
        class="mt-6 space-y-6"
    >
        @csrf

        <div>
            <x-input-label
                for="create_passkey_passkey_name"
                :value="__('Passkey Name')"
            />
            <x-text-input
                id="create_passkey_passkey_name"
                name="name"
                x-model="name"
                class="mt-1 block w-full"
            />
            <x-input-error
                :messages="$errors->createPasskey->get('name')"
                class="mt-2"
            />
            <x-alpine-input-error messages="errors?.name" />
        </div>

        <div class="flex items-center gap-4">
            <x-primary-button>{{ __('Create Passkey') }}</x-primary-button>
        </div>
    </form>

    <div class="mt-6">
        <h3 class="font-medium text-gray-900 dark:text-gray-100">{{ __('Your Passkeys') }}</h3>
        <ul class="mt-2">
            @foreach ($user->passkeys as $passkey)
                <li class="px-2 py-2 flex justify-between items-center dark:text-gray-100">
                    <div class="flex flex-col">
                        <span class="font-semibold">{{  $passkey->name }}</span>
                        <span class="font-thin text-sm text-gray-600 dark:text-gray-400">Added {{ $passkey->created_at->diffForHumans() }}</span>
                    </div>

                    <form
                        method="post"
                        action="{{ route('passkeys.destroy', $passkey) }}"
                    >
                        @csrf
                        @method('DELETE')

                        <x-danger-button class="">Remove</x-danger-button>
                    </form>
                </li>
            @endforeach
        </ul>
    </div>
</section>
