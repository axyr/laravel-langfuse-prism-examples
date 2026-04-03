<?php

declare(strict_types=1);

namespace App\Ai\Agents;

class Summarizer
{
    public static function systemPrompt(): string
    {
        return <<<'INSTRUCTIONS'
        You are a concise summarizer. Given any text, produce a clear summary
        that captures the key points in 2-3 sentences. Focus on the most
        important information and omit unnecessary details.
        INSTRUCTIONS;
    }
}
