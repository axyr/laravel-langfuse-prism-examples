<?php

declare(strict_types=1);

namespace App\Ai\Agents;

use Prism\Prism\Contracts\Schema;
use Prism\Prism\Schema\ArraySchema;
use Prism\Prism\Schema\NumberSchema;
use Prism\Prism\Schema\ObjectSchema;
use Prism\Prism\Schema\StringSchema;

class SentimentAnalyzer
{
    public static function systemPrompt(): string
    {
        return <<<'INSTRUCTIONS'
        You are a sentiment analysis expert. Analyze the given text and return
        structured data about its sentiment, confidence level, key phrases,
        and a brief summary. Be precise and consistent in your analysis.
        INSTRUCTIONS;
    }

    public static function schema(): Schema
    {
        return new ObjectSchema(
            name: 'sentiment_analysis',
            description: 'Sentiment analysis result',
            properties: [
                new StringSchema(
                    name: 'sentiment',
                    description: 'The overall sentiment: positive, negative, or neutral',
                ),
                new NumberSchema(
                    name: 'confidence',
                    description: 'Confidence score between 0 and 1',
                ),
                new ArraySchema(
                    name: 'key_phrases',
                    description: 'Array of key phrases from the text',
                    items: new StringSchema('phrase', ''),
                ),
                new StringSchema(
                    name: 'summary',
                    description: 'Brief summary of the sentiment analysis',
                ),
            ],
            requiredFields: ['sentiment', 'confidence', 'key_phrases', 'summary'],
        );
    }
}
