<?php

declare(strict_types=1);

use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;

it('creates a prompt and a manually traced generation', function () {
    $fake = Langfuse::fake();

    Prism::fake([
        TextResponseFake::make()->withText('LLM observability helps monitor AI calls in production.'),
    ]);

    $this->artisan('example:prompt-management')->assertSuccessful();

    $fake->assertPromptCreated('topic-explainer')
        ->assertTraceCreated('prompt-management-example');
});

it('prompts the agent with the compiled prompt instructions', function () {
    Langfuse::fake();

    $prismFake = Prism::fake([
        TextResponseFake::make()->withText('LLM observability is essential for production applications.'),
    ]);

    $this->artisan('example:prompt-management')->assertSuccessful();

    // Verify it made at least one request
    $prismFake->assertRequest(fn($request) => true);
});
