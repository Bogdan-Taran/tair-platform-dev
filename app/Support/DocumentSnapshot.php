<?php

namespace App\Support;

use App\Models\Child;
use App\Models\DocumentTemplate;
use App\Models\User;

class DocumentSnapshot
{
    public static function build(DocumentTemplate $template, User $user, ?Child $child, ?string $representation): array
    {
        $name = fn ($person) => trim(implode(' ', array_filter([$person->lastname, $person->firstname, $person->patronymic])));
        $passport = $template->kind === 'contract' ? $user->passportDetail?->only(['series', 'number', 'issue_date', 'issue_by', 'address']) : null;
        $replacements = ['{{document_number}}' => 'Номер будет присвоен при подтверждении', '{{signed_date}}' => now()->format('d.m.Y'), '{{signer_name}}' => $name($user), '{{athlete_name}}' => $name($child ?? $user), '{{representation}}' => $child ? ($representation ?? 'Не указано') : 'От своего имени'];
        foreach (['series', 'number', 'issue_date', 'issue_by', 'address'] as $key) {
            $replacements['{{passport_'.$key.'}}'] = $passport[$key] ?? 'Не заполнено';
        }

        return ['title' => $template->title, 'version' => $template->version, 'body' => strtr($template->body, $replacements), 'signer' => $user->only(['firstname', 'lastname', 'patronymic', 'birthdate', 'email', 'phone']), 'athlete' => ($child ?? $user)->only(['firstname', 'lastname', 'patronymic', 'birthdate']), 'passport' => $passport, 'representation' => $child ? $representation : null];
    }

    public static function checksum(array $snapshot): string
    {
        return hash('sha256', json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR));
    }
}
