<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Official Partnership Proposal - COMSOC</title>
    <style>
        body {
            margin: 0;
            padding: 0;
            background-color: #f1f5f9;
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #1e293b;
            -webkit-font-smoothing: antialiased;
        }
        .wrapper {
            width: 100%;
            table-layout: fixed;
            background-color: #f1f5f9;
            padding: 30px 10px;
        }
        .main {
            background-color: #ffffff;
            margin: 0 auto;
            width: 100%;
            max-width: 650px;
            border-spacing: 0;
            border-radius: 16px;
            overflow: hidden;
            border: 1px solid #e2e8f0;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.05), 0 2px 4px -1px rgba(0, 0, 0, 0.03);
        }
        .header {
            background: linear-gradient(135deg, #0f172a 0%, #1e1b4b 50%, #312e81 100%);
            padding: 32px 24px;
            text-align: center;
            color: #ffffff;
        }
        .header-sub {
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 2px;
            color: #94a3b8;
            margin-bottom: 4px;
            font-weight: 600;
        }
        .header-title {
            font-size: 19px;
            font-weight: 800;
            letter-spacing: 0.5px;
            margin: 0 0 4px 0;
            color: #ffffff;
            text-transform: uppercase;
        }
        .header-college {
            font-size: 13px;
            font-weight: 600;
            color: #cbd5e1;
            margin: 0 0 2px 0;
        }
        .header-org {
            font-size: 14px;
            font-weight: 800;
            color: #818cf8;
            margin: 0;
        }
        .content {
            padding: 32px 28px;
        }
        .badge {
            display: inline-block;
            padding: 5px 12px;
            font-size: 11px;
            font-weight: 700;
            border-radius: 9999px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }
        .badge-proposed {
            background-color: #eff6ff;
            color: #1d4ed8;
            border: 1px solid #bfdbfe;
        }
        .badge-endorsed {
            background-color: #ecfdf5;
            color: #047857;
            border: 1px solid #a7f3d0;
        }
        .meta-box {
            background-color: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 12px;
            padding: 16px;
            margin: 20px 0;
        }
        .meta-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
        }
        .meta-table td {
            padding: 5px 0;
            vertical-align: top;
        }
        .meta-label {
            color: #64748b;
            font-weight: 600;
            width: 140px;
        }
        .meta-value {
            color: #0f172a;
            font-weight: 700;
        }
        .letter-body {
            background-color: #ffffff;
            border: 1px solid #cbd5e1;
            border-radius: 12px;
            padding: 24px;
            margin: 20px 0;
            font-size: 13px;
            line-height: 1.7;
            color: #334155;
            white-space: pre-line;
        }
        .custom-note {
            background-color: #fefce8;
            border-left: 4px solid #eab308;
            padding: 14px 18px;
            border-radius: 0 8px 8px 0;
            margin: 20px 0;
            font-size: 13px;
            color: #713f12;
            line-height: 1.5;
        }
        .signatures {
            margin-top: 28px;
            padding-top: 20px;
            border-top: 1px solid #e2e8f0;
            width: 100%;
        }
        .sig-block {
            font-size: 12px;
            line-height: 1.4;
        }
        .sig-title {
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 1px;
            color: #94a3b8;
            font-weight: 700;
            margin-bottom: 4px;
        }
        .sig-name {
            font-weight: 800;
            color: #0f172a;
            font-size: 13px;
        }
        .sig-role {
            color: #64748b;
            font-size: 11px;
        }
        .footer {
            background-color: #f8fafc;
            padding: 24px 28px;
            border-top: 1px solid #e2e8f0;
            font-size: 11px;
            color: #64748b;
            text-align: center;
            line-height: 1.6;
        }
        .btn-view {
            display: inline-block;
            background-color: #4338ca;
            color: #ffffff !important;
            font-size: 13px;
            font-weight: 700;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 10px;
            margin: 15px 0 5px 0;
        }
    </style>
