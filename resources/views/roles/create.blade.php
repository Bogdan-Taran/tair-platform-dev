<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
    <h3>Добавление новой роли</h3>
    @if(session('status'))
        <div class="alert alert-success">
            {{ session('status') }}
        </div>
    @endif
    @if ($errors->any())
        <div class="alert alert-danger">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif
    <form action="{{ route('roles.store') }}" method="post">
        @csrf
        <label for="child_firstname">Title</label>
        <input type="text" class="" name="name" id="child_firstname" required>
        <br>
        @foreach($permissions as $permission)
            <div class="form-group">
                <input type="checkbox" class="form-checkbox" name="permissions[]" id="exampleCheck{{ $permission->id }}" value="{{ $permission->id }}">
                <label class="form-check-label" for="exampleCheck{{ $permission->id }}">{{ $permission->name }}</label>
            </div>
        @endforeach





        <div>
            <button>Отменить</button>
            <button type="submit">Сохранить</button>
        </div>
    </form>
</x-app-layout>
