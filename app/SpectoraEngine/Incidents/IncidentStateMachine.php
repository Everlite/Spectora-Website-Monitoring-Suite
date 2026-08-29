<?php

namespace App\SpectoraEngine\Incidents;

use App\Models\Domain;
use App\Models\MonitoredUrl;
use Illuminate\Support\Facades\Log;

class IncidentStateMachine
{
    /**
     * Evaluates the check outcome and transitions the target state.
     *
     * @param  Domain  $domain  Parent domain
     * @param  MonitoredUrl|null  $monitoredUrl  Sub-URL if applicable
     * @param  array<string>  $issues  Detected issues/errors during check
     * @param  string|null  $checkedUrl  Target URL string
     * @return IncidentState Current state after transition
     */
    public function transition(
        Domain $domain,
        ?MonitoredUrl $monitoredUrl,
        array $issues,
        ?string $checkedUrl = null
    ): IncidentState {
        $target = $monitoredUrl ?? $domain;
        $hasIssues = ! empty($issues);
        $alreadyNotified = (bool) ($target->notify_sent ?? false);

        if ($hasIssues) {
            // Target is failing
            if (! $alreadyNotified) {
                // First confirmed incident trigger -> Dispatch outage alert
                Log::warning("IncidentStateMachine: Outage detected on {$domain->url} (".($checkedUrl ?? $domain->url).")");
                AlertDispatcher::dispatchDowntime($domain, $issues, $checkedUrl);
                $target->update(['notify_sent' => true]);
            }

            return IncidentState::DOWN;
        }

        // Target is healthy
        if ($alreadyNotified) {
            // Target was previously down and is now healthy -> Trigger Recovery
            Log::info("IncidentStateMachine: Recovery detected on {$domain->url} (".($checkedUrl ?? $domain->url).")");
            AlertDispatcher::dispatchRecovery($domain, $checkedUrl);
            $target->update(['notify_sent' => false]);

            return IncidentState::RECOVERED;
        }

        return IncidentState::HEALTHY;
    }
}
