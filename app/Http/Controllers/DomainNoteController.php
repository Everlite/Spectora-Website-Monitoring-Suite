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

        return response()->json(
            $domain->notes()->with('user:id,first_name,last_name,email')->orderBy('created_at', 'desc')->get()
        );
    }

    public function store(Request $request, Domain $domain)
    {
        $this->authorize('update', $domain);

        $request->validate([
            'content' => 'required|string|max:10000',
        ]);

        $note = $domain->notes()->create([
            'content' => $request->content,
            'user_id' => $request->user()->id,
        ]);

        $note->load('user:id,first_name,last_name,email');

        return response()->json($note);
    }

    public function update(Request $request, DomainNote $note)
    {
        $this->authorize('update', $note->domain);

        $request->validate([
            'content' => 'required|string|max:10000',
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
