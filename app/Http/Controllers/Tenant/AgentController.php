<?php

namespace App\Http\Controllers\Tenant;

use App\Enums\CommissionType;
use App\Http\Controllers\Controller;
use App\Models\Agent;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

class AgentController extends Controller
{
    public function index(Request $request)
    {
        $agents = Agent::withCount('bookings')
            ->withSum('commissions as total_commission', 'amount')
            ->when($request->search, fn ($q, $s) => $q->where('name', 'like', "%{$s}%"))
            ->latest()
            ->paginate(15)
            ->withQueryString();

        return view('tenant.agents.index', compact('agents'));
    }

    public function create()
    {
        return view('tenant.agents.create', [
            'commissionTypes' => CommissionType::options(),
        ]);
    }

    public function store(Request $request)
    {
        $data = $this->validateData($request);
        $data['code'] = $data['code'] ?: strtoupper(Str::random(6));

        Agent::create($data);

        return redirect()->route('app.agents.index')
            ->with('success', 'Agent ditambahkan.');
    }

    public function show(Agent $agent)
    {
        $agent->load(['bookings.package', 'commissions.booking']);

        return view('tenant.agents.show', compact('agent'));
    }

    public function edit(Agent $agent)
    {
        return view('tenant.agents.edit', [
            'agent' => $agent,
            'commissionTypes' => CommissionType::options(),
        ]);
    }

    public function update(Request $request, Agent $agent)
    {
        $agent->update($this->validateData($request, $agent));

        return redirect()->route('app.agents.index')
            ->with('success', 'Agent diperbarui.');
    }

    public function destroy(Agent $agent)
    {
        $agent->delete();

        return redirect()->route('app.agents.index')
            ->with('success', 'Agent dihapus.');
    }

    protected function validateData(Request $request, ?Agent $agent = null): array
    {
        $data = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'code' => ['nullable', 'string', 'max:50', Rule::unique('agents', 'code')
                ->where('tenant_id', current_tenant_id())->ignore($agent)],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email'],
            'address' => ['nullable', 'string'],
            'commission_type' => ['required', 'in:flat,percentage'],
            'commission_value' => ['required', 'numeric', 'min:0'],
            'bank_name' => ['nullable', 'string', 'max:100'],
            'bank_account_number' => ['nullable', 'string', 'max:100'],
            'is_active' => ['nullable', 'boolean'],
        ]);

        $data['is_active'] = $request->boolean('is_active');

        return $data;
    }
}
