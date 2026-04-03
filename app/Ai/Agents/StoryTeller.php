<?php

declare(strict_types=1);

namespace App\Ai\Agents;

class StoryTeller
{
    public static function systemPrompt(): string
    {
        return <<<'INSTRUCTIONS'
        You are a creative storyteller. Write engaging, vivid short stories
        based on the given prompt. Keep stories under 200 words. Use descriptive
        language and create compelling narratives with a clear beginning,
        middle, and end.
        INSTRUCTIONS;
    }
}
