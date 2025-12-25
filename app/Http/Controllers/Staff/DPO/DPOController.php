<?php

namespace App\Http\Controllers\Staff\DPO;

use App\Http\Controllers\Controller;
use App\Http\Requests\Staff\DPO\AnonymizeRequest;
use App\Models\Client;
use App\Services\GdprService;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DPOController extends Controller
{
    public function index(Request $request)
    {
        $defaultDate = Carbon::now()->subYears(3)->format('Y-m-d');
        $selectedDate = $request->input('date', $defaultDate);

        $usersCount = Client::where('date_der_connexion', '<', $selectedDate)
            ->count();

        return view('staff.dpo.index', compact('usersCount', 'selectedDate'));
    }

    public function anonymizeClient(AnonymizeRequest $request, GdprService $gdprService)
    {
        $validated = $request->validated();
        $date_threshold = $validated['date_threshold'];

        $anonymizedCount = $gdprService->anonymizeClientsBeforeDate($date_threshold);

        return redirect()
            ->route('dpo.index')
            ->with([
                'success' => "Anonymisation terminée. Nombre de clients anonymisés : $anonymizedCount.",
                'date_threshold' => $date_threshold,
            ]);
    }
}
