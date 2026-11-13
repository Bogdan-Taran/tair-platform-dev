@extends('layouts.app')
@section('main-content')<main class="main account-form"><h1>Журнал действий</h1><a href="{{ route('admin.users.index') }}">Пользователи</a>
@forelse($logs as $log)<article class="account-record"><p>{{ $log->created_at->format('d.m.Y H:i:s') }} · Администратор № {{ $log->actor_id ?? '—' }}</p><p>{{ $log->action }} · {{ $log->subject_type }} № {{ $log->subject_id ?? '—' }}</p></article>@empty<p>Действий пока нет.</p>@endforelse {{ $logs->links() }}</main>@endsection
