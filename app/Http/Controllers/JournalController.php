<?php

namespace App\Http\Controllers;

use App\Models\Journal;
use Illuminate\Http\Request;

class JournalController extends Controller
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
        $query = Journal::with(['details.account', 'createdBy']);

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

        $journals = $query->orderBy('date', 'desc')
                         ->paginate($request->get('per_page', 15));

        return response()->json([
            'data' => $journals->items(),
            'meta' => [
                'current_page' => $journals->currentPage(),
                'last_page' => $journals->lastPage(),
                'per_page' => $journals->perPage(),
                'total' => $journals->total(),
            ]
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'description' => 'required|string',
            'details' => 'required|array|min:2',
            'details.*.account_id' => 'required|exists:accounts,id',
            'details.*.debit' => 'required|numeric|min:0',
            'details.*.credit' => 'required|numeric|min:0',
            'details.*.description' => 'nullable|string'
        ]);

        // Validate double entry
        $totalDebit = collect($validated['details'])->sum('debit');
        $totalCredit = collect($validated['details'])->sum('credit');

        if ($totalDebit != $totalCredit) {
            return response()->json([
                'message' => 'Total debit must equal total credit',
                'errors' => ['details' => ['Total debit must equal total credit']]
            ], 422);
        }

        $journal = Journal::create([
            'date' => $validated['date'],
            'reference' => $validated['reference'],
            'description' => $validated['description'],
            'status' => 'draft',
            'created_by' => auth()->id()
        ]);

        foreach ($validated['details'] as $detail) {
            $journal->details()->create($detail);
        }

        return response()->json([
            'message' => 'Journal created successfully',
            'data' => $journal->load('details.account')
        ], 201);
    }

    public function show(Journal $journal)
    {
        return response()->json([
            'data' => $journal->load('details.account', 'createdBy')
        ]);
    }

    public function update(Request $request, Journal $journal)
    {
        if ($journal->status === 'posted') {
            return response()->json([
                'message' => 'Cannot update posted journal'
            ], 422);
        }

        $validated = $request->validate([
            'date' => 'required|date',
            'reference' => 'required|string|max:255',
            'description' => 'required|string',
            'details' => 'required|array|min:2',
            'details.*.account_id' => 'required|exists:accounts,id',
            'details.*.debit' => 'required|numeric|min:0',
            'details.*.credit' => 'required|numeric|min:0',
            'details.*.description' => 'nullable|string'
        ]);

        // Validate double entry
        $totalDebit = collect($validated['details'])->sum('debit');
        $totalCredit = collect($validated['details'])->sum('credit');

        if ($totalDebit != $totalCredit) {
            return response()->json([
                'message' => 'Total debit must equal total credit',
                'errors' => ['details' => ['Total debit must equal total credit']]
            ], 422);
        }

        $journal->update([
            'date' => $validated['date'],
            'reference' => $validated['reference'],
            'description' => $validated['description']
        ]);

        // Delete existing details and create new ones
        $journal->details()->delete();
        foreach ($validated['details'] as $detail) {
            $journal->details()->create($detail);
        }

        return response()->json([
            'message' => 'Journal updated successfully',
            'data' => $journal->load('details.account')
        ]);
    }

    public function destroy(Journal $journal)
    {
        if ($journal->status === 'posted') {
            return response()->json([
                'message' => 'Cannot delete posted journal'
            ], 422);
        }

        $journal->delete();

        return response()->json([
            'message' => 'Journal deleted successfully'
        ]);
    }

    public function post(Journal $journal)
    {
        if ($journal->status === 'posted') {
            return response()->json([
                'message' => 'Journal is already posted'
            ], 422);
        }

        $journal->update(['status' => 'posted']);

        return response()->json([
            'message' => 'Journal posted successfully',
            'data' => $journal
        ]);
    }

    public function unpost(Journal $journal)
    {
        if ($journal->status === 'draft') {
            return response()->json([
                'message' => 'Journal is already in draft status'
            ], 422);
        }

        $journal->update(['status' => 'draft']);

        return response()->json([
            'message' => 'Journal unposted successfully',
            'data' => $journal
        ]);
    }
}