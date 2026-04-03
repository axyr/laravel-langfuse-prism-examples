<?php

declare(strict_types=1);

namespace App\Ai\Agents;

class PromptDrivenAgent
{
    public function __construct(
        private readonly string $compiledPrompt,
    ) {}

    public function systemPrompt(): string
    {
        return $this->compiledPrompt;
    }
}
