<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <button class="btn-black">К договору</button>
    <a href="{{ route('roles.create')}}">Добавить роль</a>

    <div class="container signed-contracts-container">
        @foreach($roles as $role)
            <article class="contract-item">
                <h3>{{ $role->name }}</h3>
            </article>
        @endforeach
    </div>
</x-app-layout>