</head>
<body>
    <div class="wrapper">
        <table class="main" width="100%" cellpadding="0" cellspacing="0">
            <!-- Header / Letterhead -->
            <tr>
                <td class="header">
                    <div class="header-sub">Republic of the Philippines</div>
                    <div class="header-title">University of Makati</div>
                    <div class="header-college">College of Computing and Information Sciences</div>
                    <div class="header-org">Computer Society (COMSOC)</div>
                </td>
            </tr>

            <!-- Content Area -->
            <tr>
                <td class="content">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 12px;">
                        <span style="font-size: 12px; color: #64748b;">
                            Date: <strong>{{ now()->format('F d, Y') }}</strong>
                        </span>
                        <span class="badge {{ $partnership->isEndorsed() ? 'badge-endorsed' : 'badge-proposed' }}">
                            {{ $partnership->isEndorsed() ? '✓ Formally Endorsed' : 'Formal Proposal' }}
                        </span>
                    </div>

                    <h2 style="font-size: 18px; font-weight: 800; color: #0f172a; margin: 8px 0 4px 0;">
                        Collaboration Proposal &amp; Letter of Intent
                    </h2>
                    <p style="font-size: 13px; color: #475569; margin: 0 0 16px 0;">
                        Addressed to: <strong>{{ $partnership->partner->contact_person ? $partnership->partner->contact_person . ' (' . $partnership->partner->name . ')' : $partnership->partner->name }}</strong>
                    </p>

                    @if($customMessage)
                        <div class="custom-note">
                            <strong>Note from COMSOC Officers:</strong><br>
                            {{ $customMessage }}
                        </div>
                    @endif

                    <!-- Proposal Metadata -->
                    <div class="meta-box">
                        <table class="meta-table">
                            <tr>
                                <td class="meta-label">Transaction Ref:</td>
                                <td class="meta-value" style="font-family: monospace; color: #4338ca;">
                                    COL-{{ $partnership->academic_year }}-{{ str_pad((string)$partnership->id, 4, '0', STR_PAD_LEFT) }}
                                </td>
                            </tr>
                            <tr>
                                <td class="meta-label">Partnership Title:</td>
                                <td class="meta-value">{{ $partnership->title }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Collaboration Mode:</td>
                                <td class="meta-value">{{ $partnership->getPartnershipTypeLabel() }}</td>
                            </tr>
                            <tr>
                                <td class="meta-label">Academic Year:</td>
                                <td class="meta-value">{{ $partnership->academic_year }}</td>
                            </tr>
                            @if($partnership->sponsorship_amount > 0)
                                <tr>
                                    <td class="meta-label">Support / Fund:</td>
                                    <td class="meta-value">PHP {{ number_format($partnership->sponsorship_amount, 2) }}</td>
                                </tr>
                            @endif
                            @if($partnership->start_date)
                                <tr>
                                    <td class="meta-label">Proposed Schedule:</td>
                                    <td class="meta-value">
                                        {{ $partnership->start_date->format('M d, Y') }}
                                        @if($partnership->end_date)
                                            to {{ $partnership->end_date->format('M d, Y') }}
                                        @endif
                                    </td>
                                </tr>
                            @endif
                        </table>
                    </div>

                    <!-- Letter Body -->
                    <div style="font-size: 12px; font-weight: 700; color: #64748b; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 6px;">
                        Official Letter Content
                    </div>
                    <div class="letter-body">
{{ $partnership->getDefaultLetterContent() }}
                    </div>

                    <!-- Signatures Section -->
                    <table class="signatures" cellpadding="0" cellspacing="0">
                        <tr>
                            <td class="sig-block" style="width: 50%; vertical-align: top;">
                                <div class="sig-title">Prepared &amp; Proposed By</div>
                                <div class="sig-name">{{ $partnership->creator->name ?? 'Officer' }}</div>
                                <div class="sig-role">{{ $partnership->creator?->getRoleLabel() ?? 'COMSOC Officer' }}</div>
                                <div class="sig-role">Computer Society, CCIS UMak</div>
                            </td>
                            <td class="sig-block" style="width: 50%; vertical-align: top; text-align: right;">
                                <div class="sig-title">Academic Adviser Endorsement</div>
                                @if($partnership->endorsed_by)
                                    <div class="sig-name" style="color: #047857;">✍️ {{ $partnership->endorser->name ?? 'Academic Adviser' }}</div>
                                    <div class="sig-role" style="color: #047857;">Formally Endorsed on {{ $partnership->endorsed_at ? $partnership->endorsed_at->format('M d, Y') : '' }}</div>
                                    <div class="sig-role">Faculty &amp; Student Org Adviser</div>
                                @else
                                    <div style="font-style: italic; color: #94a3b8; font-size: 11px;">Under Formal Adviser Review</div>
                                    <div class="sig-role">CCIS Academic Adviser</div>
                                @endif
                            </td>
                        </tr>
                    </table>

                    <!-- Partner Response Action Card -->
                    <div style="background: linear-gradient(135deg, #eff6ff 0%, #f5f3ff 100%); border: 2px solid #c7d2fe; border-radius: 14px; padding: 22px; margin-top: 28px; text-align: center;">
                        <h4 style="margin: 0 0 6px 0; color: #1e1b4b; font-size: 15px; font-weight: 800;">
                            Official Response &amp; Letter Acceptance
                        </h4>
                        <p style="margin: 0 0 16px 0; color: #475569; font-size: 12px; line-height: 1.5;">
                            Please confirm your organization's participation or submit feedback directly through our secure online portal. No account login required.
                        </p>
                        
                        <div style="display: inline-block;">
                            <a href="{{ $partnership->getResponseUrl() }}" style="display: inline-block; background-color: #059669; color: #ffffff !important; font-size: 13px; font-weight: 800; text-decoration: none; padding: 13px 26px; border-radius: 10px; margin: 4px; box-shadow: 0 2px 4px rgba(5,150,105,0.2);">
                                ✓ Accept &amp; Confirm Proposal Online
                            </a>
                            <a href="{{ $partnership->getResponseUrl() }}" style="display: inline-block; background-color: #4338ca; color: #ffffff !important; font-size: 13px; font-weight: 700; text-decoration: none; padding: 13px 22px; border-radius: 10px; margin: 4px;">
                                Review &amp; Respond →
                            </a>
                        </div>
                    </div>
                </td>
            </tr>

            <!-- Footer -->
            <tr>
                <td class="footer">
                    <strong>University of Makati — Computer Society (COMSOC)</strong><br>
                    College of Computing and Information Sciences<br>
                    J.P. Rizal Ext., West Rembo, Taguig City / Makati City, Philippines<br>
                    <span style="font-size: 10px; color: #94a3b8; display: inline-block; margin-top: 8px;">
                        This is an official automated communication from the COMSOC Hub Collaboration Portal.
                    </span>
                </td>
            </tr>
        </table>
    </div>
</body>
</html>
