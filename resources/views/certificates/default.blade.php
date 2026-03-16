<!doctype html>
<html>
<head>
    <meta charset="utf-8">
    <title>Certificate {{ $uuid }}</title>
    <style>
        @page { margin: 28px; }
        body { margin: 0; padding: 0; font-family: DejaVu Sans, sans-serif; color: #111827; }

        .frame { position: relative; width: 100%; height: 100%; border: 2px solid #111827; }
        .frame-inner { position: absolute; top: 10px; left: 10px; right: 10px; bottom: 10px; border: 1px solid #9ca3af; }

        .content { position: absolute; top: 0; left: 0; right: 0; bottom: 0; padding: 44px 56px; }

        .brand { font-size: 12px; letter-spacing: 3px; text-transform: uppercase; color: #6b7280; }
        .site { font-size: 16px; font-weight: 700; margin-top: 6px; }

        .title { margin-top: 34px; font-size: 40px; font-weight: 700; text-align: center; }
        .subtitle { margin-top: 10px; font-size: 14px; color: #4b5563; text-align: center; }

        .name { margin-top: 26px; font-size: 34px; font-weight: 700; text-align: center; }
        .line { margin: 14px auto 0 auto; width: 360px; height: 1px; background: #111827; }

        .body { margin-top: 18px; text-align: center; font-size: 14px; color: #374151; }
        .course { margin-top: 10px; text-align: center; font-size: 22px; font-weight: 700; }

        .meta-table { width: 100%; margin-top: 34px; }
        .meta-table td { vertical-align: top; font-size: 12px; color: #374151; }
        .meta-label { color: #6b7280; text-transform: uppercase; letter-spacing: 2px; font-size: 10px; }
        .sig-line { margin-top: 18px; width: 240px; height: 1px; background: #9ca3af; }

        .footer { position: absolute; left: 56px; right: 56px; bottom: 44px; }
        .footer-table { width: 100%; }
        .footer-table td { font-size: 10px; color: #6b7280; }
        .badge { display: inline-block; padding: 6px 10px; border: 1px solid #111827; font-size: 10px; letter-spacing: 2px; text-transform: uppercase; }
    </style>
</head>
<body>
    <div class="frame">
        <div class="frame-inner"></div>
        <div class="content">
            <div class="brand">{{ $siteName }}</div>
            <div class="site">Certificate</div>

            <div class="title">Certificate of Completion</div>
            <div class="subtitle">This certifies that</div>

            <div class="name">{{ $studentName }}</div>
            <div class="line"></div>

            <div class="body">has successfully completed the course</div>
            <div class="course">{{ $courseTitle }}</div>

            <table class="meta-table" cellpadding="0" cellspacing="0">
                <tr>
                    <td style="width: 33%;">
                        <div class="meta-label">Completion Date</div>
                        <div style="font-weight:700; margin-top:6px;">
                            {{ \Illuminate\Support\Carbon::parse($completionDate)->format('F j, Y') }}
                        </div>
                    </td>
                    <td style="width: 34%; text-align:center;">
                        <span class="badge">Verified</span>
                    </td>
                    <td style="width: 33%; text-align:right;">
                        <div class="meta-label">Instructor</div>
                        <div style="font-weight:700; margin-top:6px;">{{ $instructorName }}</div>
                        <div class="sig-line" style="margin-left:auto;"></div>
                        <div class="meta-label" style="margin-top:6px;">Signature</div>
                    </td>
                </tr>
            </table>

            <div class="footer">
                <table class="footer-table" cellpadding="0" cellspacing="0">
                    <tr>
                        <td>
                            Certificate ID: {{ strtoupper($uuid) }}
                        </td>
                        <td style="text-align:right;">
                            Issued by {{ $siteName }}
                        </td>
                    </tr>
                </table>
            </div>
        </div>
    </div>
</body>
</html>

