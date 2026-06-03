<?php

namespace Tests\Unit;

use App\Models\Domain;
use App\Models\User;
use App\Services\ReportService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ReportServicePdfTest extends TestCase
{
    use RefreshDatabase;

    public function test_generate_pdf_does_not_throw_and_builds_chart_data(): void
    {
        $user = User::factory()->create();
        $domain = Domain::factory()->create(['user_id' => $user->id]);

        $pdf = app(ReportService::class)->generatePdf($domain);

        $output = $pdf->output();
        $this->assertNotEmpty($output);
        $this->assertStringStartsWith('%PDF', $output);
    }
}
