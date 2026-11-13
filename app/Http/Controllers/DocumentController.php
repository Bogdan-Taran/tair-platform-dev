<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DocumentTemplate;
use App\Models\SignedDocument;
use App\Models\User;
use App\Support\DocumentSnapshot;
use Dompdf\Dompdf;
use Dompdf\Options;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class DocumentController extends Controller
{
    public function registry(Request $request): View
    {
        $filters = $request->validate(['q' => ['nullable', 'string', 'max:150'], 'kind' => ['nullable', Rule::in(['contract', 'consent'])]]);
        $query = SignedDocument::with(['user', 'child'])->when($filters['kind'] ?? null, fn ($q, $kind) => $q->where('kind', $kind));
        if (! empty($filters['q'])) {
            $search = $filters['q'];
            $query->whereHas('user', fn ($q) => $q->where(fn ($p) => $p->whereLike('lastname', '%'.$search.'%')->orWhereLike('firstname', '%'.$search.'%')->orWhereLike('phone', '%'.$search.'%')));
        }

        return view('admin.documents', ['documents' => $query->latest('signed_at')->paginate(20)->withQueryString()]);
    }

    public function index(Request $request): View
    {
        $templates = DocumentTemplate::whereNotNull('published_at')->orderByDesc('id')->get()->unique('kind');
        $documents = SignedDocument::where('user_id', $request->user()->id)->latest('signed_at')->paginate(20);
        $legacyContracts = $request->user()->contracts()->whereDoesntHave('signedDocument')->latest('sign_date')->get();

        return view('documents', compact('templates', 'documents', 'legacyContracts'));
    }

    public function preview(Request $request, DocumentTemplate $template): View
    {
        abort_unless($template->published_at, 404);
        $data = $request->validate(['child_id' => ['nullable', 'integer'], 'representation' => ['nullable', 'string', 'max:500']]);
        $children = $request->user()->children()->whereNull('archived_at')->with('branch')->get();
        $child = empty($data['child_id']) ? null : $request->user()->children()->whereNull('archived_at')->findOrFail($data['child_id']);
        $representation = $data['representation'] ?? null;
        $snapshot = DocumentSnapshot::build($template, $request->user(), $child, $representation);
        $checksum = DocumentSnapshot::checksum($snapshot);

        return view('documents.preview', compact('template', 'children', 'child', 'representation', 'snapshot', 'checksum'));
    }

    public function store(Request $request, DocumentTemplate $template): RedirectResponse
    {
        abort_unless($template->published_at, 404);
        if (! config('documents.signing_enabled')) {
            return back()->withErrors(['agreement' => 'Подписание станет доступно после утверждения способа подтверждения клубом.']);
        }
        $data = $request->validate(['agreement' => ['accepted'], 'preview_checksum' => ['required', 'string', 'size:64'], 'child_id' => ['nullable', 'integer'], 'representation' => ['nullable', 'string', 'max:500']]);
        $document = DB::transaction(function () use ($request, $template, $data) {
            $user = User::lockForUpdate()->findOrFail($request->user()->id);
            $child = empty($data['child_id']) ? null : $user->children()->whereNull('archived_at')->findOrFail($data['child_id']);
            if ($child && empty($data['representation'])) {
                throw ValidationException::withMessages(['representation' => 'Укажите основание законного представительства.']);
            }
            $subject = $child ? 'child:'.$child->id : 'self';
            $existing = SignedDocument::where('user_id', $user->id)->where('document_template_id', $template->id)->where('subject_key', $subject)->first();
            if ($existing) {
                return $existing;
            }
            if ($template->kind === 'contract' && ! $user->passportDetail) {
                throw ValidationException::withMessages(['agreement' => 'Сначала заполните паспортные данные в профиле.']);
            }
            $snapshot = DocumentSnapshot::build($template, $user, $child, $data['representation'] ?? null);
            if (! hash_equals(DocumentSnapshot::checksum($snapshot), $data['preview_checksum'])) {
                throw ValidationException::withMessages(['agreement' => 'Данные изменились. Обновите предварительный просмотр документа перед подтверждением.']);
            }
            $reference = now()->format('Ymd').'-'.$user->id.'-'.Str::upper(Str::random(8));
            $snapshot['body'] = str_replace('Номер будет присвоен при подтверждении', $reference, $snapshot['body']);
            $snapshot['number'] = $reference;
            $document = SignedDocument::create(['user_id' => $user->id, 'document_template_id' => $template->id, 'child_id' => $child?->id, 'subject_key' => $subject,
                'kind' => $template->kind, 'title' => $template->title, 'version' => $template->version, 'snapshot' => $snapshot, 'checksum' => hash('sha256', json_encode($snapshot, JSON_UNESCAPED_UNICODE | JSON_THROW_ON_ERROR)),
                'confirmation_method' => 'authenticated_checkbox', 'ip_address' => $request->ip(), 'signed_at' => now(), 'expires_at' => $template->validity_mode === 'month_end' ? now()->endOfMonth() : ($template->valid_days ? now()->addDays($template->valid_days) : null)]);
            if ($template->kind === 'contract') {
                $contract = $user->contracts()->create(['sign_date' => $document->signed_at, 'ip_address' => $request->ip()]);
                $document->update(['contract_id' => $contract->id]);
            }
            AuditLog::record('document.confirmed', 'signed_document', $document->id);

            return $document;
        });

        return redirect()->route('documents.show', $document)->with('status', 'Подтверждение сохранено.');
    }

    private function authorizeDocument(Request $request, SignedDocument $document): void
    {
        abort_unless($document->user_id === $request->user()->id || $request->user()->hasRole('admin'), 404);
    }

    public function show(Request $request, SignedDocument $document): View
    {
        $this->authorizeDocument($request, $document);

        return view('documents.show', compact('document'));
    }

    public function pdf(Request $request, SignedDocument $document): Response
    {
        $this->authorizeDocument($request, $document);
        $options = new Options;
        $options->setDefaultFont('DejaVu Sans');
        $options->setIsRemoteEnabled(false);
        $options->setIsPhpEnabled(false);
        $options->setChroot(base_path('vendor/dompdf/dompdf'));
        $pdf = new Dompdf($options);
        $pdf->loadHtml(view('documents.pdf', compact('document'))->render(), 'UTF-8');
        $pdf->setPaper('A4');
        $pdf->render();
        AuditLog::record('document.downloaded', 'signed_document', $document->id);

        return response($pdf->output(), 200, ['Content-Type' => 'application/pdf', 'Content-Disposition' => 'attachment; filename="tair-document-'.$document->id.'.pdf"', 'Cache-Control' => 'private, no-store', 'X-Content-Type-Options' => 'nosniff']);
    }

    public function revoke(Request $request, SignedDocument $document): RedirectResponse
    {
        $this->authorizeDocument($request, $document);
        abort_unless($document->kind === 'consent' || $request->user()->hasRole('admin'), 403);
        $data = $request->validate(['reason' => ['required', 'string', 'max:1000']]);
        DB::transaction(function () use ($document, $data) {
            $document = SignedDocument::lockForUpdate()->findOrFail($document->id);
            if (! $document->revoked_at) {
                $document->forceFill(['revoked_at' => now(), 'revocation_reason' => $data['reason']])->save();
                AuditLog::record('document.revoked', 'signed_document', $document->id);
            }
        });

        return back()->with('status', 'Статус документа изменён. Сохранённый экземпляр оставлен в истории.');
    }
}
