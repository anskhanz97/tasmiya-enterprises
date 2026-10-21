<?php

namespace App\Http\Controllers;

use App\Models\Lead;
use App\Models\LeadDetail;
use App\Models\User;
use App\Services\LeadWorkflow;
use App\Support\LeadPhone;
use Carbon\CarbonImmutable;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class LeadController extends Controller
{
    public function __construct(private LeadWorkflow $workflow) {}

    public function index()
    {
        $this->authorize('viewAny', Lead::class);

        return view('leads.index', [
            'users' => User::where('is_active', true)->whereIn('role', ['team_member', 'admin', 'super_admin'])->orderBy('name')->get(['id', 'name']),
            'statuses' => LeadDetail::STATUSES, 'activityTypes' => LeadDetail::ACTIVITY_TYPES,
            'isAdmin' => auth()->user()->isAdmin(), 'canAdd' => true, 'canEdit' => true,
        ]);
    }

    private function query(Request $request)
    {
        $this->authorize('viewAny', Lead::class);
        $request->validate([
            'scope' => 'nullable|in:mine,all,unassigned', 'assigned_to' => 'nullable|integer',
            'status' => ['nullable', Rule::in(array_keys(LeadDetail::STATUSES))],
            'overdue' => 'nullable|boolean',
        ]);
        $query = Lead::visibleTo($request->user());
        if ($request->input('scope') === 'mine') {
            $query->where('assigned_to', $request->user()->id);
        }
        if ($request->input('scope') === 'unassigned') {
            abort_unless($request->user()->isAdmin(), 403);
            $query->needsAssignment();
        }
        if ($request->filled('assigned_to')) {
            abort_unless($request->user()->isAdmin() || (int) $request->assigned_to === $request->user()->id, 403);
            $query->where('assigned_to', $request->integer('assigned_to'));
        }
        if ($request->filled('status')) {
            $query->where('lead_status', $request->status);
        }
        if ($request->boolean('overdue')) {
            $query->overdue();
        }

        return $query;
    }

    public function data(Request $request)
    {
        $query = $this->query($request);
        $total = (clone $query)->count();
        $term = mb_substr((string) $request->input('search.value', ''), 0, 200);
        if ($term !== '') {
            $query->where(fn ($q) => $q->where('party_name', 'like', '%'.$term.'%')->orWhere('phone', 'like', '%'.$term.'%'));
        }
        $filtered = (clone $query)->count();
        $start = max(0, (int) $request->input('start', 0));
        $length = max(1, min(100, (int) $request->input('length', 10)));
        $column = [1 => 'party_name', 2 => 'phone'][(int) $request->input('order.0.column', 0)] ?? 'id';
        $direction = $request->input('order.0.dir') === 'asc' && $column !== 'id' ? 'asc' : 'desc';
        $rows = $query->withInquiryContext()->with(['assignee', 'openLeadFollowup'])->orderBy($column, $direction)->offset($start)->limit($length)->get();

        return response()->json([
            'draw' => (int) $request->input('draw', 0), 'recordsTotal' => $total, 'recordsFiltered' => $filtered,
            'data' => $rows->values()->map(function (Lead $lead, int $index) use ($start) {
                $open = $lead->openLeadFollowup;
                $schedule = $open ? $open->follow_up_date->format('d M Y').($open->follow_up_time ? ' '.CarbonImmutable::parse($open->follow_up_time)->format('h:i A') : '') : null;
                $owner = $lead->assignee;

                return [
                    'DT_RowIndex' => $start + $index + 1, 'party_name' => e($lead->party_name).($lead->previous_inquiry_count
                        ? '<br><span class="badge badge-light inquiry-context">'.e(app(\App\Services\LeadInquiryHistory::class)->classification($lead)).'</span>' : ''), 'phone' => e($lead->phone ?? ''),
                    'lead_status' => '<span class="badge '.LeadDetail::statusClass($lead->lead_status).'">'.e($lead->lead_status).'</span>',
                    'assigned_to_name' => e($owner ? $owner->name.(! $owner->is_active ? ' (inactive)' : '') : 'Unassigned'),
                    'next_follow_up' => $schedule ? '<span class="'.($open->isOverdue() ? 'text-danger font-weight-bold' : '').'">'.e($schedule).'</span>' : '<span class="text-muted">Not scheduled</span>',
                    'action' => '<button type="button" class="btn btn-info btn-sm view-lead" data-id="'.$lead->id.'"><i class="fa fa-eye"></i> View</button>',
                ];
            }),
        ]);
    }

    public function events(Request $request)
    {
        $request->validate(['start' => 'required|date', 'end' => 'required|date|after:start']);
        $leads = $this->query($request)->select('id');
        $events = LeadDetail::open()->whereIn('lead_id', $leads)->with(['lead', 'assignee'])
            ->where('follow_up_date', '>=', CarbonImmutable::parse($request->start)->toDateString())
            ->where('follow_up_date', '<', CarbonImmutable::parse($request->end)->toDateString())->get();

        return response()->json($events->map(function (LeadDetail $detail) {
            $date = $detail->follow_up_date->format('Y-m-d');

            return [
                'id' => 'followup-'.$detail->id, 'title' => $detail->lead->party_name.' • '.$detail->lead->phone,
                'start' => $detail->follow_up_time ? CarbonImmutable::parse($date.' '.$detail->follow_up_time, config('leads.timezone'))->toIso8601String() : $date,
                'allDay' => ! $detail->follow_up_time,
                'className' => array_values(array_filter([LeadDetail::statusClass($detail->lead->lead_status), $detail->isOverdue() ? 'lead-overdue' : null])),
                'party_id' => $detail->lead_id, 'lead_status' => $detail->lead->lead_status,
                'assigned_to' => $detail->lead->assigned_to, 'assigned_to_name' => $detail->assignee?->name,
                'is_overdue' => $detail->isOverdue(),
            ];
        }));
    }

    public function duplicates(Request $request)
    {
        $this->authorize('create', Lead::class);
        $request->validate(['phone' => 'required|string|max:40']);

        return response()->json($this->matching($request)->get()->map(fn ($lead) => [
            'id' => $lead->id, 'party_name' => $lead->party_name, 'phone' => $lead->phone,
            'role' => $lead->isClosed() ? $lead->lead_status.' — reopen before reuse' : 'Lead', 'version' => $lead->version,
        ]));
    }

    private function matching(Request $request)
    {
        return Lead::visibleTo($request->user())->where('phone_normalized', LeadPhone::normalize($request->phone));
    }

    public function store(Request $request)
    {
        $this->authorize('create', Lead::class);
        $request->merge(['phone' => LeadPhone::normalize($request->phone)]);
        $data = $request->validate(array_merge($this->contactRules(), $this->scheduleRules(true), [
            'assigned_to' => 'nullable|integer', 'remarks' => 'nullable|string|max:2000',
            'reuse_party_id' => 'nullable|integer', 'version' => 'required_with:reuse_party_id|integer|min:1',
            'force_duplicate' => 'nullable|boolean',
        ]));
        if (! empty($data['reuse_party_id'])) {
            $lead = Lead::findOrFail($data['reuse_party_id']);
            $this->authorize('update', $lead);
            $lead = $this->workflow->activity($lead, array_merge($data, ['lead_status' => $lead->lead_status, 'activity_type' => 'Note', 'reschedule_only' => true]));
        } else {
            if (! $request->boolean('force_duplicate') && $this->matching($request)->exists()) {
                return response()->json(['message' => 'A contact with this phone already exists.', 'duplicates' => $this->duplicates($request)->getData()], 409);
            }
            $lead = $this->workflow->create($data);
        }

        return response()->json(['message' => 'Lead saved successfully.', 'party_id' => $lead->id, 'version' => $lead->version]);
    }

    public function show(Lead $lead)
    {
        $this->authorize('view', $lead);
        $lead->load(['contact', 'assignee', 'division', 'openLeadFollowup', 'leadDetails' => fn ($q) => $q->with(['assignee', 'creator'])->orderByDesc('id'), 'inquiries' => fn ($q) => $q->oldest('id')]);

        return response()->json([
            // Keep the source UI's party-shaped payload; its id identifies a Tasmiya lead case.
            'party' => ['id' => $lead->id, 'party_name' => $lead->party_name, 'phone' => $lead->phone,
                'party_email' => $lead->party_email, 'address' => $lead->address,
                'role' => $lead->contact->converted_at ? 'Customer' : 'Lead'],
            'version' => $lead->version, 'current_status' => $lead->lead_status, 'assigned_to' => $lead->assigned_to,
            'assigned_to_name' => $lead->assignee?->name, 'division_name' => $lead->division?->name,
            'closed' => $lead->isClosed(), 'source' => $lead->source,
            'inquiry_classification' => app(\App\Services\LeadInquiryHistory::class)->classification($lead),
            'previous_inquiry_count' => (int) $lead->previous_inquiry_count,
            'open_follow_up' => $lead->openLeadFollowup ? $this->detail($lead->openLeadFollowup) : null,
            'history' => $lead->leadDetails->map(fn ($detail) => $this->detail($detail))->values(),
            'inquiries' => $lead->inquiries->map(fn ($item) => $item->only(['id', 'name', 'email', 'phone', 'subject', 'message', 'source', 'created_at', 'email_delivery_status', 'whatsapp_delivery_status'])),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function previousInquiries(Request $request, Lead $lead)
    {
        $this->authorize('view', $lead);
        $cases = app(\App\Services\LeadInquiryHistory::class)->previous($lead, $request->user())
            ->withCount('inquiries')->with(['inquiries' => fn ($q) => $q->oldest('id')->limit(1)])->paginate(10);

        return response()->json([
            'data' => $cases->map(fn ($case) => [
                'id' => $case->id, 'status' => $case->lead_status,
                'created_at' => $case->created_at->timezone(config('leads.timezone'))->format('d M Y, g:i A'),
                'subject' => $case->inquiries->first()?->subject ?: 'Case #'.$case->id,
                'message' => \Illuminate\Support\Str::limit($case->inquiries->first()?->message ?? '', 500),
                'inquiry_count' => $case->inquiries_count,
            ]),
            'has_more' => $cases->hasMorePages(), 'page' => $cases->currentPage(), 'total' => $cases->total(),
        ])->header('Cache-Control', 'private, no-store');
    }

    public function update(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $request->merge(['phone' => LeadPhone::normalize($request->phone)]);
        $data = $request->validate(array_merge($this->contactRules(), ['version' => 'required|integer|min:1']));
        $updated = $this->workflow->updateContact($lead, $data);

        return response()->json(['message' => 'Lead contact updated successfully.', 'version' => $updated->version]);
    }

    public function followUp(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $data = $request->validate(array_merge($this->scheduleRules(), [
            'version' => 'required|integer|min:1',
            'lead_status' => ['required', Rule::in(array_diff(array_keys(LeadDetail::STATUSES), ['Converted']))],
            'activity_type' => ['required', Rule::in(['Call', 'Meeting', 'WhatsApp', 'Email', 'Note'])],
            'assigned_to' => 'nullable|integer', 'remarks' => 'nullable|string|max:2000', 'reschedule_only' => 'nullable|boolean',
        ]));
        if ($data['lead_status'] === 'Lost' && ! empty($data['follow_up_date'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['follow_up_date' => 'A Lost case cannot have an open follow-up.']);
        }
        $ownerId = array_key_exists('assigned_to', $data) ? $data['assigned_to'] : $lead->assigned_to;
        if (! empty($data['follow_up_date']) && ! $ownerId) {
            throw \Illuminate\Validation\ValidationException::withMessages(['assigned_to' => 'Assign a specialist before scheduling a follow-up.']);
        }
        if ($request->boolean('reschedule_only') && empty($data['follow_up_date'])) {
            throw \Illuminate\Validation\ValidationException::withMessages(['follow_up_date' => 'Choose the replacement follow-up date when rescheduling.']);
        }
        if (isset($data['assigned_to'])) {
            $data['assigned_to'] = (int) $data['assigned_to'];
        }
        $updated = $this->workflow->activity($lead, $data);

        return response()->json(['message' => 'Lead activity saved successfully.', 'version' => $updated->version]);
    }

    public function convert(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $data = $request->validate(['version' => 'required|integer|min:1', 'remarks' => 'nullable|string|max:2000']);
        $updated = $this->workflow->convert($lead, $data);

        return response()->json(['message' => 'Lead converted to Customer successfully.', 'version' => $updated->version]);
    }

    public function reopen(Request $request, Lead $lead)
    {
        $this->authorize('update', $lead);
        $data = $request->validate(['version' => 'required|integer|min:1', 'reason' => 'required|string|min:3|max:2000']);
        $updated = $this->workflow->reopen($lead, $data);

        return response()->json(['message' => 'Case reopened. You can now add an activity or schedule a follow-up.', 'version' => $updated->version]);
    }

    private function contactRules(): array
    {
        return ['party_name' => 'required|string|max:100', 'phone' => ['required', 'string', 'regex:/^\+[1-9]\d{1,14}$/'],
            'party_email' => 'nullable|email|max:255', 'address' => 'nullable|string|max:200'];
    }

    private function scheduleRules(bool $required = false): array
    {
        $today = CarbonImmutable::now(config('leads.timezone'))->toDateString();

        return ['follow_up_date' => ($required ? 'required' : 'nullable').'|date_format:Y-m-d|after_or_equal:'.$today.'|required_with:follow_up_time',
            'follow_up_time' => 'nullable|date_format:H:i'];
    }

    private function detail(LeadDetail $detail): array
    {
        return ['id' => $detail->id, 'lead_status' => $detail->lead_status, 'status_class' => LeadDetail::statusClass($detail->lead_status),
            'activity_type' => $detail->activity_type, 'follow_up_date' => $detail->follow_up_date?->format('Y-m-d'),
            'follow_up_time' => $detail->follow_up_time ? substr($detail->follow_up_time, 0, 5) : null,
            'assigned_to' => $detail->assigned_to, 'assigned_to_name' => $detail->assigned_to_name ?: $detail->assignee?->name,
            'remarks' => $detail->remarks, 'completed_at' => $detail->completed_at?->toIso8601String(),
            'cancelled_at' => $detail->cancelled_at?->toIso8601String(),
            'created_at' => $detail->created_at?->timezone(config('leads.timezone'))->format('Y-m-d H:i:s'),
            'created_by_name' => $detail->creator?->name, 'is_overdue' => $detail->isOverdue()];
    }
}
