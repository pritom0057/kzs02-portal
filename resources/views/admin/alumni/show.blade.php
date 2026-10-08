@extends('layouts.admin')
@section('title', $alumnus->name)
@section('heading', $alumnus->name)

@section('content')

<div style="margin-bottom:20px">
  <a href="{{ route('admin.alumni.index') }}" style="font-size:13px;color:var(--muted);text-decoration:none">
    &larr; Back to Alumni
  </a>
</div>

<div style="display:grid;grid-template-columns:1fr 2fr;gap:16px;align-items:start">

  {{-- Left column --}}
  <div style="display:grid;gap:12px">

    {{-- Profile card --}}
    <div class="panel">
      <div style="display:flex;align-items:center;gap:12px;margin-bottom:14px;padding-bottom:14px;border-bottom:1px solid var(--line)">
        <div style="width:52px;height:52px;border-radius:50%;overflow:hidden;background:var(--tint);flex-shrink:0;display:flex;align-items:center;justify-content:center;font-size:20px;font-weight:700;color:var(--red-700)">
          @if($alumnus->photo_url)
            <img src="{{ Storage::url($alumnus->photo_url) }}" style="width:100%;height:100%;object-fit:cover" alt="">
          @else
            {{ strtoupper(substr($alumnus->name, 0, 1)) }}
          @endif
        </div>
        <div>
          <p style="font-size:15px;font-weight:700;color:var(--ink)">{{ $alumnus->name }}</p>
          <p style="font-size:12px;color:var(--muted)">Roll: {{ $alumnus->roll_number }}</p>
        </div>
      </div>
      <dl style="display:grid;gap:10px;font-size:13px">
        @foreach([
          ['Email', $alumnus->email],
          ['Phone', $alumnus->phone ?: '—'],
          ['Profession', $alumnus->current_profession ?: '—'],
          ['Location', $alumnus->current_location ?: '—'],
          ['Joined', $alumnus->created_at->format('d M Y')],
        ] as [$label, $val])
        <div style="display:flex;gap:10px">
          <dt style="width:80px;color:var(--muted);flex-shrink:0">{{ $label }}</dt>
          <dd style="color:var(--ink);word-break:break-all">{{ $val }}</dd>
        </div>
        @endforeach
        <div style="display:flex;gap:10px;align-items:center">
          <dt style="width:80px;color:var(--muted);flex-shrink:0">Status</dt>
          <dd>
            <span class="badge {{ $alumnus->status === 'verified' ? 'badge-green' : ($alumnus->status === 'rejected' ? 'badge-red' : 'badge-yellow') }}">
              {{ ucfirst($alumnus->status) }}
            </span>
          </dd>
        </div>
      </dl>
    </div>

    {{-- Actions --}}
    <div class="panel" style="display:grid;gap:8px">
      <p style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:4px">Actions</p>
      @if($alumnus->status !== 'verified')
      <form method="POST" action="{{ route('admin.alumni.verify', $alumnus) }}">
        @csrf
        <button class="btn btn-primary" style="width:100%;justify-content:center">✓ Verify Account</button>
      </form>
      @endif
      @if($alumnus->status !== 'rejected')
      <form method="POST" action="{{ route('admin.alumni.reject', $alumnus) }}">
        @csrf
        <button onclick="return confirm('Reject this alumni?')"
          class="btn btn-sm" style="width:100%;justify-content:center;background:#fee2e2;color:#991b1b;border-color:#fca5a5">
          ✕ Reject Account
        </button>
      </form>
      @endif
    </div>

    {{-- Event registration --}}
    @if($alumnus->eventRegistration)
    @php
      $reg = $alumnus->eventRegistration;
      $balanceDue = max(0, $reg->total_amount - $reg->paid_amount);
      $overpaid   = $reg->paid_amount > $reg->total_amount;
      $fullyPaid  = $reg->paid_amount >= $reg->total_amount && $reg->payment_status === 'paid';
    @endphp
    <div class="panel">
      <p style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px">Event Registration</p>
      <dl style="display:grid;gap:10px;font-size:13px;margin-bottom:14px">
        @foreach([
          ['T-Shirt', $reg->tshirt_size, ''],
          ['Guests', $reg->guest_count, ''],
          ['Dietary', $reg->dietary_notes ?: '—', ''],
          ['Total', '৳'.number_format($reg->total_amount), 'font-weight:700'],
          ['Paid', '৳'.number_format($reg->paid_amount), $reg->paid_amount > 0 ? 'color:#166534;font-weight:700' : 'color:var(--muted)'],
          ['Method', ucfirst($reg->payment_method ?: '—'), ''],
        ] as [$label, $val, $style])
        <div style="display:flex;gap:10px">
          <dt style="width:70px;color:var(--muted);flex-shrink:0">{{ $label }}</dt>
          <dd style="{{ $style }}">{{ $val }}</dd>
        </div>
        @endforeach
        @if($balanceDue > 0)
        <div style="display:flex;gap:10px">
          <dt style="width:70px;color:var(--muted);flex-shrink:0">Due</dt>
          <dd style="color:#991b1b;font-weight:700">৳{{ number_format($balanceDue) }}</dd>
        </div>
        @endif
        @if($overpaid)
        <div style="display:flex;gap:10px">
          <dt style="width:70px;color:var(--muted);flex-shrink:0">Overpaid</dt>
          <dd style="color:#b45309;font-weight:700">৳{{ number_format($reg->paid_amount - $reg->total_amount) }} credit</dd>
        </div>
        @endif
        @if($reg->payment_reference)
        <div style="display:flex;gap:10px">
          <dt style="width:70px;color:var(--muted);flex-shrink:0">Ref</dt>
          <dd style="word-break:break-all">{{ $reg->payment_reference }}</dd>
        </div>
        @endif
        <div style="display:flex;gap:10px;align-items:center">
          <dt style="width:70px;color:var(--muted);flex-shrink:0">Status</dt>
          <dd>
            <span class="badge {{ $reg->payment_status === 'paid' ? 'badge-green' : ($reg->payment_status === 'pending' ? 'badge-blue' : 'badge-yellow') }}">
              {{ ucfirst($reg->payment_status ?? 'unpaid') }}
            </span>
            @if($reg->payment_status === 'paid' && $balanceDue > 0)
            <span class="badge badge-red" style="margin-left:4px">Additional Required</span>
            @endif
          </dd>
        </div>
      </dl>

      {{-- Attendees breakdown --}}
      @php
        try {
          $bringSpouse  = (bool) $reg->getAttribute('bring_spouse');
          $driverIncl   = (bool) $reg->getAttribute('driver_included');
          $childrenList = is_array($reg->children_details) ? $reg->children_details : [];
          $guestsList   = is_array($reg->guests_details)   ? $reg->guests_details   : [];
          $spouseName   = $reg->getAttribute('spouse_name') ?: '—';
          $spouseNameBn = $reg->getAttribute('spouse_name_bn');
          $hasAttendees = $bringSpouse || count($childrenList) || count($guestsList) || $driverIncl;
        } catch (\Throwable $e) {
          $hasAttendees = false; $bringSpouse = false; $driverIncl = false;
          $childrenList = []; $guestsList = []; $spouseName = ''; $spouseNameBn = '';
        }
      @endphp
      @if($hasAttendees)
      <div style="margin-bottom:14px;padding:10px 12px;background:var(--tint);border-radius:8px;font-size:12px">
        <p style="font-size:10px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:8px">Attendees</p>
        <ul style="list-style:none;padding:0;margin:0;display:grid;gap:6px">
          <li style="display:flex;gap:8px">
            <span style="color:var(--muted);min-width:56px;flex-shrink:0">Alumni</span>
            <span style="color:var(--ink);font-weight:600">{{ $alumnus->name }}</span>
          </li>
          @if($bringSpouse)
          <li style="display:flex;gap:8px">
            <span style="color:var(--muted);min-width:56px;flex-shrink:0">Spouse</span>
            <span style="color:var(--ink)">{{ $spouseName }}{{ $spouseNameBn ? ' ('.$spouseNameBn.')' : '' }}</span>
          </li>
          @endif
          @foreach($childrenList as $i => $child)
          <li style="display:flex;gap:8px">
            <span style="color:var(--muted);min-width:56px;flex-shrink:0">Child {{ $i + 1 }}</span>
            <span style="color:var(--ink)">{{ ($child['name'] ?? '—').(!empty($child['name_bn']) ? ' ('.$child['name_bn'].')' : '') }}</span>
          </li>
          @endforeach
          @foreach($guestsList as $i => $guest)
          <li style="display:flex;gap:8px">
            <span style="color:var(--muted);min-width:56px;flex-shrink:0">Guest {{ $i + 1 }}</span>
            <span style="color:var(--ink)">{{ ($guest['name'] ?? '—').(!empty($guest['relationship']) ? ' ('.$guest['relationship'].')' : '').(!empty($guest['contact']) ? ' · '.$guest['contact'] : '') }}</span>
          </li>
          @endforeach
          @if($driverIncl)
          <li style="display:flex;gap:8px">
            <span style="color:var(--muted);min-width:56px;flex-shrink:0">Driver</span>
            <span style="color:var(--ink)">Included</span>
          </li>
          @endif
        </ul>
      </div>
      @endif

      <div style="border-top:1px solid var(--line);padding-top:12px;display:grid;gap:8px">
        @if($overpaid)
        <form method="POST" action="{{ route('admin.alumni.payment.adjust', $alumnus) }}">
          @csrf
          <button onclick="return confirm('Adjust to current total?')"
            class="btn btn-sm" style="width:100%;justify-content:center;background:#fef3c7;color:#b45309;border-color:#fcd34d">
            ⇅ Adjust / Apply Credit (set paid = ৳{{ number_format($reg->total_amount) }})
          </button>
        </form>
        @elseif($fullyPaid)
        <form method="POST" action="{{ route('admin.alumni.payment.reset', $alumnus) }}">
          @csrf
          <button onclick="return confirm('Reset payment to unpaid?')" class="btn btn-ghost btn-sm" style="width:100%;justify-content:center">
            ↺ Reset to Unpaid
          </button>
        </form>
        @else
        <form method="POST" action="{{ route('admin.alumni.payment.confirm', $alumnus) }}">
          @csrf
          <button class="btn btn-primary" style="width:100%;justify-content:center">
            @if($reg->paid_amount > 0)
              ✓ Confirm Additional Payment (৳{{ number_format($balanceDue) }})
            @else
              ✓ Confirm Payment (Mark as Paid)
            @endif
          </button>
        </form>
        @if($reg->payment_status === 'pending')
        <div style="margin-top:4px;padding-top:10px;border-top:1px solid var(--line)">
          <p style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.07em;margin-bottom:8px">Cancel Payment Request</p>
          <form method="POST" action="{{ route('admin.alumni.payment.cancel', $alumnus) }}" style="display:grid;gap:8px"
            onsubmit="return confirm('Cancel this pending payment? The member will need to resubmit.')">
            @csrf
            <input type="text" name="reason" required maxlength="300"
              placeholder="Reason for cancellation…"
              style="font-size:12px">
            <button type="submit" class="btn btn-sm"
              style="width:100%;justify-content:center;background:#fee2e2;color:#991b1b;border-color:#fca5a5">
              ✕ Cancel Payment Request
            </button>
          </form>
        </div>
        @endif
        @endif
      </div>
    </div>
    @else
    <div style="background:var(--tint);border:1px solid var(--line);border-radius:12px;padding:16px;font-size:13px;color:var(--muted);text-align:center">
      Not registered for event yet.
    </div>
    @endif
  </div>

  {{-- Right column --}}
  <div style="display:grid;gap:14px">

    {{-- Payment History --}}
    <div class="panel">
      <p style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:12px">Payment History</p>
      @if($alumnus->paymentLogs->isEmpty())
        <p style="font-size:13px;color:var(--muted);text-align:center;padding:12px 0">No payment activity yet.</p>
      @else
      <ul style="list-style:none;padding:0;margin:0;display:grid;gap:12px">
        @foreach($alumnus->paymentLogs->sortByDesc('created_at') as $log)
        @php
          [$icon, $cls] = match($log->type) {
            'confirmed','ssl_confirmed' => ['✓', 'badge-green'],
            'submitted'                 => ['⏳', 'badge-blue'],
            'reset'                     => ['↺', 'badge-yellow'],
            'adjusted'                  => ['⇅', 'badge-yellow'],
            'cancelled'                 => ['✕', 'badge-red'],
            default                     => ['·', 'badge-gray'],
          };
        @endphp
        <li style="display:flex;align-items:flex-start;gap:10px;font-size:13px">
          <span class="badge {{ $cls }}" style="margin-top:2px">{{ $icon }}</span>
          <div style="flex:1;min-width:0">
            <p style="color:var(--ink)">{{ $log->note }}</p>
            <p style="font-size:11px;color:var(--muted);margin-top:2px">
              {{ $log->actor }} · {{ $log->created_at->format('d M Y, H:i') }}
            </p>
          </div>
          @if($log->amount > 0)
          <span style="font-size:13px;font-weight:700;color:var(--ink);flex-shrink:0">৳{{ number_format($log->amount) }}</span>
          @endif
        </li>
        @endforeach
      </ul>
      @endif
    </div>

    {{-- Admin Notes --}}
    <div class="panel">
      <p style="font-size:11px;color:var(--muted);text-transform:uppercase;letter-spacing:.08em;margin-bottom:14px">Admin Notes</p>

      <form method="POST" action="{{ route('admin.alumni.notes.store', $alumnus) }}" style="display:flex;gap:8px;margin-bottom:16px">
        @csrf
        <input type="text" name="note" placeholder="Add a note about this alumnus…" style="flex:1;@error('note') border-color:var(--red-700); @enderror" maxlength="500" required>
        <button type="submit" class="btn btn-primary btn-sm" style="flex-shrink:0">Add</button>
      </form>
      @error('note')
        <p style="color:var(--red-700);font-size:12px;margin-top:-10px;margin-bottom:10px">{{ $message }}</p>
      @enderror

      @if($alumnus->adminNotes->isEmpty())
        <p style="font-size:13px;color:var(--muted);text-align:center;padding:12px 0">No notes yet.</p>
      @else
        <ul style="list-style:none;padding:0;margin:0;display:grid;gap:8px">
          @foreach($alumnus->adminNotes->sortByDesc('created_at') as $note)
          <li style="background:var(--tint);border-radius:10px;padding:12px 14px;display:flex;align-items:flex-start;justify-content:space-between;gap:10px">
            <div style="min-width:0">
              <p style="font-size:13px;color:var(--ink)">{{ $note->note }}</p>
              <p style="font-size:11px;color:var(--muted);margin-top:4px">
                {{ $note->admin->name }} · {{ $note->created_at->format('d M Y, H:i') }}
              </p>
            </div>
            @if($note->admin_id === auth()->id())
            <form method="POST" action="{{ route('admin.notes.destroy', $note) }}" style="flex-shrink:0">
              @csrf
              @method('DELETE')
              <button onclick="return confirm('Delete this note?')"
                style="background:none;border:none;cursor:pointer;font-size:12px;color:var(--red-700);font-weight:600">
                Delete
              </button>
            </form>
            @endif
          </li>
          @endforeach
        </ul>
      @endif
    </div>

  </div>

</div>
@endsection
