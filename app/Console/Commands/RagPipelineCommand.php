<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Ai\Agents\RagAnswerer;
use App\Console\Commands\Concerns\FormatsExampleOutput;
use Axyr\Langfuse\Dto\EventBody;
use Axyr\Langfuse\Dto\GenerationBody;
use Axyr\Langfuse\Dto\ScoreBody;
use Axyr\Langfuse\Dto\SpanBody;
use Axyr\Langfuse\Dto\TraceBody;
use Axyr\Langfuse\Dto\Usage;
use Axyr\Langfuse\Enums\ScoreDataType;
use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Illuminate\Console\Command;
use Prism\Prism\Facades\Prism;

class RagPipelineCommand extends Command
{
    use FormatsExampleOutput;

    protected $signature = 'example:rag-pipeline';

    protected $description = 'Example 7: Rich nested trace hierarchy for a RAG pipeline';

    public function handle(): int
    {
        $this->header(
            'Example 7: RAG Pipeline',
            'Rich nested trace hierarchy showing retrieval, reranking, and generation.',
        );

        $question = 'What are the benefits of using Langfuse with Laravel?';

        // Create the top-level trace
        $trace = Langfuse::trace(new TraceBody(
            name: 'rag-pipeline',
            input: $question,
            metadata: ['pipeline_version' => '1.0'],
        ));
        Langfuse::setCurrentTrace($trace);

        // --- Retrieval span ---
        $this->line('  <fg=gray>Step 1: Retrieval...</>');

        $retrievalSpan = $trace->span(new SpanBody(
            name: 'retrieval',
            input: $question,
        ));

        // Embedding generation (simulated)
        $embedGeneration = $retrievalSpan->generation(new GenerationBody(
            name: 'embed-query',
            model: 'text-embedding-3-small',
            input: $question,
        ));
        $embedGeneration->end(
            output: '[0.023, -0.041, 0.087, ...]',
            usage: new Usage(input: 12, total: 12, unit: 'TOKENS'),
        );

        // Vector search (simulated)
        $vectorSpan = $retrievalSpan->span(new SpanBody(
            name: 'vector-search',
            input: ['query_embedding' => '[0.023, -0.041, ...]', 'top_k' => 5],
        ));

        $documents = [
            'Langfuse provides observability for LLM applications, tracking traces, generations, and costs.',
            'Laravel AI SDK offers a unified interface for working with multiple AI providers.',
            'The laravel-langfuse package auto-instruments Prism calls with zero configuration.',
        ];

        $vectorSpan->end(output: $documents);
        $retrievalSpan->end(output: ['document_count' => count($documents)]);

        // --- Reranking span ---
        $this->line('  <fg=gray>Step 2: Reranking...</>');

        $rerankSpan = $trace->span(new SpanBody(
            name: 'reranking',
            input: ['query' => $question, 'documents' => $documents],
        ));
        $rerankSpan->end(output: ['reranked_count' => count($documents), 'top_score' => 0.95]);

        // --- Context assembled event ---
        $context = implode("\n\n", $documents);

        $trace->event(new EventBody(
            name: 'context-assembled',
            input: ['document_count' => count($documents)],
            output: $context,
        ));

        // --- Answer generation (auto-traced via Prism subscriber) ---
        $this->line('  <fg=gray>Step 3: Generating answer...</>');

        $answerer = new RagAnswerer($context);
        $response = Prism::text()
            ->using('anthropic', 'claude-sonnet-4-6')
            ->withSystemPrompt($answerer->systemPrompt())
            ->withPrompt($question)
            ->asText();

        $this->newLine();
        $this->line("  <fg=white>{$response->text}</>");

        // --- Score the answer ---
        $trace->score(new ScoreBody(
            name: 'answer-relevance',
            value: 0.92,
            dataType: ScoreDataType::NUMERIC,
            comment: 'Answer directly addresses the question using provided context',
        ));

        $trace->update(new TraceBody(output: $response->text));

        Langfuse::flush();

        $this->langfuseReminder();

        return self::SUCCESS;
    }
}
