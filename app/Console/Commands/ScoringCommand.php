<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Ai\Agents\Summarizer;
use App\Console\Commands\Concerns\FormatsExampleOutput;
use Axyr\Langfuse\Dto\ScoreBody;
use Axyr\Langfuse\Dto\TraceBody;
use Axyr\Langfuse\Enums\ScoreDataType;
use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Illuminate\Console\Command;
use Prism\Prism\Facades\Prism;

class ScoringCommand extends Command
{
    use FormatsExampleOutput;

    protected $signature = 'example:scoring';

    protected $description = 'Example 6: Attach quality scores to traces for evaluation';

    public function handle(): int
    {
        $this->header(
            'Example 6: Scoring',
            'Attach quality scores to traces for evaluation in Langfuse.',
        );

        $text = <<<'TEXT'
        Retrieval-Augmented Generation (RAG) is a technique that enhances LLM responses by
        retrieving relevant documents from a knowledge base before generating an answer.
        RAG pipelines typically involve document chunking, embedding generation, vector search,
        optional reranking, and finally LLM generation with the retrieved context. This approach
        reduces hallucinations and allows LLMs to access up-to-date or domain-specific information.
        TEXT;

        // Step 1: Create manual trace with userId and tags
        $trace = Langfuse::trace(new TraceBody(
            name: 'scoring-example',
            userId: 'user-42',
            input: $text,
            tags: ['example', 'scoring', 'summarization'],
        ));
        Langfuse::setCurrentTrace($trace);

        // Step 2: Agent generation nests under the trace
        $this->line('  <fg=gray>Prompting Summarizer agent...</>');

        $response = Prism::text()
            ->using('anthropic', 'claude-sonnet-4-6')
            ->withSystemPrompt(Summarizer::systemPrompt())
            ->withPrompt("Summarize this text concisely: {$text}")
            ->asText();

        $this->newLine();
        $this->line("  <fg=white>{$response->text}</>");

        // Step 3: Attach scores
        $this->newLine();
        $this->line('  <fg=gray>Attaching scores to trace...</>');

        $trace->score(new ScoreBody(
            name: 'relevance',
            value: 0.9,
            dataType: ScoreDataType::NUMERIC,
            comment: 'Summary accurately captures the key points about RAG',
        ));

        $trace->score(new ScoreBody(
            name: 'conciseness',
            value: 0.85,
            dataType: ScoreDataType::NUMERIC,
            comment: 'Summary is concise but could be slightly shorter',
        ));

        $trace->score(new ScoreBody(
            name: 'quality',
            stringValue: 'good',
            dataType: ScoreDataType::CATEGORICAL,
            comment: 'Overall quality assessment of the summary',
        ));

        $this->line('  <fg=white>Scores attached: relevance=0.9, conciseness=0.85, quality=good</>');

        Langfuse::flush();

        $this->langfuseReminder();

        return self::SUCCESS;
    }
}
