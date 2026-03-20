<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New User Registration</title>
</head>
@php
    $appName = $payload['app_name'] ?? config('app.name', 'Application');
    $logoUrl = $payload['app_logo_url'] ?? null;
    $parts = preg_split('/\s+/', trim((string) $appName)) ?: [];
    $initials = '';
    foreach ($parts as $part) {
        if ($part !== '') {
            $initials .= strtoupper(substr($part, 0, 1));
        }
        if (strlen($initials) >= 2) {
            break;
        }
    }
    if ($initials === '') {
        $initials = strtoupper(substr((string) $appName, 0, 2));
    }
@endphp
<body style="margin:0;padding:0;background-color:#0b1220;font-family:Arial,Helvetica,sans-serif;color:#f8fafc;">
    <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="padding:24px 12px;background-color:#0d1117;">
        <tr>
            <td align="center">
                <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="max-width:600px;background-color:#161b22;border:1px solid #30363d;border-radius:12px;box-shadow:0 18px 48px rgba(0,0,0,0.45);overflow:hidden;">
                    <tr>
                        <td style="height:5px;line-height:5px;font-size:5px;background:#3b82f6;background:linear-gradient(90deg,#2563eb 0%,#7c3aed 100%);">&nbsp;</td>
                    </tr>
                    <tr>
                        <td style="padding:24px 24px 8px 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0">
                                <tr>
                                    <td style="width:56px;vertical-align:top;">
                                        @if($logoUrl)
                                            <img src="{{ $logoUrl }}" alt="{{ $appName }}" style="width:46px;height:46px;border-radius:50%;display:block;border:1px solid #30363d;object-fit:cover;">
                                        @else
                                            <div style="width:46px;height:46px;line-height:46px;text-align:center;border-radius:50%;background-color:#1f6feb;color:#ffffff;font-size:18px;font-weight:700;">{{ $initials }}</div>
                                        @endif
                                    </td>
                                    <td style="vertical-align:top;">
                                        <div style="font-size:28px;line-height:32px;font-weight:800;color:#ffffff;letter-spacing:0.5px;">{{ strtoupper($appName) }}</div>
                                        <div style="padding-top:4px;font-size:13px;line-height:20px;color:#8b949e;">2FA Authentication Platform</div>
                                    </td>
                                </tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:6px 24px 0 24px;font-size:24px;line-height:30px;font-weight:700;color:#ffffff;">New User Registration</td>
                    </tr>
                    <tr>
                        <td style="padding:8px 24px 0 24px;font-size:15px;line-height:24px;color:#9da7b3;">A new account has been created on your platform.</td>
                    </tr>
                    <tr>
                        <td style="padding:18px 24px 0 24px;">
                            <table role="presentation" width="100%" cellspacing="0" cellpadding="0" style="background-color:#0f141b;border:1px solid #30363d;border-radius:10px;padding:14px;">
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">👤 Full Name:</strong> {{ $payload['name'] ?? 'N/A' }}</td></tr>
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">📧 Email:</strong> {{ $payload['email'] ?? 'N/A' }}</td></tr>
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">🔗 Registered Via:</strong> {{ $payload['provider'] ?? 'email' }}</td></tr>
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">🕐 Registered At:</strong> {{ $payload['registered_at'] ?? now()->timezone('Asia/Phnom_Penh')->format('Y-m-d H:i:s') }}</td></tr>
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">🌐 IP Address:</strong> {{ $payload['ip_address'] ?? 'Unknown' }}</td></tr>
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">🌍 Country:</strong> {{ $payload['country'] ?? 'Unknown' }}</td></tr>
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">🏙️ City:</strong> {{ $payload['city'] ?? 'Unknown' }}</td></tr>
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">💻 Device Type:</strong> {{ $payload['device_type'] ?? 'Unknown' }}</td></tr>
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">🖥️ OS:</strong> {{ $payload['os'] ?? 'Unknown' }}</td></tr>
                                <tr><td style="padding:6px 10px 6px 12px;border-left:3px solid #3b82f6;font-size:15px;line-height:24px;color:#d0d7de;"><strong style="color:#ffffff;">🌐 Browser:</strong> {{ $payload['browser'] ?? 'Unknown' }}</td></tr>
                            </table>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:24px 24px 0 24px;">
                            <a href="{{ $payload['view_user_url'] ?? url('/admin/users') }}" style="display:inline-block;padding:12px 20px;background-color:#2563eb;border-radius:8px;color:#ffffff;text-decoration:none;font-size:14px;font-weight:700;">View User</a>
                        </td>
                    </tr>
                    <tr>
                        <td style="padding:20px 24px 24px 24px;font-size:12px;line-height:19px;color:#8b949e;">
                            <div>© 2026 {{ strtoupper($appName) }}. All rights reserved.</div>
                            <div>This is an automated notification - please do not reply.</div>
                        </td>
                    </tr>
                </table>
            </td>
        </tr>
    </table>
</body>
</html>
