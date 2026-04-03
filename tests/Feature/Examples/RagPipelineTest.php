<?php

declare(strict_types=1);

use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;

it('creates a nested trace hierarchy for the RAG pipeline', function () {
    $fake = Langfuse::fake();

    Prism::fake([
        TextResponseFake::make()->withText('Langfuse provides observability with auto-tracing and zero configuration.'),
    ]);

    $this->artisan('example:rag-pipeline')->assertSuccessful();

    $fake->assertTraceCreated('rag-pipeline')
        ->assertSpanCreated('retrieval')
        ->assertSpanCreated('vector-search')
        ->assertSpanCreated('reranking')
        ->assertGenerationCreated('embed-query')
        ->assertEventCreated('context-assembled')
        ->assertScoreCreated('answer-relevance');
});
