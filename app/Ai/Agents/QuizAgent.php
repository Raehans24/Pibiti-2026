<?php

namespace App\Ai\Agents;

use Illuminate\Contracts\JsonSchema\JsonSchema;
use Laravel\Ai\Contracts\Agent;
use Laravel\Ai\Contracts\Conversational;
use Laravel\Ai\Contracts\HasStructuredOutput;
use Laravel\Ai\Contracts\HasTools;
use Laravel\Ai\Contracts\Tool;
use Laravel\Ai\Messages\Message;
use Laravel\Ai\Promptable;
use Stringable;

class QuizAgent implements Agent, Conversational, HasStructuredOutput, HasTools
{
    use Promptable;

    /**
     * Get the instructions that the agent should follow.
     */
    public function instructions(): Stringable|string
    {
        return "Kamu adalah pembuat soal pembelajaran.
                Buat 5 soal pilihan ganda berdasarkan materi yang diberikan.
                Kembalikan response dalam format JSON yang valid dan sesuai dengan schema yang diminta.
                
                Untuk field answer/jawaban, wajib berisi karakter alphabet saja, yaitu salah satu dari: A, B, C, atau D.
                Jangan pernah mengisi field answer dengan teks panjang atau nama field seperti 'option_a'.";
    }

    /**
     * Get the list of messages comprising the conversation so far.
     *
     * @return Message[]
     */
    public function messages(): iterable
    {
        return [];
    }

    /**
     * Get the tools available to the agent.
     *
     * @return Tool[]
     */
    public function tools(): iterable
    {
        return [];
    }

    /**
     * Get the agent's structured output schema definition.
     */
    public function schema(JsonSchema $schema): array
    {
        return [
            'question' => $schema->array()->items(
                $schema->object(
                    fn ($schema) => [
                        'question' => $schema->string()->required(),
                        'option_a' => $schema->string()->required(),
                        'option_b' => $schema->string()->required(),
                        'option_c' => $schema->string()->required(),
                        'option_d' => $schema->string()->required(),
                        'answer' => $schema->string()->enum(['A', 'B', 'C', 'D'])->required(),
                    ]
                )
            )->required(),
        ];
    }
}
