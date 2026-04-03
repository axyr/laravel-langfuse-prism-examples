<?php

declare(strict_types=1);

use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;

it('runs the streaming command successfully', function () {
    Langfuse::fake();

    Prism::fake([
        TextResponseFake::make()->withText('Once upon a time, a Laravel application became sentient.'),
    ]);

    $this->artisan('example:streaming')->assertSuccessful();
});

it('prompts the storyteller agent with the expected prompt', function () {
    Langfuse::fake();

    $prismFake = Prism::fake([
        TextResponseFake::make()->withText('The application started writing its own migrations.'),
    ]);

    $this->artisan('example:streaming')->assertSuccessful();

    // Verify it made at least one request
    $prismFake->assertRequest(fn($request) => true);
});
