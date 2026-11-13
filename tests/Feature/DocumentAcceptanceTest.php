<?php

namespace Tests\Feature;

use App\Models\Branch;
use App\Models\DocumentTemplate;
use App\Models\SignedDocument;
use App\Models\User;
use App\Support\DocumentSnapshot;
use Carbon\Carbon;
use Database\Seeders\DocumentTemplateSeeder;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentAcceptanceTest extends TestCase
{
    use RefreshDatabase;

    public function test_imported_contract_is_filled_saved_and_expires_at_month_end(): void
    {
        $this->travelTo(Carbon::parse('2026-11-12 12:00:00'));
        $this->seed(DocumentTemplateSeeder::class);
        $t = DocumentTemplate::where('kind', 'contract')->firstOrFail();
        $t->forceFill(['published_at' => now()])->save();
        $u = User::factory()->create(['firstname' => 'Анна', 'lastname' => 'Иванова']);
        $u->passportDetail()->create(['series' => '0123', 'number' => '001234', 'issue_date' => '2010-01-01', 'issue_by' => 'Отделением МВД г. Томска', 'address' => 'Тестовый адрес']);
        $branch = Branch::forceCreate(['branch_name' => 'Метеор', 'branch_address' => 'Томск']);
        $child = $u->children()->create(['firstname' => 'Иван', 'lastname' => 'Иванов', 'birthdate' => '2015-01-01', 'branch_id' => $branch->id]);
        $this->actingAs($u)->get(route('documents.preview', [$t, 'child_id' => $child->id, 'representation' => 'Родитель']))->assertOk()->assertSee('Иванова Анна')->assertSee('Иванов Иван')->assertSee('0123')->assertDontSee('{{signer_name}}');
        $snapshot = DocumentSnapshot::build($t, $u, $child, 'Родитель');
        $this->post(route('documents.sign', $t), ['agreement' => 1, 'child_id' => $child->id, 'representation' => 'Родитель', 'preview_checksum' => DocumentSnapshot::checksum($snapshot)])->assertSessionHasNoErrors();
        $doc = SignedDocument::firstOrFail();
        $this->assertSame('authenticated_checkbox', $doc->confirmation_method);
        $this->assertSame('2026-11-30', $doc->expires_at->toDateString());
        $this->assertSame('001234', $doc->snapshot['passport']['number']);
        $this->assertStringNotContainsString('{{', $doc->snapshot['body']);
        $this->assertNotNull($doc->contract_id);
        $response = $this->get(route('documents.pdf', $doc));
        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
    }

    public function test_profile_change_requires_reviewing_updated_document(): void
    {
        $t = DocumentTemplate::factory()->create(['kind' => 'consent']);
        $u = User::factory()->create();
        $snapshot = DocumentSnapshot::build($t, $u, null, null);
        $u->update(['firstname' => 'Изменено']);
        $this->actingAs($u)->post(route('documents.sign', $t), ['agreement' => 1, 'preview_checksum' => DocumentSnapshot::checksum($snapshot)])->assertSessionHasErrors('agreement');
        $this->assertDatabaseCount('signed_documents', 0);
    }

    public function test_cannot_confirm_document_for_someone_elses_child(): void
    {
        $t = DocumentTemplate::factory()->create(['kind' => 'consent']);
        $u = User::factory()->create();
        $other = User::factory()->create();
        $child = $other->children()->create(['firstname' => 'Иван', 'lastname' => 'Иванов', 'birthdate' => '2015-01-01', 'branch_id' => 1]);
        $this->actingAs($u)->post(route('documents.sign', $t), ['agreement' => 1, 'child_id' => $child->id, 'representation' => 'Родитель', 'preview_checksum' => str_repeat('0', 64)])->assertNotFound();
        $this->assertDatabaseCount('signed_documents', 0);
    }

    public function test_draft_can_be_edited_but_published_version_cannot(): void
    {
        $u = User::factory()->create();
        $u->assignRole(Role::create(['name' => 'admin', 'guard_name' => 'web']));
        $t = DocumentTemplate::factory()->create(['published_at' => null]);
        $data = ['title' => 'Обновлённый договор', 'body' => trim(str_repeat('Условия договора. ', 4))];
        $this->actingAs($u)->patch(route('admin.templates.update', $t), $data)->assertSessionHasNoErrors();
        $this->assertSame($data['body'], $t->fresh()->body);
        $this->post(route('admin.templates.publish', $t), ['approved' => 1])->assertSessionHasNoErrors();
        $this->patch(route('admin.templates.update', $t), array_merge($data, ['title' => 'Нельзя менять']))->assertForbidden();
        $this->assertSame($data['title'],$t->fresh()->title);
    }
}
