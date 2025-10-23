@extends('layouts.app')

@section('content')
<div class="max-w-2xl mx-auto p-6 bg-white dark:bg-gray-800 shadow rounded-lg">
    <h2 class="text-xl font-semibold mb-6 text-gray-800 dark:text-gray-200">
        Modifica Profilo
    </h2>

    {{-- Messaggio di successo --}}
    @if (session('status') === 'profile-updated')
        <div class="mb-4 p-3 text-sm text-green-700 bg-green-100 rounded">
            Profilo aggiornato con successo!
        </div>
    @endif

    {{-- Form aggiornamento --}}
    <form method="POST" action="{{ route('profile.update') }}">
        @csrf
        @method('PATCH')

        {{-- Nome --}}
        <div class="mb-4">
            <label for="name" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Nome</label>
            <input id="name" name="name" type="text" value="{{ old('name', $user->name) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            @error('name')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Email --}}
        <div class="mb-4">
            <label for="email" class="block text-sm font-medium text-gray-700 dark:text-gray-300">Email</label>
            <input id="email" name="email" type="email" value="{{ old('email', $user->email) }}"
                class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm dark:bg-gray-700 dark:border-gray-600 dark:text-white">
            @error('email')
                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
            @enderror
        </div>

        {{-- Pulsante Salva --}}
        <div class="flex justify-end">
            <button type="submit"
                class="px-4 py-2 bg-indigo-600 text-white rounded-md hover:bg-indigo-700">
                Salva modifiche
            </button>
        </div>
    </form>

    <hr class="my-6">

    {{-- Sezione elimina account --}}
    <div>
        <h3 class="text-lg font-medium text-red-600">Elimina Account</h3>
        <p class="text-sm text-gray-600 dark:text-gray-400 mb-4">
            Una volta eliminato l’account, tutti i dati saranno rimossi in modo permanente.
        </p>

        <form method="POST" action="{{ route('profile.destroy') }}">
            @csrf
            @method('DELETE')

            <button type="submit"
                class="px-4 py-2 bg-red-600 text-white rounded-md hover:bg-red-700"
                onclick="return confirm('Sei sicuro di voler eliminare il tuo account?')">
                Elimina Account
            </button>
        </form>
    </div>
</div>
@endsection
