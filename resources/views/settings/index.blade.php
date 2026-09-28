@extends('layouts.app')
@section('title', 'Integrations & settings')
@section('content')
@php
    $cards = [
        'contact' => ['Company & location', 'Details visitors see on the contact page.', [
            'company_email' => ['Company email', 'email', 'contact@tasmiya.com'],
            'company_whatsapp' => ['Company WhatsApp', 'tel', '+92 312 4246916'],
            'company_address' => ['Office address', 'text', 'Office, street, city, country'],
            'map_embed_url' => ['Google Maps embed URL', 'url', 'https://www.google.com/maps/embed?...'],
        ]],
        'google' => ['Google Drive Picker', 'Import profile images from Google Drive.', [
            'google_client_id' => ['OAuth web client ID', 'text', '...apps.googleusercontent.com'],
            'google_api_key' => ['Browser API key', 'text', 'AIza...'],
            'google_app_id' => ['Cloud project number', 'text', 'Numbers only, not the client ID'],
        ]],
        'onedrive' => ['OneDrive image picker', 'Microsoft sign-in grants read-only access when an editor chooses an image.', [
            'onedrive_client_id' => ['Entra application (client) ID', 'text', 'xxxxxxxx-xxxx-xxxx-xxxx-xxxxxxxxxxxx'],
        ]],
        'email' => ['Inquiry email', 'Send new contact inquiries using your SMTP account.', [
            'smtp_host' => ['SMTP host', 'text', 'smtp.example.com'],
            'smtp_port' => ['SMTP port', 'number', '587'],
            'smtp_username' => ['SMTP username', 'text', ''],
            'smtp_password' => ['SMTP password', 'password', 'Enter a new SMTP password'],
            'smtp_from_email' => ['From email', 'email', 'no-reply@example.com'],
        ]],
        'whatsapp' => ['WhatsApp notifications', 'Meta Cloud API can notify the company when an inquiry arrives.', [
            'whatsapp_business_account_id' => ['Business account ID (WABA)', 'text', 'From WhatsApp Manager'],
            'whatsapp_phone_id' => ['Phone number ID', 'text', 'From WhatsApp Manager'],
            'whatsapp_access_token' => ['Meta access token', 'password', 'Enter a new access token'],
            'whatsapp_template_name' => ['Approved alert template', 'template', ''],
            'whatsapp_app_secret' => ['Meta app secret', 'password', 'Signs webhook requests'],
            'whatsapp_verify_token' => ['Webhook verify token', 'password', 'A private token you choose'],
        ]],
        'bank' => ['Bank transfer', 'Customers transfer to this account and submit a reference.', [
            'bank_name' => ['Bank name', 'text', ''],
            'bank_account_title' => ['Account title', 'text', ''],
            'bank_iban' => ['IBAN', 'text', 'PK...'],
        ]],
        'raast' => ['Raast', 'Show an alias or ID for manual Raast transfers.', [
            'raast_id' => ['Raast ID or alias', 'text', ''],
        ]],
        'easypaisa' => ['Easypaisa', 'Show a wallet destination for manual transfer.', [
            'easypaisa_number' => ['Receiving number', 'tel', ''],
            'easypaisa_title' => ['Account title', 'text', ''],
        ]],
        'jazzcash' => ['JazzCash', 'Show a wallet destination for manual transfer.', [
            'jazzcash_number' => ['Receiving number', 'tel', ''],
            'jazzcash_title' => ['Account title', 'text', ''],
        ]],
        'stripe' => ['Stripe', 'Optional card checkout through Stripe.', [
            'stripe_public' => ['Publishable key', 'text', 'pk_...'],
            'stripe_secret' => ['Secret key', 'password', 'Enter a new secret key'],
            'stripe_webhook_secret' => ['Webhook signing secret', 'password', 'Enter a new signing secret'],
        ]],
    ];
    $switches = ['bank'=>'enable_bank_transfer', 'raast'=>'enable_raast', 'easypaisa'=>'enable_easypaisa', 'jazzcash'=>'enable_jazzcash', 'stripe'=>'enable_card'];
    $paymentCards = ['bank','raast','easypaisa','jazzcash','stripe'];
    $secretFields = ['smtp_password','whatsapp_access_token','whatsapp_app_secret','whatsapp_verify_token','stripe_secret','stripe_webhook_secret'];
    $googleValid = preg_match('/^[0-9]+-[a-z0-9]+\.apps\.googleusercontent\.com$/i', (string) $settings['google_client_id']) && preg_match('/^AIza[0-9A-Za-z_-]+$/', (string) $settings['google_api_key']) && ctype_digit((string) $settings['google_app_id']);
