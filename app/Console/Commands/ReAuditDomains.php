<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Domain;
use App\Jobs\PerformSpectoraAudit;
use App\Jobs\CheckUrlJob;

class ReAuditDomains extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'spectora:re-audit';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Re-run all domain audits to update results to English';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting re-audit for all domains...');
        
        $domains = Domain::all();
        $count = $domains->count();
        
        $bar = $this->output->createProgressBar($count);
        $bar->start();

        foreach ($domains as $domain) {
            // Dispatch both jobs to ensure safety_details and last_pagespeed_details are updated
            CheckUrlJob::dispatch($domain);
            PerformSpectoraAudit::dispatch($domain);
            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info('Dispatched audits for ' . $count . ' domains successfully.');
    }
}
