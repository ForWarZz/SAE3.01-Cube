<?php

namespace App\Http\Controllers\Staff\SAV;

use App\Http\Controllers\Controller;
use App\Http\Requests\OrderReturnUpdateStateRequest;
use App\Models\OrderReturnRequest;
use App\Models\OrderReturnState;

class SAVController extends Controller
{
    public function index()
    {
        $returns = OrderReturnRequest::with(['order.client', 'state', 'lines.orderLine'])
            ->orderBy('date_demande', 'desc')
            ->paginate(20);

        return view('staff.sav.index', [
            'returns' => $returns,
        ]);
    }

    public function show(OrderReturnRequest $returnRequest)
    {
        $returnRequest->load([
            'order.client',
            'order.billingAddress.city',
            'order.deliveryAddress.city',
            'order.shop.city',
            'state',
            'lines.orderLine.reference.article',
            'lines.orderLine.reference.bikeReference.color',
            'lines.orderLine.reference.bikeReference.ebike.battery',
            'lines.orderLine.size',
        ]);

        $availableStates = OrderReturnState::all();

        return view('staff.sav.show', [
            'returnRequest' => $returnRequest,
            'availableStates' => $availableStates,
        ]);
    }

    public function updateState(OrderReturnUpdateStateRequest $request, OrderReturnRequest $returnRequest)
    {
        $returnRequest->update([
            'id_etat_retour' => $request->input('id_etat_retour'),
        ]);

        return redirect()->route('sav.show', $returnRequest)
            ->with('success', 'État de la demande de retour mis à jour');
    }
}
