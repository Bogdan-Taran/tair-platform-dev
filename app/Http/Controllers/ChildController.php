<?php

namespace App\Http\Controllers;

use App\Http\Requests\ChildRequest;
use App\Models\Branch;
use App\Models\Child;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class ChildController extends Controller
{
    public function create(): View
    {
        return view('children.edit', ['child' => new Child, 'branches' => Branch::orderBy('branch_name')->get()]);
    }

    public function store(ChildRequest $request): RedirectResponse
    {
        $request->user()->children()->create($request->validated());

        return redirect()->route('dashboard')->with('status', 'Ребёнок добавлен.');
    }

    public function edit(Request $request, Child $child): View
    {
        abort_unless($child->user_id === $request->user()->id, 404);

        return view('children.edit', ['child' => $child, 'branches' => Branch::orderBy('branch_name')->get()]);
    }

    public function update(ChildRequest $request, Child $child): RedirectResponse
    {
        abort_unless($child->user_id === $request->user()->id, 404);
        abort_if($child->archived_at, 409, 'Сначала восстановите профиль ребёнка.');
        $child->update($request->validated());

        return redirect()->route('dashboard')->with('status', 'Данные ребёнка сохранены.');
    }

    public function archive(Request $request, Child $child): RedirectResponse
    {
        abort_unless($child->user_id === $request->user()->id, 404);
        $request->validate(['archived' => ['required', 'boolean']]);
        $child->forceFill(['archived_at' => $request->boolean('archived') ? now() : null])->save();

        return redirect()->route('dashboard')->with('status', $request->boolean('archived') ? 'Профиль ребёнка в архиве.' : 'Профиль ребёнка восстановлен.');
    }
}
