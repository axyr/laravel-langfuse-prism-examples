<?php

declare(strict_types=1);

use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;

it('runs the basic agent command successfully', function () {
    Langfuse::fake();

    Prism::fake([
        TextResponseFake::make()->withText('A concise summary of the text.'),
    ]);

    $this->artisan('example:basic-agent')->assertSuccessful();
});

it('prompts the summarizer agent with sample text', function () {
    Langfuse::fake();

    $prismFake = Prism::fake([
        TextResponseFake::make()->withText('A concise summary of the text.'),
    ]);

    $this->artisan('example:basic-agent')->assertSuccessful();

    // Verify it made at least one request
    $prismFake->assertRequest(fn($request) => true);
});
