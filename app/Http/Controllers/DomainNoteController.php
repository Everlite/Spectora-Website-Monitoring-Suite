<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Models\DomainNote;
use Illuminate\Http\Request;

class DomainNoteController extends Controller
{
    public function index(Domain $domain)
    {
        $this->authorize('view', $domain);

        return response()->json($domain->notes()->orderBy('created_at', 'desc')->get());
    }

    public function store(Request $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        $request->validate([
            'content' => 'required|string',
        ]);

        $note = $domain->notes()->create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
        ]);

        return response()->json($note);
    }

    public function update(Request $request, DomainNote $note)
    {
        $this->authorize('update', $note->domain);

        $request->validate([
            'content' => 'required|string',
        ]);

        $note->update([
            'content' => $request->content,
        ]);

        return response()->json($note);
    }

    public function destroy(DomainNote $note)
    {
        $this->authorize('update', $note->domain);

        $note->delete();

        return response()->json(['message' => 'Note deleted']);
    }
}