@endphp
<style>
@import url('https://fonts.googleapis.com/css2?family=DM+Sans:wght@400;500;600;700;800&family=Outfit:wght@400;500;600;700;800&display=swap');
.ig{--ink:#202857;--accent:#5d50af;--muted:#69728d;--line:#dde1ef;background:#f3f4fa;min-height:100vh;padding:35px clamp(16px,4vw,64px) 90px;color:var(--ink);font-family:'DM Sans',sans-serif}.ig *{box-sizing:border-box}.ig-inner{max-width:1320px;margin:auto}.ig-head{background:#202857;color:white;border-radius:22px;padding:42px clamp(26px,4vw,58px);position:relative;overflow:hidden}.ig-head:after{content:'';position:absolute;width:380px;height:380px;border:1px solid #b6aefa44;border-radius:50%;right:-80px;top:-160px;box-shadow:0 0 0 78px #b6aefa13,0 0 0 155px #b6aefa0a}.ig-back{color:#c9c6f2;text-decoration:none;font-size:13px;font-weight:800}.ig-head h1{font:700 clamp(35px,4vw,57px) Outfit;letter-spacing:-.05em;margin:23px 0 8px}.ig-head p{max-width:620px;color:#d1d4e9;line-height:1.6;margin:0}.ig-layout{display:grid;grid-template-columns:218px minmax(0,1fr);gap:23px;margin-top:24px}.ig-nav{position:sticky;top:92px;align-self:start;background:#e7e9f4;padding:12px;border-radius:15px}.ig-nav a{display:block;text-decoration:none;color:#535d80;padding:10px 13px;border-radius:8px;font-size:12px;font-weight:800}.ig-nav a:hover,.ig-nav a:focus-visible{background:#fff;color:#43379d}.ig-nav-label{font-size:10px!important;color:#8990aa!important;letter-spacing:.08em;padding-top:20px!important}.ig-card{background:white;border:1px solid var(--line);border-radius:17px;margin-bottom:15px;scroll-margin-top:92px;overflow:hidden}.ig-card:target{box-shadow:0 0 0 2px #aca1e6}.ig-card-head{display:flex;align-items:start;justify-content:space-between;gap:20px;padding:26px 30px 20px}.ig-card h2{font:700 27px Outfit;letter-spacing:-.04em;margin:0 0 5px}.ig-card-head p{font-size:13px;line-height:1.5;color:var(--muted);margin:0}.ig-edit{border:1px solid #cfc9ed;border-radius:9px;background:#f5f3ff;color:#4b408e;padding:10px 16px;cursor:pointer;font:800 12px 'DM Sans',sans-serif;white-space:nowrap}.ig-edit:hover{background:#e9e5fb}.ig-status{margin:0 30px 18px;font-size:11px;font-weight:800;border-radius:7px;padding:7px 10px;display:inline-block;background:#eceafa;color:#5146a0}.ig-status.warn{background:#fff2df;color:#896022}.ig-status.off{background:#edf0f6;color:#67708b}.ig-values{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:0;padding:0 30px 28px}.ig-value{border-top:1px solid #eef0f6;padding:14px 12px 13px 0;min-width:0}.ig-value span{display:block;font-size:11px;font-weight:800;color:#6b7591;margin-bottom:6px}.ig-value strong{font-size:13px;font-weight:700;overflow-wrap:anywhere}.ig-value .missing{color:#9ca5b9;font-weight:500}.ig-editor{border-top:1px solid #e9ebf4;background:#fafbff;padding:26px 30px 28px}.ig-editor[hidden],.ig-values[hidden]{display:none!important}.ig-fields{display:grid;grid-template-columns:repeat(2,minmax(0,1fr));gap:18px}.ig-field{display:flex;flex-direction:column;gap:8px;min-width:0}.ig-field label{font-size:12px;font-weight:800}.ig-field input,.ig-field select{width:100%;border:1px solid #cbd2e7;border-radius:9px;background:white;padding:12px 13px;font:500 13px 'DM Sans',sans-serif;color:#202857;outline:none}.ig-field input:focus,.ig-field select:focus{border-color:#6c5fbd;box-shadow:0 0 0 3px #e4dffc}.ig-field small{font-size:11px;color:#68718d}.ig-actions{display:flex;gap:10px;align-items:center;margin-top:22px}.ig-save{border:0;background:var(--accent);border-radius:9px;color:white;padding:12px 19px;font:800 12px 'DM Sans',sans-serif;cursor:pointer}.ig-save:hover{background:#433895}.ig-cancel,.ig-plain{border:0;background:transparent;color:#5b52a1;font:800 12px 'DM Sans',sans-serif;cursor:pointer;padding:10px}.ig-check{display:flex;align-items:center;gap:9px;font-size:12px;font-weight:800;margin-top:20px}.ig-check input{accent-color:#5d50af;width:17px;height:17px}.ig-check.small{font-size:11px;color:#6c7590;margin-top:2px}.ig-alert{padding:14px 18px;border-radius:10px;margin:18px 0;background:#e2f1e8;color:#1c6549;font-size:13px}.ig-alert.error{background:#fcebe9;color:#923f3d}.ig-help{font-size:12px;line-height:1.6;color:#6b7591;margin:18px 0 0}.ig-help a{color:#5448a4}.ig-payment-note{background:#eeecfa;border-radius:13px;padding:18px 22px;margin:0 0 16px;color:#46457d;font-size:12px;line-height:1.6}.ig-payment-note strong{display:block;font:700 19px Outfit;margin-bottom:4px;color:#202857}.ig-social-list{padding:0 30px 25px;display:grid;gap:8px}.ig-social-row{display:grid;grid-template-columns:1fr 2fr auto;gap:10px;align-items:center;border-top:1px solid #eef0f6;padding:13px 0;font-size:13px}.ig-social-row span{font-weight:800}.ig-social-row a{overflow:hidden;text-overflow:ellipsis;white-space:nowrap;color:#5850a1}.ig-social-fields{display:grid;gap:10px}.ig-social-edit-row{display:grid;grid-template-columns:minmax(110px,1fr) minmax(160px,2fr) auto auto auto;gap:8px;align-items:center}.ig-social-edit-row input{border:1px solid #cbd2e7;border-radius:9px;padding:11px;min-width:0;font:500 13px 'DM Sans',sans-serif}.ig-social-edit-row button{border:1px solid #d9d9e9;background:white;border-radius:8px;padding:10px;cursor:pointer;color:#504796}.ig-social-edit-row button[data-remove]{color:#9b4747}.ig-test{display:inline-block;margin-top:13px;color:#5448a4;font-size:12px;font-weight:800}.ig-template-note{background:#efedf9;padding:12px;border-radius:8px;margin-top:15px;font-size:12px;color:#505b7c}.ig-mono{font-family:ui-monospace,SFMono-Regular,Consolas,monospace!important;font-size:12px!important}
@media(max-width:840px){.ig-layout{grid-template-columns:1fr}.ig-nav{position:static;display:flex;overflow:auto}.ig-nav a{white-space:nowrap}.ig-nav-label{display:none}}@media(max-width:600px){.ig{padding:16px 12px 70px}.ig-head{padding:28px}.ig-card-head,.ig-editor{padding:20px}.ig-values,.ig-social-list{padding:0 20px 20px}.ig-values,.ig-fields{grid-template-columns:1fr}.ig-social-edit-row{grid-template-columns:1fr 1fr}.ig-social-edit-row input:nth-of-type(2){grid-column:1/-1}.ig-status{margin-left:20px}.ig-card h2{font-size:24px}}@media(prefers-reduced-motion:reduce){.ig *{transition:none!important;animation:none!important}}
</style>
<main class="ig"><div class="ig-inner">
<header class="ig-head"><a class="ig-back" href="{{ route('dashboard') }}">Back to dashboard</a><h1>Integrations & settings</h1><p>See what is connected, edit one service at a time, and keep your public details accurate.</p></header>
@if(session('success'))<div class="ig-alert" role="status">{{ session('success') }}</div>@endif
@if($errors->any())<div class="ig-alert error" role="alert"><strong>Changes were not saved.</strong><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<div class="ig-layout"><nav class="ig-nav" aria-label="Settings sections">
@foreach(['contact'=>'Company & location','google'=>'Google Drive','onedrive'=>'OneDrive','email'=>'Inquiry email','whatsapp'=>'WhatsApp','bank'=>'Bank transfer','raast'=>'Raast','easypaisa'=>'Easypaisa','jazzcash'=>'JazzCash','stripe'=>'Stripe','social'=>'Social links'] as $id=>$label)
@if($id==='bank')<span class="ig-nav-label">PAYMENTS</span>@endif
<a href="#ig-{{ $id }}">{{ $label }}</a>
@endforeach
</nav><div>
@foreach($cards as $id=>[$title,$intro,$fields])
@if($id==='bank')<div class="ig-payment-note"><strong>Payment methods</strong>Bank, Raast and wallet details below enable manual transfers with admin verification. Automatic JazzCash, Easypaisa or Raast collection requires a merchant agreement and provider-specific API integration; saving a number here does not activate a gateway.</div>@endif
@php
    $enabledKey = $switches[$id] ?? null;
    $configured = match($id) {
        'contact' => filled($settings['company_email']) && filled($settings['company_whatsapp']) && filled($settings['company_address']),
        'google' => (bool) $googleValid,
        'onedrive' => filled($settings['onedrive_client_id']),
        'email' => filled($settings['smtp_host']) && filled($settings['smtp_from_email']),
        'whatsapp' => filled($settings['whatsapp_phone_id']) && filled($settings['whatsapp_access_token']) && filled($settings['whatsapp_template_name']),
        'bank' => filled($settings['bank_iban']),
        'raast' => filled($settings['raast_id']),
        'easypaisa' => filled($settings['easypaisa_number']),
        'jazzcash' => filled($settings['jazzcash_number']),
        'stripe' => filled($settings['stripe_public']) && filled($settings['stripe_secret']) && filled($settings['stripe_webhook_secret']),
    };
    $enabled = $enabledKey ? $settings[$enabledKey] === '1' : true;
    $status = $configured ? ($enabled ? ($id==='google' ? ($googlePickerVerified ? 'Configured' : 'Credentials saved · import unverified') : ($id==='onedrive' ? 'App ID saved; sign-in untested' : (in_array($id,$paymentCards) && $id!=='stripe' ? 'Manual transfer available' : 'Configured'))) : 'Disabled') : 'Needs setup';
@endphp
<section class="ig-card" id="ig-{{ $id }}" data-card="{{ $id }}">
<div class="ig-card-head"><div><h2>{{ $title }}</h2><p>{{ $intro }}</p></div><button type="button" class="ig-edit" data-edit="{{ $id }}" aria-controls="ig-editor-{{ $id }}" aria-expanded="false">Edit</button></div>
<span class="ig-status {{ $configured ? ($enabled ? ($id==='google' && !$googlePickerVerified ? 'warn' : '') : 'off') : 'warn' }}">{{ $status }}</span>
<div class="ig-values" id="ig-values-{{ $id }}">
@foreach($fields as $key=>[$label,$type,$placeholder])
@php
    $displayValue = blank($settings[$key]) ? 'Not set' : (in_array($key,$secretFields, true) ? 'Saved securely' : ($key==='google_api_key' ? substr($settings[$key],0,6).'••••'.substr($settings[$key],-4) : $settings[$key]));
@endphp
<div class="ig-value"><span>{{ $label }}</span><strong class="{{ blank($settings[$key]) ? 'missing' : '' }}">{{ $displayValue }}</strong></div>
@endforeach
@if($enabledKey)<div class="ig-value"><span>Customer-facing option</span><strong>{{ $enabled ? 'Enabled' : 'Disabled' }}</strong></div>@endif
</div>
<form method="POST" action="{{ route('settings.integrations.update') }}" class="ig-editor" id="ig-editor-{{ $id }}" hidden autocomplete="off">@csrf @method('PUT')<input type="hidden" name="section" value="{{ $id }}">
<div class="ig-fields">
@foreach($fields as $key=>[$label,$type,$placeholder])
<div class="ig-field"><label for="ig-{{ $key }}">{{ $label }}</label>
@if($type==='template')
<select id="ig-{{ $key }}" name="{{ $key }}"><option value="">No template selected</option>@if($settings[$key])<option value="{{ $settings[$key] }}" selected>{{ $settings[$key] }} (saved)</option>@endif</select>
<button type="button" class="ig-plain" id="ig-load-templates" style="text-align:left;padding:0">Load approved templates from Meta</button><small id="ig-template-status">Create and submit a template in WhatsApp Manager first. Only approved templates can send alerts.</small>
@else
<input id="ig-{{ $key }}" name="{{ $key }}" type="{{ $type }}" value="{{ $type==='password' ? '' : old($key,$settings[$key]) }}" placeholder="{{ $placeholder }}" autocomplete="{{ $type==='password' ? 'new-password' : 'off' }}" @if(in_array($key,['smtp_username','smtp_password'])) readonly data-unlock-on-edit @endif @if($key==='google_app_id') inputmode="numeric" pattern="[0-9]+" @endif>
@if(in_array($key,$secretFields) && filled($settings[$key]))<small>Saved securely. Leave blank to keep it.</small><label class="ig-check small"><input type="checkbox" name="clear_secrets[]" value="{{ $key }}"> Remove saved value</label>@endif
@endif
</div>
@endforeach
@if($id==='email')<div class="ig-field"><label for="ig-smtp_encryption">Connection security</label><select id="ig-smtp_encryption" name="smtp_encryption"><option value="tls" @selected($settings['smtp_encryption']==='tls')>TLS</option><option value="ssl" @selected($settings['smtp_encryption']==='ssl')>SSL</option><option value="none" @selected($settings['smtp_encryption']==='none')>None</option></select></div>@endif
</div>
@if($enabledKey)<label class="ig-check"><input type="checkbox" name="{{ $enabledKey }}" value="1" @checked($enabled)> Show {{ $title }} as a payment option</label>@endif
@if($id==='google')<p class="ig-help">Enable both Google Picker API and Google Drive API in this Cloud project. For this local server, register <code>http://localhost:8000</code> as an Authorized JavaScript origin in the Google OAuth <strong>Web application</strong> client, then open the site using <strong>localhost</strong>, not 127.0.0.1. This JavaScript token flow does not need a redirect URI. The project number is digits only. If you restrict the API key to websites, also allow <code>http://localhost:8000/*</code> and <code>https://docs.google.com/*</code>; allow both Google Picker API and Drive API under API restrictions.</p>@endif
@if($id==='onedrive')<p class="ig-help">This client-side MSAL integration needs an Entra SPA registration with <code>http://localhost:8000/</code> as a redirect URI and delegated Files.Read permission. One application ID is enough in the portal; no client secret belongs in a browser app.</p>@endif
@if($id==='whatsapp')<div class="ig-template-note">Templates are created, edited and approved in <a href="https://business.facebook.com/wa/manage/message-templates/" target="_blank" rel="noopener noreferrer">WhatsApp Manager ↗</a>. Save your WABA ID and access token, then load approved templates here. The alert template needs one body variable for the inquiry summary.</div>@endif
@if(in_array($id,['raast','easypaisa','jazzcash']))<p class="ig-help">This card configures manual transfers only. Provider API credentials vary by merchant product and are not collected until an automatic gateway adapter is enabled.</p>@endif
<div class="ig-actions"><button type="submit" class="ig-save">Save {{ $title }}</button><button type="button" class="ig-cancel" data-cancel="{{ $id }}">Cancel</button></div></form>
@if($id==='google' && $googleValid && !$googlePickerVerified && auth()->user()?->profile)
@php $testUrl = route('profiles.edit',auth()->user()->profile); if (app()->environment('local')) $testUrl = str_replace('127.0.0.1','localhost',$testUrl); @endphp
<a class="ig-test" href="{{ $testUrl }}#pe-media">Test Google import on your profile editor ↗</a>
@endif
</section>
@endforeach
<section class="ig-card" id="ig-social" data-card="social"><div class="ig-card-head"><div><h2>Social links</h2><p>Add, remove and reorder the links shown in the footer.</p></div><button type="button" class="ig-edit" data-edit="social" aria-controls="ig-editor-social" aria-expanded="false">Edit</button></div>
<span class="ig-status {{ count($socialLinks) ? '' : 'off' }}">{{ count($socialLinks) }} {{ count($socialLinks)===1 ? 'link' : 'links' }} shown</span>
<div class="ig-social-list" id="ig-values-social">@forelse($socialLinks as $link)<div class="ig-social-row"><span>{{ $link['label'] }}</span><a href="{{ $link['url'] }}" target="_blank" rel="noopener noreferrer">{{ $link['url'] }}</a><span>↗</span></div>@empty<div class="ig-value"><strong class="missing">No social links set</strong></div>@endforelse</div>
<form method="POST" action="{{ route('settings.social.update') }}" class="ig-editor" id="ig-editor-social" hidden autocomplete="off">@csrf @method('PUT')<div id="ig-social-fields" class="ig-social-fields">@foreach(old('links',$socialLinks) as $index=>$link)<div class="ig-social-edit-row"><input name="links[{{ $index }}][label]" value="{{ $link['label'] }}" maxlength="35" required aria-label="Platform name" placeholder="Platform"><input name="links[{{ $index }}][url]" value="{{ $link['url'] }}" type="url" required aria-label="Social URL" placeholder="https://"><button type="button" data-move="up" aria-label="Move link up">↑</button><button type="button" data-move="down" aria-label="Move link down">↓</button><button type="button" data-remove aria-label="Remove link">Remove</button></div>@endforeach</div><button type="button" class="ig-plain" id="ig-add-social">+ Add a social link</button><p class="ig-help">Use a full https:// URL. Changes appear on the public website after you save.</p><div class="ig-actions"><button type="submit" class="ig-save">Save social links</button><button type="button" class="ig-cancel" data-cancel="social">Cancel</button></div></form></section>
</div></div></div></main>
<script>
document.addEventListener('DOMContentLoaded',()=>{
 const open=(id,show)=>{const card=document.querySelector('[data-card="'+id+'"]'),editor=document.getElementById('ig-editor-'+id),values=document.getElementById('ig-values-'+id),button=card.querySelector('[data-edit]');editor.hidden=!show;values.hidden=show;editor.querySelectorAll('[data-unlock-on-edit]').forEach(input=>input.readOnly=!show);button.setAttribute('aria-expanded',show?'true':'false');button.textContent=show?'Editing':'Edit';if(show)editor.querySelector('input:not([type=hidden]),select')?.focus();};
 document.querySelectorAll('[data-edit]').forEach(button=>button.addEventListener('click',()=>open(button.dataset.edit,document.getElementById('ig-editor-'+button.dataset.edit).hidden)));
 document.querySelectorAll('[data-cancel]').forEach(button=>button.addEventListener('click',()=>{document.getElementById('ig-editor-'+button.dataset.cancel).reset();open(button.dataset.cancel,false)}));
 @if($errors->any())open(@json(old('section','social')),true);@endif
 const list=document.getElementById('ig-social-fields');let nextIndex=list.children.length;
 document.getElementById('ig-add-social').addEventListener('click',()=>{if(list.children.length>=20)return;const row=document.createElement('div');row.className='ig-social-edit-row';row.innerHTML='<input maxlength="35" required aria-label="Platform name" placeholder="Platform e.g. TikTok"><input type="url" required aria-label="Social URL" placeholder="https://"><button type="button" data-move="up" aria-label="Move link up">↑</button><button type="button" data-move="down" aria-label="Move link down">↓</button><button type="button" data-remove aria-label="Remove link">Remove</button>';list.appendChild(row);renumber();row.querySelector('input').focus();});
 function renumber(){[...list.children].forEach((row,index)=>{row.querySelectorAll('input')[0].name='links['+index+'][label]';row.querySelectorAll('input')[1].name='links['+index+'][url]';});nextIndex=list.children.length;}
 list.addEventListener('click',event=>{const button=event.target.closest('button'),row=button?.closest('.ig-social-edit-row');if(!row)return;if(button.hasAttribute('data-remove'))row.remove();else if(button.dataset.move==='up'&&row.previousElementSibling)list.insertBefore(row,row.previousElementSibling);else if(button.dataset.move==='down'&&row.nextElementSibling)list.insertBefore(row.nextElementSibling,row);renumber();});
 const load=document.getElementById('ig-load-templates');load?.addEventListener('click',async()=>{const status=document.getElementById('ig-template-status'),select=document.getElementById('ig-whatsapp_template_name');status.textContent='Loading approved templates…';try{const response=await fetch(@json(route('settings.whatsapp.templates')),{headers:{Accept:'application/json'}});const data=await response.json();if(!response.ok)throw new Error(data.message||'Could not load templates.');const saved=select.value;select.replaceChildren(new Option('No template selected',''));for(const template of data.templates)select.add(new Option(template.name+' ('+template.language+')',template.name));if([...select.options].some(option=>option.value===saved))select.value=saved;status.textContent=data.templates.length?data.templates.length+' approved template(s) available.':'No approved templates found. Create one in WhatsApp Manager first.';}catch(error){status.textContent=error.message;}});
});
</script>
@endsection
