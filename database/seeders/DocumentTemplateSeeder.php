<?php

namespace Database\Seeders;

use App\Models\DocumentTemplate;
use Illuminate\Database\Seeder;

class DocumentTemplateSeeder extends Seeder
{
    public function run(): void
    {
        DocumentTemplate::firstOrCreate(['kind' => 'contract', 'version' => '2026-2027'], ['title' => 'Договор возмездного оказания услуг', 'body' => file_get_contents(resource_path('documents/contract-2026-2027.txt')), 'validity_mode' => 'month_end', 'valid_days' => null]);
        DocumentTemplate::firstOrCreate(['kind' => 'consent', 'version' => '2026-11-12'], ['title' => 'Согласие на обработку персональных данных', 'body' => file_get_contents(resource_path('documents/consent-2026-11.txt')), 'validity_mode' => 'none', 'valid_days' => null]);
    }
}
