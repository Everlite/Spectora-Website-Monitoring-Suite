<?php

namespace Tests\Unit;

use App\Services\DomainAlertService;
use PHPUnit\Framework\TestCase;

class DomainAlertServiceTest extends TestCase
{
    public function test_sanitize_issues_hides_exception_details(): void
    {
        $sanitized = DomainAlertService::sanitizeIssuesForNotification([
            '❌ Unreachable (HTTP 503)',
            '❌ Check failed: Connection refused at /var/www/internal',
        ]);

        $this->assertSame('❌ Unreachable (HTTP 503)', $sanitized[0]);
        $this->assertSame('❌ Check failed: The site could not be reached.', $sanitized[1]);
    }

    public function test_sanitize_issues_leaves_other_messages_unchanged(): void
    {
        $issue = '❌ Required keyword missing: uptime';

        $this->assertSame(
            [$issue],
            DomainAlertService::sanitizeIssuesForNotification([$issue])
        );
    }
}
