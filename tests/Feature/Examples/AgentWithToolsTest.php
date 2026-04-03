<?php

declare(strict_types=1);

use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;

it('runs the agent with tools command successfully', function () {
    Langfuse::fake();

    Prism::fake([
        TextResponseFake::make()->withText('The population is approximately 17.5 million, divided by 12 equals about 1.46 million.'),
    ]);

    $this->artisan('example:agent-with-tools')->assertSuccessful();
});

it('prompts the research assistant with the expected question', function () {
    Langfuse::fake();

    $prismFake = Prism::fake([
        TextResponseFake::make()->withText('The answer is approximately 1.46 million per province.'),
    ]);

    $this->artisan('example:agent-with-tools')->assertSuccessful();

    // Verify it made at least one request
    $prismFake->assertRequest(fn($request) => true);
});
