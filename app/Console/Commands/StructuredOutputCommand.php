<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Ai\Agents\SentimentAnalyzer;
use App\Console\Commands\Concerns\FormatsExampleOutput;
use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Illuminate\Console\Command;
use Prism\Prism\Facades\Prism;

class StructuredOutputCommand extends Command
{
    use FormatsExampleOutput;

    protected $signature = 'example:structured-output';

    protected $description = 'Example 3: Structured JSON output visible in Langfuse generation';

    public function handle(): int
    {
        $this->header(
            'Example 3: Structured Output',
            'Structured JSON output appears in the Langfuse generation.',
        );

        $reviews = [
            'This product exceeded my expectations! The build quality is outstanding and delivery was incredibly fast. Highly recommended.',
            'Terrible experience. The item arrived broken and customer support was completely unhelpful. I want a refund.',
            'It works as described. Nothing special but gets the job done. Shipping took a bit longer than expected.',
        ];

        foreach ($reviews as $index => $review) {
            $number = $index + 1;
            $this->line("  <fg=gray>Review {$number}: {$review}</>");

            $response = Prism::structured()
                ->using('anthropic', 'claude-sonnet-4-6')
                ->withSystemPrompt(SentimentAnalyzer::systemPrompt())
                ->withSchema(SentimentAnalyzer::schema())
                ->withPrompt("Analyze the sentiment of this review: {$review}")
                ->generate();

            $this->line('  <fg=white>Result: ' . json_encode($response->structured, JSON_THROW_ON_ERROR | JSON_PRETTY_PRINT) . '</>');
            $this->newLine();
        }

        Langfuse::flush();

        $this->langfuseReminder();

        return self::SUCCESS;
    }
}
