<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <button class="btn-black">К договору</button>
    <a href="{{ route('add-child') }}">Добавить ребёнка</a>

    <div class="container signed-contracts-container">
        @foreach($children as $child)
        <article class="contract-item">
            <p>АКАДЕМГОРОДОК</p>
            <h3>Ченкова Эльвира Ф.</h3>
            <h4>{{ $child->child_firstname }}</h4>
            <p>{{$child->child_birthdate}}</p>
            <a>89528883535</a>
        </article>
        @endforeach
    </div>
</x-app-layout>
