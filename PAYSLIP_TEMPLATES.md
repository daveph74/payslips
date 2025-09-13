# Payslip Templates Guide

## Available Templates

### 1. OutWorx Template (Default)
**File:** `resources/views/payslips/outworx-template.blade.php`

**Features:**
- Clean, professional OutWorx company design
- Monochromatic color scheme (black text on white background)
- Company logo placeholder with orange/black gradient
- Structured sections: Header, Employee Info, Earnings, Deductions, Take Home Pay
- Philippine payroll standard deductions (SSS, PhilHealth, Pag-ibig)
- PHP currency formatting (₱)
- Professional table layouts with borders

**Layout:**
```
┌─────────────────────────────────────┐
│ [Logo] OutWorx                      │
│ PAYSLIP                             │
│                                     │
│ Employee Name: [Name]               │
│ Employee ID: [ID]                   │
│ Department: [Dept]                  │
│ Pay Period: [Period]                │
│ Pay date: [Date]                    │
│                                     │
│ Earnings                            │
│ ┌─────────────────────────────────┐ │
│ │ Description    │ Amount in PHP  │ │
│ │ Pay period...  │ ₱5,000.00      │ │
│ │ Parking/Transpo│ ₱500.00        │ │
│ │ Holiday Pay    │ ₱200.00        │ │
│ │ Total Earnings │ ₱5,700.00      │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Deductions                          │
│ ┌─────────────────────────────────┐ │
│ │ Description    │ Amount in PHP  │ │
│ │ Social Security│ ₱550.00        │ │
│ │ PhilHealth     │ ₱250.00        │ │
│ │ Pag-ibig       │ ₱100.00        │ │
│ │ Withholding Tax│ ₱800.00        │ │
│ │ Loans          │ ₱300.00        │ │
│ │ Unpaid Absences│ ₱200.00        │ │
│ │ Others         │ ₱320.00        │ │
│ │ Total Deductions│ ₱2,520.00     │ │
│ └─────────────────────────────────┘ │
│                                     │
│ Take Home Pay for the Period        │
│ ┌─────────────────────────────────┐ │
│ │ Take Home Pay...│ ₱3,180.00     │ │
│ └─────────────────────────────────┘ │
└─────────────────────────────────────┘
```

### 2. Modern Template
**File:** `resources/views/payslips/template.blade.php`

**Features:**
- Modern design with gradients and colors
- Responsive layout with cards
- Color-coded sections
- Professional styling
- Company branding space
- Detailed salary breakdown

## Template Management

### View Current Template
```bash
php artisan payslip:template
```

### Switch to OutWorx Template
```bash
php artisan payslip:template outworx-template
php artisan config:clear
```

### Switch to Modern Template
```bash
php artisan payslip:template template
php artisan config:clear
```

## Configuration

### Environment Variables
Add to your `.env` file:
```env
PAYSLIP_TEMPLATE=outworx-template
PAYSLIP_COMPANY_NAME=OutWorx
PAYSLIP_CURRENCY_SYMBOL=₱
PAYSLIP_CURRENCY_CODE=PHP
```

### Config File
Edit `config/payslip.php` to customize:
- Company information
- Currency settings
- Deduction categories
- Template selection

## Customization

### Company Logo
To add your company logo to the OutWorx template:

1. **Replace the CSS logo placeholder:**
```css
.logo-icon {
    background-image: url('path/to/your/logo.png');
    background-size: contain;
    background-repeat: no-repeat;
    background-position: center;
}
```

2. **Or use an image tag:**
```html
<div class="company-logo">
    <img src="{{ asset('images/company-logo.png') }}" alt="Company Logo" style="height: 40px;">
    <div class="company-name">Your Company</div>
</div>
```

### Colors and Styling
Modify the CSS in the template file to match your brand:
- Change font families
- Update color schemes
- Adjust spacing and layout
- Customize table styles

### Deduction Categories
Update the deduction categories in `config/payslip.php`:
```php
'deductions' => [
    'sss' => [
        'name' => 'Social Security System',
        'rate' => 0.11,
    ],
    // Add more categories...
],
```

## Testing Templates

### Generate Sample PDFs
```bash
# Generate with current template
php artisan tinker --execute="
\$payroll = App\Models\Payroll::with('employee')->first();
if (\$payroll) {
    \$pdf = Barryvdh\DomPDF\Facade\Pdf::loadView('payslips.outworx-template', compact('payroll'));
    \$pdf->setPaper('A4', 'portrait');
    \$pdf->save('storage/app/sample_outworx.pdf');
    echo 'Sample created: storage/app/sample_outworx.pdf';
}
"
```

### Compare Templates
1. Switch to OutWorx template and generate a PDF
2. Switch to Modern template and generate another PDF
3. Compare the outputs to see the differences

## Best Practices

1. **Always test** new templates with real data
2. **Keep backups** of working templates
3. **Use version control** for template changes
4. **Document customizations** for team members
5. **Test PDF generation** on different devices/browsers

## Troubleshooting

### Template Not Found
- Check template file exists in `resources/views/payslips/`
- Verify template name in config
- Run `php artisan config:clear`

### Styling Issues
- Check CSS syntax in template file
- Verify font availability
- Test with different PDF viewers

### Data Not Displaying
- Check variable names in template
- Verify data is being passed correctly
- Check for typos in field names
