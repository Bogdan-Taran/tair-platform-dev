<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>{{ $title }} — Администратор ТАИР</title>
    <link rel="stylesheet" href="{{ asset('frontend/css/styles.css') }}">
    <link rel="stylesheet" href="{{ asset('frontend/css/style-admin-dashboard.css') }}">
</head>
<body class="admin-page">
@include('layouts.navigation')
<div class="admin-shell">
    <nav class="admin-sidebar" aria-label="Разделы администратора">
        @foreach(['contracts' => 'Документы', 'parents' => 'Родители', 'children' => 'Дети'] as $key => $label)
            <a href="{{ route('admin.dashboard', ['section' => $key]) }}" @class(['admin-tab', 'is-active' => $section === $key]) @if($section === $key) aria-current="page" @endif>{{ $label }}</a>
        @endforeach
    </nav>
    <main class="admin-content" id="main-content">
        <h1>{{ $title }}</h1>
        <nav class="admin-folders" aria-label="Филиалы">
            <a href="{{ route('admin.dashboard', array_merge(request()->only('section', 'q', 'sort'), ['branch' => null])) }}" @class(['admin-folder', 'is-active' => !$branchId]) @if(!$branchId) aria-current="true" @endif><span>Все</span></a>
            @foreach($branches as $branch)
                <a href="{{ route('admin.dashboard', array_merge(request()->only('section', 'q', 'sort'), ['branch' => $branch->id])) }}" @class(['admin-folder', 'is-active' => (int) $branchId === $branch->id]) @if((int) $branchId === $branch->id) aria-current="true" @endif><span>{{ $branch->branch_name }}</span></a>
            @endforeach
        </nav>
        <form class="admin-filters" method="get" action="{{ route('admin.dashboard') }}" role="search">
            <input type="hidden" name="section" value="{{ $section }}">
            @if($branchId)<input type="hidden" name="branch" value="{{ $branchId }}">@endif
            <span class="admin-filter-label">Фильтры</span>
            <label class="sr-only" for="admin-sort">Сортировка</label>
            <select id="admin-sort" name="sort">
                @foreach(['newest' => 'Сначала новые', 'oldest' => 'Сначала старые', 'name_asc' => 'От А до Я', 'name_desc' => 'От Я до А'] as $value => $label)
                    <option value="{{ $value }}" @selected($sort === $value)>{{ $label }}</option>
                @endforeach
            </select>
            <label class="sr-only" for="admin-search">Поиск по ФИО родителя или ребёнка</label>
            <input id="admin-search" type="search" name="q" value="{{ $search }}" placeholder="поиск по ФИО" maxlength="150">
            <button type="submit" class="admin-search-button"><span aria-hidden="true" class="search-symbol"></span>Найти</button>
            @if($search !== '' || $branchId || $sort !== 'newest')
                <a class="admin-reset" href="{{ route('admin.dashboard', ['section' => $section]) }}">Сбросить</a>
            @endif
        </form>
        @if($errors->any())<div role="alert">{{ $errors->first() }}</div>@endif
        <p class="admin-result-count">Найдено: {{ $records->total() }}</p>
        <div class="admin-cards">
            @forelse($records as $record)
                @php
                    $parent = $section === 'parents' ? $record : $record->user;
                    $children = $section === 'children' ? collect([$record]) : ($parent?->children ?? collect());
                    $cardBranches = $children->pluck('branch')->filter()->unique('id');
                @endphp
                <article class="admin-card">
                    <div class="admin-badges">
                        @forelse($cardBranches as $cardBranch)<span>{{ $cardBranch->branch_name }}</span>@empty<span>Без филиала</span>@endforelse
                    </div>
                    @if($section === 'children')
                        <h2>{{ $record->lastname }} {{ $record->firstname }} {{ $record->patronymic }}</h2>
                        <p class="admin-secondary">Родитель: {{ $parent?->lastname }} {{ $parent?->firstname }} {{ $parent?->patronymic }}</p>
                        <p class="admin-date">Дата рождения: {{ \Carbon\Carbon::parse($record->birthdate)->format('d.m.Y') }}</p>
                    @else
                        <h2 title="{{ $parent?->lastname }} {{ $parent?->firstname }} {{ $parent?->patronymic }}">{{ $parent?->lastname }} {{ $parent?->firstname }} {{ $parent?->patronymic ? mb_substr($parent->patronymic, 0, 1).'.' : '' }}</h2>
                        @forelse($children as $child)
                            <p class="admin-secondary" title="{{ $child->lastname }} {{ $child->firstname }} {{ $child->patronymic }}">{{ $child->lastname }} {{ $child->firstname }} {{ mb_substr($child->patronymic, 0, 1) }}.</p>
                        @empty<p class="admin-secondary">Дети не добавлены</p>@endforelse
                        @if($section === 'contracts')
                            <p class="admin-date">{{ \Carbon\Carbon::parse($record->sign_date)->locale('ru')->isoFormat('D MMMM YYYY [г.]') }}</p>
                            <details class="admin-details"><summary>Договор № {{ $record->id }}</summary><p>Подписан {{ \Carbon\Carbon::parse($record->sign_date)->format('d.m.Y H:i') }}</p><p>{{ $parent?->email }}</p></details>
                        @else
                            <p class="admin-date">Договоров: {{ $record->contracts_count }}</p>
                            <p class="admin-secondary">{{ $parent?->email }}</p>
                        @endif
                    @endif
                    @if($parent?->phone)
                        <a class="admin-phone" href="tel:{{ preg_replace('/[^0-9+]/', '', $parent->phone) }}"><img src="{{ asset('frontend/src/assets/phone-icon.png') }}" alt="" width="28" height="28">{{ $parent->phone }}</a>
                    @else<p class="admin-secondary">Телефон не указан</p>@endif
                </article>
            @empty
                <div class="admin-empty"><h2>{{ $search !== '' || $branchId ? 'Ничего не найдено' : 'Пока нет записей' }}</h2><p>{{ $search !== '' || $branchId ? 'Измените запрос или выберите другой филиал.' : 'Здесь появятся данные после регистрации и подписания договоров.' }}</p></div>
            @endforelse
        </div>
        @if($records->hasPages())
            <nav class="admin-pagination" aria-label="Страницы результатов">
                @if($records->previousPageUrl())<a href="{{ $records->previousPageUrl() }}" rel="prev">← Назад</a>@endif
                <span>Страница {{ $records->currentPage() }} из {{ $records->lastPage() }}</span>
                @if($records->nextPageUrl())<a href="{{ $records->nextPageUrl() }}" rel="next">Далее →</a>@endif
            </nav>
        @endif
    </main>
</div>
<script src="{{ asset('frontend/js/admin-dashboard.js') }}" defer></script>
</body>
</html>
