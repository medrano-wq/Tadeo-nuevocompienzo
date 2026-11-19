<x-guest-layout>
    <div class="mb-4 text-sm text-gray-600">
        {{ __('Hemos enviado un código de verificación de 6 dígitos a tu correo electrónico. Por favor, ingrésalo a continuación para continuar.') }}
    </div>

    <!-- Session Status -->
    <x-auth-session-status class="mb-4" :status="session('status')" />

    <form method="POST" action="{{ route('login.code.verify') }}">
        @csrf

        <!-- Token de Verificación -->
        <div>
            <x-input-label for="token" :value="__('Código de verificación')" />
            
            <x-text-input id="token" 
                          class="block mt-1 w-full text-center tracking-widest text-lg" 
                          type="text" 
                          name="token" 
                          required 
                          autofocus 
                          autocomplete="one-time-code" 
                          maxlength="6" />

            <x-input-error :messages="$errors->get('token')" class="mt-2" />
        </div>

        <div class="flex items-center justify-end mt-4">
            <x-primary-button class="ms-3">
                {{ __('Verificar código') }}
            </x-primary-button>
        </div>
    </form>
</x-guest-layout>