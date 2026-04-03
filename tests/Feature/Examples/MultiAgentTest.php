<?php

declare(strict_types=1);

use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;

it('creates a single trace with spans for both agents', function () {
    $fake = Langfuse::fake();

    Prism::fake([
        TextResponseFake::make()->withText('- LLM observability tracks model performance\n- Costs can be monitored per query'),
        TextResponseFake::make()->withText('LLM observability has become essential for production AI applications.'),
    ]);

    $this->artisan('example:multi-agent')->assertSuccessful();

    $fake->assertTraceCreated('researcher-writer-pipeline')
        ->assertSpanCreated('research-phase')
        ->assertSpanCreated('writing-phase');
});

it('prompts both agents in sequence', function () {
    Langfuse::fake();

    $prismFake = Prism::fake([
        TextResponseFake::make()->withText('- Key finding: observability reduces debugging time'),
        TextResponseFake::make()->withText('An article about LLM observability.'),
    ]);

    $this->artisan('example:multi-agent')->assertSuccessful();

    // Verify it made 2 requests (one for each agent)
    $prismFake->assertCallCount(2);
});
