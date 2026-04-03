<?php

declare(strict_types=1);

namespace App\Console\Commands;

use App\Ai\Agents\PromptDrivenAgent;
use App\Console\Commands\Concerns\FormatsExampleOutput;
use Axyr\Langfuse\Dto\CreatePromptBody;
use Axyr\Langfuse\Dto\TraceBody;
use Axyr\Langfuse\LangfuseFacade as Langfuse;
use Illuminate\Console\Command;
use Prism\Prism\Facades\Prism;

class PromptManagementCommand extends Command
{
    use FormatsExampleOutput;

    protected $signature = 'example:prompt-management';

    protected $description = 'Example 5: Langfuse prompt management linked to generations';

    public function handle(): int
    {
        $this->header(
            'Example 5: Prompt Management',
            'Langfuse prompts as agent instructions, linked to generations.',
        );

        // Step 1: Create a prompt in Langfuse
        $this->line('  <fg=gray>Creating prompt in Langfuse...</>');

        Langfuse::createPrompt(new CreatePromptBody(
            name: 'topic-explainer',
            type: 'text',
            prompt: 'You are a {{expertise_level}} expert. Explain {{topic}} in a way that a {{audience}} can understand. Focus on practical applications and real-world examples.',
            labels: ['production'],
        ));

        // Step 2: Fetch the prompt back
        $this->line('  <fg=gray>Fetching prompt from Langfuse...</>');

        $prompt = Langfuse::prompt(
            name: 'topic-explainer',
            fallback: 'You are an expert. Explain {{topic}} clearly.',
        );

        // Step 3: Compile with variables
        /** @var string $compiled */
        $compiled = $prompt->compile([
            'expertise_level' => 'senior',
            'topic' => 'LLM observability',
            'audience' => 'Laravel developer',
        ]);

        $this->line("  <fg=gray>Compiled prompt: {$compiled}</>");

        // Step 4: Create manual trace
        $trace = Langfuse::trace(new TraceBody(
            name: 'prompt-management-example',
            input: $compiled,
            metadata: [
                'prompt_name' => $prompt->getName(),
                'prompt_version' => $prompt->getVersion(),
            ],
        ));
        Langfuse::setCurrentTrace($trace);

        // Step 5: Prompt agent - generation nests under our trace
        $this->line('  <fg=gray>Prompting agent with managed prompt...</>');

        $agent = new PromptDrivenAgent($compiled);
        $response = Prism::text()
            ->using('anthropic', 'claude-sonnet-4-6')
            ->withSystemPrompt($agent->systemPrompt())
            ->withPrompt('Explain LLM observability and why it matters for production applications.')
            ->asText();

        // Step 6: Update trace with output
        $trace->update(new TraceBody(
            output: $response->text,
        ));

        $this->newLine();
        $this->line("  <fg=white>{$response->text}</>");

        Langfuse::flush();

        $this->langfuseReminder();

        return self::SUCCESS;
    }
}
