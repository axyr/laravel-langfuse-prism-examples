<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Ai\Agents\StoryTeller;
use App\Console\Commands\Concerns\FormatsExampleOutput;
use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Streaming\Events\TextDeltaEvent;
use Illuminate\Console\Command;

class StreamingCommand extends Command
{
    use FormatsExampleOutput;

    protected $signature = 'example:streaming';

    protected $description = 'Example 4: Streaming with automatic Langfuse tracing';

    public function handle(): int
    {
        $this->header(
            'Example 4: Streaming',
            'Streaming works identically with auto-tracing. The complete text is captured in Langfuse.',
        );

        $prompt = 'Write a short story about a developer who discovers their Laravel application has become sentient.';

        $this->line("  <fg=gray>Prompt: {$prompt}</>");
        $this->newLine();
        $this->output->write('  ');

        $stream = Prism::text()
            ->using('anthropic', 'claude-sonnet-4-6')
            ->withSystemPrompt(StoryTeller::systemPrompt())
            ->withPrompt($prompt)
            ->asStream();

        foreach ($stream as $event) {
            if ($event instanceof TextDeltaEvent) {
                $this->output->write($event->delta);
            }
        }

        $this->newLine();

        Langfuse::flush();

        $this->langfuseReminder();

        return self::SUCCESS;
    }
}
