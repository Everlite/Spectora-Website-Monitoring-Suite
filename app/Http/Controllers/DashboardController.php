<?php

namespace App\Http\Controllers;

use App\Models\Domain;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $domainsQuery = Domain::query()
            ->withCount(['analyticsVisits as visitors_today' => function ($query) {
                $query->whereDate('created_at', now());
            }]);

        if (! $user->is_admin) {
            $domainsQuery->where('user_id', $user->id);
        }

        $domains = $domainsQuery->orderBy('url')->get();

        return view('dashboard', compact('domains'));
    }
}
