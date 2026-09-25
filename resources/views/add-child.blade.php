<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard') }}
        </h2>
    </x-slot>
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
        <label for="child_firstname">Имя</label>
        <input type="text" class="" name="child_firstname" id="child_firstname" required>
        <br>

        <label for="child_lastname">Фамилия</label>
        <input type="text" class="" name="child_lastname" id="child_lastname" required>
        <br>

        <label for="child_patronymic">Отчество</label>
        <input type="text" class="" name="child_patronymic" id="child_patronymic" required>
        <br>

        <p>Пол:</p>
        <label>
            <input type="radio" name="child_gender" value="male" checked> Мужской
        </label>
        <label>
            <input type="radio" name="child_gender" value="female"> Женский
        </label>
        <br>

        <label for="child_birthdate">Дата рождения</label>
        <input type="date" class="" name="child_birthdate" id="child_birthdate" required>
        <br>

        <label for="child_branch">Филиал</label>
        <select id="child_branch" name="child_branch_id" required>
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
</x-app-layout>
