<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class RunNightAudit extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'audit:night {date? : Optional date to run audit for (YYYY-MM-DD)}';

    protected $description = 'Run the night audit to post daily charges and taxes';

    public function handle(\App\Services\NightAuditService $nightAuditService)
    {
        $date = $this->argument('date');
        $this->info("Starting Night Audit" . ($date ? " for $date" : "") . "...");

        try {
            $results = $nightAuditService->performAudit($date);

            $this->table(
            ['Category', 'Count'],
            [
                ['Bookings Processed', $results['bookings_processed']],
                ['Charges Posted', $results['charges_posted']],
                ['Taxes Posted', $results['taxes_posted']],
            ]
            );

            if (!empty($results['errors'])) {
                $this->error("Audit encountered " . count($results['errors']) . " errors:");
                foreach ($results['errors'] as $error) {
                    $this->line(" - $error");
                }
            }
            else {
                $this->info("Night Audit completed successfully.");
            }
        }
        catch (\Exception $e) {
            $this->error("Night Audit failed: " . $e->getMessage());
            return 1;
        }

        return 0;
    }
}
