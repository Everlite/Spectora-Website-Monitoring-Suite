<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>Website Back Online</title>
</head>
<body style="font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #0b0f17; color: #e2e8f0; margin: 0; padding: 24px;">
    <div style="max-width: 600px; margin: 0 auto; background-color: #131b2e; border: 1px solid #1e293b; border-radius: 16px; overflow: hidden; box-shadow: 0 10px 25px rgba(0,0,0,0.5);">
        <div style="background: linear-gradient(135deg, #10b981, #059669); padding: 24px; text-align: center;">
            <h1 style="color: #ffffff; margin: 0; font-size: 22px; font-weight: 800; letter-spacing: -0.5px;">
                ✅ Incident Resolved: Back Online
            </h1>
        </div>
        <div style="padding: 28px;">
            <p style="font-size: 15px; line-height: 1.6; color: #94a3b8;">
                Great news! Your monitored website has recovered and is now responding with healthy HTTP status codes.
            </p>
            <div style="background-color: #0b0f17; border: 1px solid #1e293b; border-radius: 12px; padding: 16px; margin: 20px 0;">
                <div style="font-size: 12px; text-transform: uppercase; color: #64748b; font-weight: 700; margin-bottom: 4px;">Monitored URL</div>
                <div style="font-size: 16px; font-weight: 700; color: #38bdf8;">{{ $checkedUrl ?? $domain->url }}</div>
            </div>
            <div style="text-align: center; margin-top: 28px;">
                <a href="{{ rtrim(config('app.url'), '/') }}/domains/{{ $domain->uuid }}" style="display: inline-block; background: #38bdf8; color: #0b0f17; text-decoration: none; font-weight: 700; font-size: 14px; padding: 12px 24px; border-radius: 8px;">
                    Open Domain Dashboard
                </a>
            </div>
        </div>
        <div style="padding: 16px; background-color: #070b14; text-align: center; font-size: 12px; color: #475569;">
            Sent by Spectora Incident State Machine · {{ config('app.name') }}
        </div>
    </div>
</body>
</html>
