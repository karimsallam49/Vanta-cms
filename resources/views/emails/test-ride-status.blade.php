<!DOCTYPE html>
<html lang="en">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"></head>
<body style="margin:0;padding:0;background:#0e0e10;font-family:Arial,Helvetica,sans-serif;color:#ececec">
  <div style="max-width:560px;margin:0 auto;padding:32px 16px">
    <div style="border:1px solid #26262a;border-radius:12px;overflow:hidden;background:#141416">
      <div style="padding:20px 28px;border-bottom:1px solid #26262a">
        <span style="font-size:18px;font-weight:800;letter-spacing:0.08em;color:#fff">VANTA<span style="color:#e10600">MOTO</span></span>
      </div>
      <div style="padding:28px">
        <p style="margin:0 0 4px;font-size:12px;letter-spacing:0.18em;text-transform:uppercase;color:#8a8a92">Test ride request</p>
        <h1 style="margin:0 0 18px;font-size:22px;color:#fff">Hi {{ $ride->name }},</h1>

        @php
          $line = match ($ride->status) {
            \App\Enums\TestRideStatus::Contacted => 'We reached out about your test ride request. Our team will confirm your slot shortly.',
            \App\Enums\TestRideStatus::Confirmed => 'Great news — your test ride is confirmed. See you at the showroom.',
            \App\Enums\TestRideStatus::Completed => 'Thanks for riding with us. We hope you enjoyed it — our team is here if you want to take the next step.',
            \App\Enums\TestRideStatus::Cancelled => 'Your test ride request has been cancelled. If this was not you, or you would like to rebook, just reply to this email.',
            default => 'We received your test ride request and will get back to you soon.',
          };
        @endphp
        <p style="margin:0 0 22px;font-size:14px;line-height:1.7;color:#c8c8ce">{{ $line }}</p>

        <table style="width:100%;border-collapse:collapse;font-size:13px">
          <tr>
            <td style="padding:10px 0;border-top:1px solid #26262a;color:#8a8a92;width:38%">Status</td>
            <td style="padding:10px 0;border-top:1px solid #26262a;color:#fff;font-weight:700">{{ $ride->status->label() }}</td>
          </tr>
          <tr>
            <td style="padding:10px 0;border-top:1px solid #26262a;color:#8a8a92">Motorcycle</td>
            <td style="padding:10px 0;border-top:1px solid #26262a;color:#fff">{{ $ride->motorcycle->brand->name }} {{ $ride->motorcycle->name }}</td>
          </tr>
          <tr>
            <td style="padding:10px 0;border-top:1px solid #26262a;color:#8a8a92">Showroom</td>
            <td style="padding:10px 0;border-top:1px solid #26262a;color:#fff">{{ $ride->showroom->name }} — {{ $ride->showroom->city }}</td>
          </tr>
          <tr>
            <td style="padding:10px 0;border-top:1px solid #26262a;color:#8a8a92">Preferred date</td>
            <td style="padding:10px 0;border-top:1px solid #26262a;color:#fff">{{ $ride->preferred_date->format('d M Y') }}</td>
          </tr>
        </table>
      </div>
      <div style="padding:16px 28px;border-top:1px solid #26262a;font-size:11px;color:#6a6a72">
        VANTA MOTO — Middle East motorcycle group · +971 800 555 0123 · ride@vantamoto.com
      </div>
    </div>
  </div>
</body>
</html>
