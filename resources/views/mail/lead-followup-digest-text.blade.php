TASMIYA ENTERPRISES — DAILY FOLLOW-UP SUMMARY
{{ $digestDate }} · Asia/Karachi

Hello {{ $recipient->name }},

@foreach($items as $item)
{{ $item['category'] }}: {{ $item['name'] }} — {{ $item['department'] }}
{{ $item['due'] }}
@if($item['remarks']){{ $item['remarks'] }}@endif

Open case #{{ $item['lead_id'] }}: {{ $item['url'] }}

@endforeach
View my follow-ups: {{ route('notifications.index', ['view' => 'followups']) }}

This summary reflects your assignments when prepared. Sign in for the latest status.
