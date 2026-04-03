<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use App\Ai\Tools\Calculator;
use App\Ai\Tools\SearchWeb;

class ResearchAssistant
{
    public static function systemPrompt(): string
    {
        return <<<'INSTRUCTIONS'
        You are a research assistant with access to web search and a calculator.
        Use the search tool to find information and the calculator for any
        mathematical computations. Provide well-researched, accurate answers.
        INSTRUCTIONS;
    }

    public static function tools(): array
    {
        return [
            new SearchWeb(),
            new Calculator(),
        ];
    }
}
