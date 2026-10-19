@extends('layouts.app')

@section('main-content')

    <main class="main">
        <h3>Добавление ребёнка</h3>
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

        <form action="{{ route('store-child') }}" method="post">
            @csrf
            <label for="firstname">Имя</label>
            <input type="text" class="" name="firstname" id="firstname" required>
            <br>

            <label for="lastname">Фамилия</label>
            <input type="text" class="" name="lastname" id="lastname" required>
            <br>

            <label for="patronymic">Отчество</label>
            <input type="text" class="" name="patronymic" id="patronymic" required>
            <br>

            <label for="birthdate">Дата рождения</label>
            <input type="date" class="" name="birthdate" id="birthdate" required>
            <br>

            <label for="branch">Филиал</label>
            <select id="branch" name="branch_id" required>
                <option value="" disabled selected>Выберите филиал...</option>
                <option value="1">Академгородок</option>
                <option value="2">Школа 53</option>
                <option value="3">Метеор</option>
            </select>
            <br>

            <div>
                <button>Отменить</button>
                <button type="submit">Сохранить</button>
            </div>
        </form>
    </main>

@endsection
