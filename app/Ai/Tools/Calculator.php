<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use Prism\Prism\Tool;
use Prism\Prism\ValueObjects\ToolError;
use Prism\Prism\ValueObjects\ToolOutput;

class Calculator extends Tool
{
    public function name(): string
    {
        return 'calculator';
    }

    public function description(): string
    {
        return 'Evaluate a mathematical expression and return the result.';
    }

    public function parameters(): array
    {
        return [
            'type' => 'object',
            'properties' => [
                'expression' => [
                    'type' => 'string',
                    'description' => 'The mathematical expression to evaluate, e.g. "2 + 2" or "sqrt(16)"',
                ],
            ],
            'required' => ['expression'],
        ];
    }

    public function handle(mixed ...$args): ToolOutput|ToolError|string
    {
        $expression = $args['expression'] ?? '';

        // Simple safe evaluation for basic math
        $sanitized = preg_replace('/[^0-9+\-*\/().%\s]/', '', $expression);

        if ($sanitized === '' || $sanitized === null) {
            return (string) json_encode(['error' => 'Invalid expression', 'expression' => $expression]);
        }

        try {
            $result = eval("return (float)({$sanitized});");

            return (string) json_encode([
                'expression' => $expression,
                'result' => $result,
            ]);
        } catch (\Throwable) {
            return (string) json_encode([
                'expression' => $expression,
                'error' => 'Could not evaluate expression',
            ]);
        }
    }
}
