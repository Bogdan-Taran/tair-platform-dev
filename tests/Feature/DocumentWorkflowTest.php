<?php

namespace Tests\Feature;

use App\Models\DocumentTemplate;
use App\Models\SignedDocument;
use App\Models\User;
use App\Support\DocumentSnapshot;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Tests\TestCase;

class DocumentWorkflowTest extends TestCase
{
    use RefreshDatabase;

    public function test_signing_is_disabled_until_method_is_approved(): void
    {
        config(['documents.signing_enabled' => false]);
        $t = DocumentTemplate::factory()->create(['kind' => 'consent']);
        $u = User::factory()->create();
        $this->actingAs($u)->post(route('documents.sign', $t), ['agreement' => 1, 'preview_checksum' => DocumentSnapshot::checksum(DocumentSnapshot::build($t, $u->fresh(), null, null))])->assertSessionHasErrors('agreement');
        $this->assertDatabaseCount('signed_documents', 0);
    }

    public function test_snapshot_is_immutable_and_resubmission_does_not_duplicate(): void
    {
        config(['documents.signing_enabled' => true]);
        $t = DocumentTemplate::factory()->create(['kind' => 'consent', 'body' => 'Исходный текст']);
        $u = User::factory()->create(['firstname' => 'Анна']);
        $this->actingAs($u)->post(route('documents.sign', $t), ['agreement' => 1, 'preview_checksum' => DocumentSnapshot::checksum(DocumentSnapshot::build($t, $u->fresh(), null, null))])->assertSessionHasNoErrors();
        $doc = SignedDocument::firstOrFail();
        $this->assertSame('Исходный текст', $doc->snapshot['body']);
        $u->update(['firstname' => 'НовоеИмя']);
        $t->update(['body' => 'Изменённый текст']);
        $this->post(route('documents.sign', $t), ['agreement' => 1, 'preview_checksum' => DocumentSnapshot::checksum(DocumentSnapshot::build($t, $u->fresh(), null, null))])->assertSessionHasNoErrors();
        $this->assertDatabaseCount('signed_documents', 1);
        $this->get(route('documents.show', $doc))->assertOk()->assertSee('Исходный текст')->assertSee('Анна')->assertDontSee('НовоеИмя');
        $this->assertStringNotContainsString('Исходный текст', $doc->getRawOriginal('snapshot'));
    }

    public function test_unauthorized_documents_are_not_disclosed_and_consent_can_be_revoked(): void
    {
        $doc = SignedDocument::factory()->create(['kind' => 'consent']);
        $other = User::factory()->create();
        $this->actingAs($other)->get(route('documents.show', $doc))->assertNotFound();
        $this->patch(route('documents.revoke', $doc), ['reason' => 'Отзываю'])->assertNotFound();
        $this->actingAs($doc->user)->patch(route('documents.revoke', $doc), ['reason' => 'Отзываю'])->assertSessionHasNoErrors();
        $this->assertNotNull($doc->fresh()->revoked_at);
        $this->assertSame('Отозвано', $doc->fresh()->statusLabel());
    }

    public function test_contract_requires_passport_and_explicit_consent(): void
    {
        config(['documents.signing_enabled' => true]);
        $t = DocumentTemplate::factory()->create();
        $u = User::factory()->create();
        $this->actingAs($u)->post(route('documents.sign', $t), ['agreement' => 0, 'preview_checksum' => str_repeat('0', 64)])->assertSessionHasErrors('agreement');
        $this->post(route('documents.sign', $t), ['agreement' => 1, 'preview_checksum' => DocumentSnapshot::checksum(DocumentSnapshot::build($t, $u->fresh(), null, null))])->assertSessionHasErrors('agreement');
        $this->assertDatabaseCount('signed_documents', 0);
    }

    public function test_templates_are_admin_only_and_drafts_are_not_public(): void
    {
        $u = User::factory()->create();
        $t = DocumentTemplate::factory()->create(['published_at' => null]);
        $this->actingAs($u)->get(route('admin.templates.index'))->assertForbidden();
        $this->get(route('documents.preview', $t))->assertNotFound();
        $u->assignRole(Role::create(['name' => 'admin', 'guard_name' => 'web']));
        $this->post(route('admin.templates.publish', $t), ['approved' => 1])->assertSessionHasNoErrors();
        $this->get(route('documents.preview', $t))->assertOk()->assertSee($t->body);
    }

    public function test_signed_history_prevents_cascading_account_deletion(): void
    {
        $doc = SignedDocument::factory()->create();
        $this->actingAs($doc->user)->delete('/profile', ['password' => 'password'])->assertSessionHasErrorsIn('userDeletion', 'password');
        $this->assertDatabaseHas('signed_documents', ['id' => $doc->id]);
        $this->assertDatabaseHas('users', ['id' => $doc->user_id]);
    }

    public function test_pdf_is_generated_from_snapshot_and_is_private(): void
    {
        $doc = SignedDocument::factory()->create(['snapshot' => ['body' => 'Договор ТАИР', 'signer' => ['firstname' => 'Анна'], 'athlete' => ['firstname' => 'Иван']]]);
        $other = User::factory()->create();
        $this->actingAs($other)->get(route('documents.pdf', $doc))->assertNotFound();
        $response = $this->actingAs($doc->user)->get(route('documents.pdf', $doc));
        $response->assertOk()->assertHeader('Content-Type', 'application/pdf');
        $this->assertStringStartsWith('%PDF-', $response->getContent());
        $this->assertDatabaseHas('audit_logs', ['action' => 'document.downloaded', 'subject_id' => $doc->id]);
    }
}
