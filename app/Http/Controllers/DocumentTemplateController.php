<?php

namespace App\Http\Controllers;

use App\Models\AuditLog;
use App\Models\DocumentTemplate;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class DocumentTemplateController extends Controller
{
    public function index(): View
    {
        return view('admin.templates', ['templates' => DocumentTemplate::latest('id')->paginate(20)]);
    }

    public function store(Request $request): RedirectResponse
    {
        $data = $request->validate(['kind' => ['required', Rule::in(['contract', 'consent'])], 'version' => ['required', 'string', 'max:80', Rule::unique('document_templates')->where('kind', $request->input('kind'))], 'title' => ['required', 'string', 'max:255'], 'body' => ['required', 'string', 'min:30', 'max:100000'], 'validity_mode' => ['required', Rule::in(['days', 'month_end', 'none'])], 'valid_days' => ['nullable', 'integer', 'min:1', 'max:3650']]);
        if ($data['kind'] === 'contract' && $data['validity_mode'] === 'none') {
            return back()->withInput()->withErrors(['validity_mode' => 'Укажите срок договора.']);
        }
        if ($data['kind'] === 'contract' && $data['validity_mode'] === 'days' && empty($data['valid_days'])) {
            return back()->withInput()->withErrors(['valid_days' => 'Укажите срок договора в днях.']);
        }
        if ($data['kind'] === 'consent' || $data['validity_mode'] !== 'days') {
            $data['valid_days'] = null;
        }
        if ($data['kind'] === 'consent') {
            $data['validity_mode'] = 'none';
        }
        $template = DocumentTemplate::create($data);
        AuditLog::record('document_template.created', 'document_template', $template->id);

        return back()->with('status', 'Версия сохранена как черновик.');
    }

    public function edit(DocumentTemplate $template): View
    {
        abort_if($template->published_at, 403);

        return view('admin.template-edit', compact('template'));
    }

    public function update(Request $request, DocumentTemplate $template): RedirectResponse
    {
        $data = $request->validate(['title' => ['required', 'string', 'max:255'], 'body' => ['required', 'string', 'min:30', 'max:100000']]);
        DB::transaction(function () use ($template, $data) {
            $template = DocumentTemplate::lockForUpdate()->findOrFail($template->id);
            abort_if($template->published_at, 403);
            $template->update($data);
            AuditLog::record('document_template.updated', 'document_template', $template->id);
        });

        return redirect()->route('admin.templates.index')->with('status', 'Черновик сохранён.');
    }

    public function publish(Request $request, DocumentTemplate $template): RedirectResponse
    {
        $request->validate(['approved' => ['accepted']]);
        DB::transaction(function () use ($template) {
            $template = DocumentTemplate::lockForUpdate()->findOrFail($template->id);
            if (! $template->published_at) {
                $template->forceFill(['published_at' => now()])->save();
                AuditLog::record('document_template.published', 'document_template', $template->id);
            }
        });

        return back()->with('status', 'Версия опубликована. Для изменения текста создайте новую версию.');
    }
}
