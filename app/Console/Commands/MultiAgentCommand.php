<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Ai\Agents\Researcher;
use App\Ai\Agents\Writer;
use App\Console\Commands\Concerns\FormatsExampleOutput;
use Axyr\Langfuse\Dto\SpanBody;
use Axyr\Langfuse\Dto\TraceBody;
use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Illuminate\Console\Command;

class MultiAgentCommand extends Command
{
    use FormatsExampleOutput;

    protected $signature = 'example:multi-agent';

    protected $description = 'Example 8: Multiple agents sharing a single Langfuse trace';

    public function handle(): int
    {
        $this->header(
            'Example 8: Multi-Agent Pipeline',
            'Multiple agents share a single trace. Each agent auto-nests under the current trace.',
        );

        $topic = 'The impact of LLM observability on production AI applications';

        // Step 1: Create the shared trace
        $trace = Langfuse::trace(new TraceBody(
            name: 'researcher-writer-pipeline',
            input: $topic,
            tags: ['multi-agent', 'pipeline'],
        ));
        Langfuse::setCurrentTrace($trace);

        // Step 2: Research phase
        $this->line('  <fg=gray>Phase 1: Researching...</>');

        $researchSpan = $trace->span(new SpanBody(
            name: 'research-phase',
            input: $topic,
        ));

        $researchResult = Prism::text()
            ->using('anthropic', 'claude-sonnet-4-6')
            ->withSystemPrompt(Researcher::systemPrompt())
            ->withPrompt("Research the following topic thoroughly: {$topic}")
            ->asText();

        $researchSpan->end(output: $researchResult->text);

        $this->line('  <fg=white>Research complete.</>');

        // Step 3: Writing phase
        $this->newLine();
        $this->line('  <fg=gray>Phase 2: Writing article...</>');

        $writingSpan = $trace->span(new SpanBody(
            name: 'writing-phase',
            input: $researchResult->text,
        ));

        $writer = new Writer($researchResult->text);
        $article = Prism::text()
            ->using('anthropic', 'claude-sonnet-4-6')
            ->withSystemPrompt($writer->systemPrompt())
            ->withPrompt("Write a concise article about: {$topic}")
            ->asText();

        $writingSpan->end(output: $article->text);

        // Step 4: Update trace with final output
        $trace->update(new TraceBody(output: $article->text));

        $this->newLine();
        $this->line("  <fg=white>{$article->text}</>");

        Langfuse::flush();

        $this->langfuseReminder();

        return self::SUCCESS;
    }
}
