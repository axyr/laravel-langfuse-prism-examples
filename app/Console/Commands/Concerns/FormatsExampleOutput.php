<?php

declare(strict_types=1);

namespace App\Console\Commands\Concerns;

trait FormatsExampleOutput
{
    protected function header(string $title, string $description): void
    {
        $this->newLine();
        $this->components->info($title);
        $this->line("  {$description}");
        $this->newLine();
    }

    protected function langfuseReminder(): void
    {
        $this->newLine();
        $this->components->info('Check your Langfuse dashboard to see the trace for this example.');
    }
}
