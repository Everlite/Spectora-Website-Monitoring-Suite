<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use App\Services\ReportService;
use Illuminate\Support\Facades\Log;

class ReportController extends Controller
{
    public function download(Domain $domain, ReportService $reportService)
    {
        Log::info('PDF Download requested for domain: '.$domain->id);

        $this->authorize('view', $domain);

        try {
            $pdf = $reportService->generatePdf($domain);
            $filename = 'report-'.str_replace(['/', '\\', ':', '*'], '-', $domain->url).'-'.now()->format('Y-m').'.pdf';

            return $pdf->download($filename);
        } catch (\Exception $e) {
            Log::error('PDF Generation failed: '.$e->getMessage());
            Log::error($e->getTraceAsString());

            return redirect()->back()->with('error', 'PDF could not be generated. Please try again later.');
        }
    }
}
