<!DOCTYPE html><html lang="ru"><head><meta charset="utf-8"><style>
@page { margin: 2cm; } body { font-family: "DejaVu Sans",sans-serif; font-size: 10pt; line-height: 1.4; color:#111; } h1 { font-size:16pt; } h2 { font-size:12pt; } .body { white-space:pre-wrap; word-wrap:break-word; } .hash { font-size:8pt; word-wrap:break-word; }
</style></head><body>
@php($snapshot=$document->snapshot)
<h1>{{ $document->title }}</h1><p>Экземпляр № {{ $document->id }} · версия {{ $document->version }}</p>
<h2>Участники</h2>
<p>Подписант: {{ implode(' ',array_filter(collect($snapshot['signer'] ?? [])->only(['lastname','firstname','patronymic'])->all())) }}</p>
<p>Спортсмен: {{ implode(' ',array_filter(collect($snapshot['athlete'] ?? [])->only(['lastname','firstname','patronymic'])->all())) }}</p>
@if($snapshot['representation'] ?? null)<p>Основание представительства: {{ $snapshot['representation'] }}</p>@endif
<div class="body">{{ $snapshot['body'] ?? '' }}</div>
@if($snapshot['passport'] ?? null)<h2>Реквизиты подписанта</h2>
@foreach(['series'=>'Серия','number'=>'Номер','issue_date'=>'Дата выдачи','issue_by'=>'Кем выдан','address'=>'Адрес'] as $key=>$label)<p>{{ $label }}: {{ $snapshot['passport'][$key] ?? '' }}</p>@endforeach @endif
<h2>Сведения о подтверждении</h2><p>Дата: {{ $document->signed_at->format('d.m.Y H:i:s') }} ({{ config('app.timezone') }})<br>Способ: {{ $document->confirmation_method === 'authenticated_checkbox' ? 'отметка согласия в авторизованном кабинете' : 'пароль аккаунта и отметка согласия' }}<br>IP: {{ $document->ip_address }}</p>
@if($document->expires_at)<p>Срок действия до {{ $document->expires_at->format('d.m.Y') }}</p>@endif
<p>Статус на дату выгрузки: {{ $document->statusLabel() }}</p>
@if($document->revoked_at)<p>Дата прекращения: {{ $document->revoked_at->format('d.m.Y H:i:s') }}. Причина: {{ $document->revocation_reason }}</p>@endif
<p class="hash">Контрольная сумма сохранённых данных SHA-256:<br>{{ $document->checksum }}</p>
</body></html>