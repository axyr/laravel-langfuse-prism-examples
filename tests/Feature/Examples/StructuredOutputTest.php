<?php

declare(strict_types=1);

use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Prism\Prism\Facades\Prism;
use Prism\Prism\Testing\StructuredResponseFake;

it('runs the structured output command successfully', function () {
    Langfuse::fake();

    Prism::fake([
        StructuredResponseFake::make()->withStructured(['sentiment' => 'positive', 'confidence' => 0.95, 'key_phrases' => ['exceeded expectations'], 'summary' => 'Very positive review']),
        StructuredResponseFake::make()->withStructured(['sentiment' => 'negative', 'confidence' => 0.92, 'key_phrases' => ['terrible experience'], 'summary' => 'Very negative review']),
        StructuredResponseFake::make()->withStructured(['sentiment' => 'neutral', 'confidence' => 0.78, 'key_phrases' => ['works as described'], 'summary' => 'Neutral review']),
    ]);

    $this->artisan('example:structured-output')->assertSuccessful();
});

it('prompts the sentiment analyzer for each review', function () {
    Langfuse::fake();

    $prismFake = Prism::fake([
        StructuredResponseFake::make()->withStructured(['sentiment' => 'positive', 'confidence' => 0.95, 'key_phrases' => ['exceeded expectations'], 'summary' => 'Very positive']),
        StructuredResponseFake::make()->withStructured(['sentiment' => 'negative', 'confidence' => 0.92, 'key_phrases' => ['terrible'], 'summary' => 'Very negative']),
        StructuredResponseFake::make()->withStructured(['sentiment' => 'neutral', 'confidence' => 0.78, 'key_phrases' => ['works'], 'summary' => 'Neutral']),
    ]);

    $this->artisan('example:structured-output')->assertSuccessful();

    // Verify it made 3 requests (one for each review)
    $prismFake->assertCallCount(3);
});
