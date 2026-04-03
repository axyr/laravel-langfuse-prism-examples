<?php

declare(strict_types=1);

namespace App\Ai\Agents;

class Tutor
{
    private array $conversationHistory = [];

    public static function systemPrompt(): string
    {
        return <<<'INSTRUCTIONS'
        You are a patient, knowledgeable tutor. Explain concepts clearly and
        build on previous messages in the conversation. Ask follow-up questions
        to check understanding. Adapt your explanations based on the student's
        level of knowledge shown in prior exchanges.
        INSTRUCTIONS;
    }

    public function addToHistory(string $role, string $content): void
    {
        $this->conversationHistory[] = ['role' => $role, 'content' => $content];
    }

    public function getHistory(): array
    {
        return $this->conversationHistory;
    }
}
