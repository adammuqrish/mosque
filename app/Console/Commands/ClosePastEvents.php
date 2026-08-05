<?php

namespace App\Console\Commands;

use App\Models\Event;
use Illuminate\Console\Command;

class ClosePastEvents extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'events:close-past';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Close events that have passed their event date';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $this->info('Checking for past events to close...');

        $count = Event::where('status', '!=', 'closed')
            ->where('status', '!=', 'cancelled')
            ->where('event_date', '<', now())
            ->update(['status' => 'closed']);

        if ($count === 0) {
            $this->info('No past events found that need closing.');
            return Command::SUCCESS;
        }

        $this->info("Successfully closed {$count} past event(s).");

        return Command::SUCCESS;
    }
}
