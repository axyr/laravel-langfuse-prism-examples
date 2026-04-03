<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Ai\Agents\Summarizer;
use App\Console\Commands\Concerns\FormatsExampleOutput;
use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Illuminate\Console\Command;

class BasicAgentCommand extends Command
{
    use FormatsExampleOutput;

    protected $signature = 'example:basic-agent';

    protected $description = 'Example 1: Basic agent with automatic Langfuse tracing';

    public function handle(): int
    {
        $this->header(
            'Example 1: Basic Agent',
            'Auto-tracing with zero Langfuse code. Just enable LANGFUSE_PRISM_ENABLED=true.',
        );

        $text = <<<'TEXT'
        Laravel is a web application framework with expressive, elegant syntax. It provides
        a robust set of tools and an architectural pattern that helps developers build
        modern, full-stack web applications. Laravel includes features like an ORM called
        Eloquent, a templating engine called Blade, built-in authentication and authorization,
        queuing, real-time event broadcasting, and comprehensive testing support. Created by
        Taylor Otwell, Laravel has become one of the most popular PHP frameworks, known for
        its developer-friendly approach and rich ecosystem of first-party packages.
        TEXT;

        $this->line('  <fg=gray>Prompting Summarizer agent...</>');

        $response = Prism::text()
            ->using('anthropic', 'claude-sonnet-4-6')
            ->withSystemPrompt(Summarizer::systemPrompt())
            ->withPrompt($text)
            ->asText();

        $this->newLine();
        $this->line("  <fg=white>{$response->text}</>");

        Langfuse::flush();

        $this->langfuseReminder();

        return self::SUCCESS;
    }
}
