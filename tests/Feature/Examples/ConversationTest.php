<?php

declare(strict_types=1);

use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\TextResponseFake;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates traces grouped by session for a multi-turn conversation', function () {
    $fake = Langfuse::fake();

    Prism::fake([
        TextResponseFake::make()->withText('Dependency injection is a design pattern where dependencies are provided to a class.'),
        TextResponseFake::make()->withText('In Laravel, type-hint dependencies in constructors and the service container resolves them.'),
        TextResponseFake::make()->withText('DI makes testing easier because you can swap real implementations with mocks.'),
    ]);

    $this->artisan('example:conversation')->assertSuccessful();

    $fake->assertTraceCreated('conversation-turn-1')
        ->assertTraceCreated('conversation-turn-2')
        ->assertTraceCreated('conversation-turn-3');
});

it('prompts the tutor with three conversation turns', function () {
    Langfuse::fake();

    $prismFake = Prism::fake([
        TextResponseFake::make()->withText('DI provides dependencies from outside.'),
        TextResponseFake::make()->withText('Laravel uses the service container for this.'),
        TextResponseFake::make()->withText('In tests, you can inject fakes instead.'),
    ]);

    $this->artisan('example:conversation')->assertSuccessful();

    // Verify it made 3 requests (one for each turn)
    $prismFake->assertCallCount(3);
});
