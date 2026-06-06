<?php

namespace App\Http\Controllers;

use App\Ai\Agents\SummaryAgent;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
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
        /** @var array<int, array{id: int, title: ?string, content: ?string, summary: ?string, quizzes: array}> $notes */
        $notes = session('notes', []);

        return view('notes.index', [
            'notes' => $notes,
        ]);
    }

    /**
     * Store a newly created note in session.
     */
    public function store(Request $request): RedirectResponse
    {
        /** @var array<int, array{id: int, title: ?string, content: ?string, summary: ?string, quizzes: array}> $notes */
        $notes = session('notes', []);
        $maxId = (int) (collect($notes)->max('id') ?? 0);

        $notes[] = [
            'id' => $maxId + 1,
            'title' => $request->input('title'),
            'content' => $request->input('content'),
            'summary' => null,
            'quizzes' => [],
        ];

        session([
            'notes' => $notes,
        ]);

        return redirect()->route('notes');
    }

    /**
     * Display the specified note.
     */
    public function show(string|int $id): View
    {
        /** @var array<int, array{id: int, title: ?string, content: ?string, summary: ?string, quizzes: array}> $notes */
        $notes = session('notes', []);
        $note = collect($notes)->firstWhere('id', (int) $id);

        if (! $note) {
            abort(404);
        }

        return view('notes.show', [
            'note' => $note,
        ]);
    }

    /**
     * Serve the uploaded document file for preview.
     */
    public function file(string|int $id): Response
    {
        /** @var array<int, array{id: int, title: ?string, content: ?string, summary: ?string, quizzes: array, file_path?: string}> $notes */
        $notes = session('notes', []);
        $note = collect($notes)->firstWhere('id', (int) $id);

        if (! $note || empty($note['file_path'])) {
            abort(404);
        }

        if (! Storage::exists($note['file_path'])) {
            abort(404);
        }

        return Storage::response($note['file_path']);
    }

    /**
     * Generate an AI summary for the specified note.
     */
    public function summary(string|int $id): RedirectResponse
    {
        /** @var array<int, array{id: int, title: ?string, content: ?string, summary: ?string, quizzes: array, file_path?: string}> $notes */
        $notes = session('notes', []);
        $note = collect($notes)->firstWhere('id', (int) $id);

        if (! $note) {
            abort(404);
        }

        $attachments = [];
        if (! empty($note['file_path'])) {
            $attachments[] = Document::fromStorage($note['file_path']);
        }

        $prompt = ! empty($note['file_path'])
            ? 'Buatlah ringkasan materi dari dokumen terlampir.'
            : $note['content'];

        $summary = SummaryAgent::make()->prompt($prompt, $attachments);

        $notes = collect($notes)->map(function (array $item) use ($id, $summary) {
            if ($item['id'] === (int) $id) {
                $item['summary'] = Str::markdown((string) $summary);
            }

            return $item;
        })->all();

        session([
            'notes' => $notes,
        ]);

        return redirect()->route('notes.show', $id);
    }

    /**
     * Remove the specified note from session.
     */
    public function destroy(string|int $id): RedirectResponse
    {
        /** @var array<int, array{id: int, title: ?string, content: ?string, summary: ?string, quizzes: array, file_path?: string}> $notes */
        $notes = session('notes', []);

        $note = collect($notes)->firstWhere('id', (int) $id);
        if ($note && ! empty($note['file_path'])) {
            Storage::delete($note['file_path']);
        }

        $notes = collect($notes)->reject(function (array $item) use ($id) {
            return $item['id'] === (int) $id;
        })->values()->all();

        session([
            'notes' => $notes,
        ]);

        return redirect()->route('notes');
    }

    /**
     * Update the specified note in session.
     */
    public function update(Request $request, string|int $id): RedirectResponse
    {
        $request->validate([
            'title' => ['required', 'string', 'max:255'],
            'content' => ['nullable', 'string'],
        ]);

        /** @var array<int, array{id: int, title: ?string, content: ?string, summary: ?string, quizzes: array, file_path?: string}> $notes */
        $notes = session('notes', []);
        $noteExists = false;

        $notes = collect($notes)->map(function (array $item) use ($id, $request, &$noteExists) {
            if ($item['id'] === (int) $id) {
                $item['title'] = $request->input('title');
                $item['content'] = $request->input('content');
                $noteExists = true;
            }

            return $item;
        })->all();

        if (! $noteExists) {
            abort(404);
        }

        session([
            'notes' => $notes,
        ]);

        return redirect()->route('notes.show', $id);
    }

    /**
     * Upload a document file and create a note from its content.
     */
    public function upload(Request $request): RedirectResponse
    {
        $request->validate([
            'file' => ['required', 'file', 'mimes:txt,md,pdf', 'max:102400'],
        ]);

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

        /** @var array<int, array{id: int, title: ?string, content: ?string, summary: ?string, quizzes: array, file_path?: string}> $notes */
        $notes = session('notes', []);
        $maxId = (int) (collect($notes)->max('id') ?? 0);

        $notes[] = [
            'id' => $maxId + 1,
            'title' => $originalName,
            'content' => $content,
            'file_path' => $path,
            'summary' => null,
            'quizzes' => [],
        ];

        session([
            'notes' => $notes,
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
