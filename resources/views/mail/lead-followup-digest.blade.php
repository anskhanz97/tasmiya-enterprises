<!doctype html>
<html lang="en"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Your Tasmiya follow-ups</title></head>
<body style="margin:0;background:#f2f2f8;color:#202857;font-family:Arial,sans-serif;line-height:1.6">
<table role="presentation" style="width:100%;border-collapse:collapse"><tr><td style="padding:28px 12px">
<table role="presentation" style="width:100%;max-width:660px;margin:auto;border-collapse:collapse;background:white">
<tr><td style="padding:28px;background:#343174;color:white"><p style="margin:0;font-size:13px;letter-spacing:1px">TASMIYA ENTERPRISES</p><h1 style="margin:10px 0 0;font-size:26px">Your daily follow-up summary</h1></td></tr>
<tr><td style="padding:28px"><p>Hello {{ $recipient->name }},</p><p>Here are your overdue and upcoming follow-ups for {{ $digestDate }}. Dates and times use Asia/Karachi.</p>
@php($counts = collect($items)->countBy('category'))
<p style="padding:14px;background:#eeebfb;color:#51429d"><strong>{{ $counts['Overdue'] ?? 0 }} overdue · {{ $counts['Today'] ?? 0 }} today · {{ $counts['Upcoming'] ?? 0 }} upcoming</strong></p>
@foreach($items as $item)
<table role="presentation" style="width:100%;border-collapse:collapse;margin:18px 0;border-bottom:1px solid #e1dfef"><tr><td style="padding:0 0 18px"><p style="margin:0;font-size:12px;color:#646b87">{{ $item['category'] }} · {{ $item['department'] }}</p><h2 style="margin:5px 0;font-size:18px">{{ $item['name'] }}</h2><p style="margin:0">{{ $item['due'] }}</p>@if($item['remarks'])<p style="color:#646b87">{{ $item['remarks'] }}</p>@endif<a href="{{ $item['url'] }}" style="color:#6054b5;font-weight:bold">Open case #{{ $item['lead_id'] }}</a></td></tr></table>
@endforeach
<p><a href="{{ route('notifications.index', ['view' => 'followups']) }}" style="display:inline-block;background:#6054b5;color:white;padding:12px 20px;border-radius:12px;text-decoration:none;font-weight:bold">View my follow-ups</a></p>
<p style="font-size:12px;color:#646b87">This summary reflects your assignments when it was prepared. Sign in to see the latest status. Completing, rescheduling, or closing a follow-up updates future summaries.</p></td></tr></table>
<p style="max-width:660px;margin:20px auto;color:#646b87;font-size:12px">Tasmiya Enterprises · Your workspace</p>
</td></tr></table></body></html>
