<?php

declare(strict_types=1);

use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;

it('creates a trace with scores attached', function () {
    $fake = Langfuse::fake();

    Prism::fake([
        TextResponseFake::make()->withText('RAG enhances LLM responses by retrieving relevant documents.'),
    ]);

    $this->artisan('example:scoring')->assertSuccessful();

    $fake->assertTraceCreated('scoring-example')
        ->assertScoreCreated('relevance')
        ->assertScoreCreated('conciseness')
        ->assertScoreCreated('quality');
});
