<!DOCTYPE html>
<html><head><title>EFD Receipt #{{ $transaction->receipt_number }}</title>
<style>body{font-family:monospace;font-size:14px;max-width:300px;margin:auto;padding:20px}
h2{text-align:center}.line{border-top:1px dashed #000;margin:8px 0}
table{width:100%}td{padding:2px 0}.right{text-align:right}
.footer{text-align:center;margin-top:16px;font-size:11px;color:#666}
@media print{body{margin:0;padding:10px}.no-print{display:none}}</style>
</head>
<body>
<div class="no-print" style="text-align:center;margin-bottom:16px">
    <button onclick="window.print()" class="btn-primary">Print Receipt</button>
    <button onclick="history.back()" class="btn-ghost">Back</button>
</div>
<h2>INSHOTEL</h2>
<p style="text-align:center">TIN: {{ $transaction->tin ?? '000-000-000' }}<br>
{{ now()->format('d/m/Y H:i') }}</p>
<div class="line"></div>
<p style="text-align:center"><strong>EFD RECEIPT</strong></p>
<p style="text-align:center">{{ $transaction->receipt_number }}</p>
<div class="line"></div>
<table>
    <tr><td>Amount</td><td class="right">{{ number_format($transaction->amount, 2) }}</td></tr>
    <tr><td>VAT (18%)</td><td class="right">{{ number_format($transaction->vat, 2) }}</td></tr>
    <tr><td><strong>Total</strong></td><td class="right"><strong>{{ number_format($transaction->amount + $transaction->vat, 2) }}</strong></td></tr>
</table>
<div class="line"></div>
<p style="text-align:center">Status: {{ strtoupper($transaction->status) }}</p>
<div class="footer">
    <p>Electronic Fiscal Device<br>Authorized by TRA</p>
</div>
</body></html>
