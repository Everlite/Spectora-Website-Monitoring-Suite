<?php

namespace App\Jobs;

use App\Mail\MonthlyAgencyReportMail;
use App\Models\User;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Sleep;

class SendMonthlyReportsJob implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Execute the job.
     */
    public function handle(): void
    {
        Log::info('Starting monthly digest job.');

        // Get all users with their domains
        $users = User::with(['domains' => function ($query) {
            $query->select('id', 'user_id', 'uuid', 'url', 'safety_status', 'response_time');
        }])->get();

        foreach ($users as $user) {
            if ($user->domains->isEmpty() || ! $user->email) {
                continue;
            }

            try {
                Log::info("Processing digest for user {$user->email}");

                $total = $user->domains->count();
                $issues = 0;
                $problemDomains = [];

                foreach ($user->domains as $domain) {
                    $hasIssue = false;
                    $reason = '';

                    // Check Safety
                    if ($domain->safety_status && $domain->safety_status !== 'safe') {
                        $hasIssue = true;
                        $reason = 'Security Warning: '.ucfirst($domain->safety_status);
                    }

                    // Check Response Time (Simple check for now, e.g. > 2s)
                    if (! $hasIssue && $domain->response_time > 2.0) {
                        $hasIssue = true;
                        $reason = 'Slow response time: '.$domain->response_time.'s';
                    }

                    if ($hasIssue) {
                        $issues++;
                        if (count($problemDomains) < 5) {
                            $problemDomains[] = [
                                'uuid' => $domain->uuid,
                                'url' => $domain->url,
                                'reason' => $reason,
                            ];
                        }
                    }
                }

                $stats = [
                    'total' => $total,
                    'safe' => $total - $issues,
                    'issues' => $issues,
                    'problem_domains' => $problemDomains,
                ];

                // Send Email
                Mail::to($user->email)->send(
                    new MonthlyAgencyReportMail($user, $stats)
                );

                Log::info("Digest sent to {$user->email}");

                // Sleep to respect mail server limits
                Sleep::for(1)->second();

            } catch (\Exception $e) {
                Log::error("Failed to send digest for user {$user->id}: ".$e->getMessage());
            }
        }
        Log::info('Monthly digest job finished.');
    }
}
