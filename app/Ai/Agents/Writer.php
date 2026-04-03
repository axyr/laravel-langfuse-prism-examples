<?php

declare(strict_types=1);

namespace App\Ai\Agents;

class Writer
{
    public function __construct(
        private readonly string $researchNotes,
    ) {}

    public function systemPrompt(): string
    {
        return <<<INSTRUCTIONS
        You are a skilled article writer. Using the research notes provided below,
        write a clear, engaging article. Structure it with an introduction,
        body paragraphs, and conclusion. Keep it concise but informative.

        Research notes:
        {$this->researchNotes}
        INSTRUCTIONS;
    }
}
