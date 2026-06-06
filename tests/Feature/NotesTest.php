<?php

use App\Ai\Agents\SummaryAgent;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;

beforeEach(function () {
    session([
        'is_login' => true,
        'username' => 'admin',
    ]);
});

test('user can view notes page', function () {
    $response = $this->get(route('notes'));

    $response->assertStatus(200);
});

test('user can store a new note', function () {
    $response = $this->post(route('notes.store'), [
        'title' => 'My Test Title',
        'content' => 'My Test Content',
    ]);

    $response->assertRedirect(route('notes'));
    $this->assertCount(1, session('notes'));
    $note = session('notes')[0];
    $this->assertEquals('My Test Title', $note['title']);
    $this->assertEquals('My Test Content', $note['content']);
});

test('user can upload a text file as a note', function () {
    Storage::fake();

    $file = UploadedFile::fake()->create('lecture.txt', 100, 'text/plain');
    file_put_contents($file->getRealPath(), 'Extracted text content from file.');

    $response = $this->post(route('notes.upload'), [
        'file' => $file,
    ]);

    $response->assertRedirect(route('notes'));
    $this->assertCount(1, session('notes'));
    $note = session('notes')[0];
    $this->assertEquals('lecture', $note['title']);
    $this->assertEquals('Extracted text content from file.', $note['content']);
    $this->assertNotEmpty($note['file_path']);
    Storage::assertExists($note['file_path']);
});

test('user can summarize a note with an uploaded file using AI', function () {
    Storage::fake();

    SummaryAgent::fake([
        'Ini ringkasan dokumen terlampir.',
    ]);

    $filePath = 'files/test_doc.txt';
    Storage::put($filePath, 'Document file content.');

    session(['notes' => [
        [
            'id' => 1,
            'title' => 'Test File Note',
            'content' => 'Document file content.',
            'file_path' => $filePath,
            'summary' => null,
            'quizzes' => [],
        ],
    ]]);

    $response = $this->post(route('notes.summary', 1));

    $response->assertRedirect(route('notes.show', 1));
    $note = session('notes')[0];
    $this->assertStringContainsString('Ini ringkasan dokumen terlampir.', $note['summary']);
});

test('user can delete a note and its file is removed from storage', function () {
    Storage::fake();

    $filePath = 'files/delete_me.txt';
    Storage::put($filePath, 'Delete me content.');

    session(['notes' => [
        [
            'id' => 1,
            'title' => 'Delete Me Note',
            'content' => 'Delete me content.',
            'file_path' => $filePath,
            'summary' => null,
            'quizzes' => [],
        ],
    ]]);

    Storage::assertExists($filePath);

    $response = $this->delete(route('notes.destroy', 1));

    $response->assertRedirect(route('notes'));
    $this->assertCount(0, session('notes'));
    Storage::assertMissing($filePath);
});

test('user can preview/download the uploaded file', function () {
    Storage::fake();

    $filePath = 'files/preview.pdf';
    Storage::put($filePath, 'PDF file content.');

    session(['notes' => [
        [
            'id' => 1,
            'title' => 'Test PDF Note',
            'content' => '',
            'file_path' => $filePath,
            'summary' => null,
            'quizzes' => [],
        ],
    ]]);

    $response = $this->get(route('notes.file', 1));

    $response->assertStatus(200);
    $this->assertEquals('PDF file content.', $response->streamedContent());
});

test('user can update a note', function () {
    session(['notes' => [
        [
            'id' => 1,
            'title' => 'Old Title',
            'content' => 'Old Content',
            'summary' => null,
            'quizzes' => [],
        ],
    ]]);

    $response = $this->put(route('notes.update', 1), [
        'title' => 'New Title',
        'content' => 'New Content',
    ]);

    $response->assertRedirect(route('notes.show', 1));
    $note = session('notes')[0];
    $this->assertEquals('New Title', $note['title']);
    $this->assertEquals('New Content', $note['content']);
});
