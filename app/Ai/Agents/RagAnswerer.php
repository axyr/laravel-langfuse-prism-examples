<?php

declare(strict_types=1);

namespace App\Ai\Agents;

class RagAnswerer
{
    public function __construct(
        private readonly string $context,
    ) {}

    public function systemPrompt(): string
    {
        return <<<INSTRUCTIONS
        You are a knowledgeable assistant that answers questions based strictly
        on the provided context. If the answer is not in the context, say so.
        Be precise and cite relevant parts of the context.

        Context:
        {$this->context}
        INSTRUCTIONS;
    }
}
