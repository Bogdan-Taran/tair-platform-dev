<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>

    <header class="container">
        <h2>Пока у вас нет подписанных договоров об оказании платных услуг</h2>
        <p>Чтобы ознакомиться и подписать договор, нажмите кнопку ниже, он появится здесь после подписания</p>
    </header>

    <div class="mt-5">
        <a href="{{ route('add-child') }}" class="btn-black">К договору</a>
        <a href="{{ route('add-child') }}" class="btn-black">Добавить ребёнка</a>
    </div>

    <div class="container signed-contracts-container mt-4">
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
