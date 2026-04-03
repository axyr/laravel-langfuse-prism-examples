<?php

declare(strict_types=1);

namespace App\Ai\Tools;

use Prism\Prism\Schema\StringSchema;
use Prism\Prism\Tool;
use Prism\Prism\ValueObjects\ToolError;
use Prism\Prism\ValueObjects\ToolOutput;

class SearchWeb extends Tool
{
    public function name(): string
    {
        return 'search_web';
    }

    public function description(): string
    {
        return 'Search the web for information on a given query. Returns relevant search results.';
    }

    /**
     * @return array<string, \Prism\Prism\Contracts\Schema>
     */
    public function parameters(): array
    {
        return [
            'query' => new StringSchema('query', 'The search query'),
        ];
    }

    public function handle(mixed ...$args): ToolOutput|ToolError|string
    {
        $query = $args['query'] ?? '';

        // Simulated search results for demonstration purposes
        return json_encode([
            'results' => [
                [
                    'title' => "Result 1 for: {$query}",
                    'snippet' => "This is a relevant finding about {$query}. Studies show significant developments in this area.",
                    'url' => 'https://example.com/result-1',
                ],
                [
                    'title' => "Result 2 for: {$query}",
                    'snippet' => "Recent research on {$query} indicates new trends and patterns worth exploring.",
                    'url' => 'https://example.com/result-2',
                ],
            ],
        ], JSON_THROW_ON_ERROR);
    }
}
