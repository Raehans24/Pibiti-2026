<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SummaryAgent;
use App\Models\Note;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Laravel\Ai\Files\Document;
use Smalot\PdfParser\Parser;
use Symfony\Component\HttpFoundation\Response;

class NotesController extends Controller
{
    /**
     * Display a listing of the notes.
     */
    public function index(): View
    {
        /** @var User $user */
        $user = Auth::user();
        $notes = $user->notes()->latest()->get();

        return view('notes.index', [
            'notes' => $notes,
        ]);
    }

    /**
     * Store a newly created note in database.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();

        $user->notes()->create([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('notes');
    }

    /**
     * Display the specified note.
     */
    public function show(string|int $id): View
    {
        /** @var User $user */
        $user = Auth::user();
        $note = $user->notes()->findOrFail($id);

        return view('notes.show', [
            'note' => $note,
        ]);
    }

    /**
     * Serve the uploaded document file for preview.
     */
    public function file(string|int $id): Response
    {
        /** @var User $user */
        $user = Auth::user();
        $note = $user->notes()->findOrFail($id);

        if (empty($note->file_path)) {
            abort(404);
        }

        if (! Storage::exists($note->file_path)) {
            abort(404);
        }

        return Storage::response($note->file_path);
    }

    /**
     * Generate an AI summary for the specified note.
     */
    public function summary(string|int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $note = $user->notes()->findOrFail($id);

        $attachments = [];
        if (! empty($note->file_path)) {
            $attachments[] = Document::fromStorage($note->file_path);
        }

        $prompt = ! empty($note->file_path)
            ? 'Buatlah ringkasan materi dari dokumen terlampir.'
            : $note->content;

        $summary = SummaryAgent::make()->prompt($prompt, $attachments);

        $note->update([
            'summary' => Str::markdown((string) $summary),
        ]);

        return redirect()->route('notes.show', $id);
    }

    /**
     * Remove the specified note from database.
     */
    public function destroy(string|int $id): RedirectResponse
    {
        /** @var User $user */
        $user = Auth::user();
        $note = $user->notes()->findOrFail($id);

        if (! empty($note->file_path)) {
            Storage::delete($note->file_path);
        }

        $note->delete();

        return redirect()->route('notes');
    }

    /**
     * Update the specified note in database.
     */
    public function update(Request $request, string|int $id): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        /** @var User $user */
        $user = Auth::user();
        $note = $user->notes()->findOrFail($id);

        $note->update([
            'title' => $request->input('title'),
            'content' => $request->input('content'),
        ]);

        return redirect()->route('notes.show', $id);
    }

    /**
     * Upload a document file and create a note from its content.
     */
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'extensions:txt,md,pdf', 'max:102400'],
        ]);

        /** @var User $user */
        $user = Auth::user();

        $file = $request->file('file');
        $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
        $extension = strtolower($file->getClientOriginalExtension());

        $content = match ($extension) {
            'txt', 'md' => file_get_contents($file->getRealPath()),
            'pdf' => $this->extractPdfText($file->getRealPath()),
            default => '',
        };

        if (empty(trim($content)) && $extension !== 'pdf') {
            return redirect()->route('notes')->with('error', 'Tidak dapat mengekstrak teks dari file yang diupload.');
        }

        $path = $file->storeAs('private', $originalName.'.'.$extension);

        $user->notes()->create([
            'title' => $originalName,
            'content' => $content,
            'file_path' => $path,
        ]);

        return redirect()->route('notes');
    }

    /**
     * Extract text content from a PDF file.
     */
    private function extractPdfText(string $filePath): string
    {
        if (! class_exists(Parser::class)) {
            return '';
        }

        $parser = new Parser;

        return $parser->parseFile($filePath)->getText();
    }
}
