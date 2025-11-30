<?php

namespace App\Http\Controllers\Web;

use App\Http\Controllers\Controller;
use App\Models\Journal;
use Illuminate\Http\Request;

class MemorialController extends Controller
{
    public function __construct()
    {
        $this->middleware('permission:journals.view')->only(['index', 'show']);
        $this->middleware('permission:journals.create')->only(['store']);
        $this->middleware('permission:journals.update')->only(['update']);
        $this->middleware('permission:journals.delete')->only(['destroy']);
    }

    public function index(Request $request)
    {
        $query = Journal::with(['details.account', 'creator', 'attachments', 'debitAccount', 'creditAccount'])
            ->where('source_module', 'memorial');

        if ($request->has('search')) {
            $search = $request->get('search');
            $query->where(function ($q) use ($search) {
                $q->where('reference', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        if ($request->has('date_from')) {
            $query->whereDate('date', '>=', $request->get('date_from'));
        }

        if ($request->has('date_to')) {
            $query->whereDate('date', '<=', $request->get('date_to'));
        }

        $memorials = $query->orderBy('date', 'desc')
                         ->paginate($request->get('per_page', 15));

        return view('memorials.index', compact('memorials'));
    }

    public function show(Journal $memorial)
    {
        if ($memorial->source_module !== 'memorial') {
            abort(404);
        }

        return response()->json([
            'data' => $memorial->load(['details.account', 'creator', 'attachments', 'debitAccount', 'creditAccount'])
        ]);
    }
}