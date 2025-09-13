<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class PayslipTemplateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'payslip:template {template?}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Set or view the payslip template';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $template = $this->argument('template');

        if (!$template) {
            // Show current template
            $currentTemplate = config('payslip.template', 'outworx-template');
            $this->info("Current payslip template: {$currentTemplate}");
            $this->line('');
            $this->line('Available templates:');
            $this->line('- outworx-template (OutWorx company style)');
            $this->line('- template (Default modern style)');
            $this->line('');
            $this->line('Usage: php artisan payslip:template <template-name>');
            return 0;
        }

        $availableTemplates = ['outworx-template', 'template'];

        if (!in_array($template, $availableTemplates)) {
            $this->error("Invalid template: {$template}");
            $this->line('Available templates: ' . implode(', ', $availableTemplates));
            return 1;
        }

        // Update .env file
        $envFile = base_path('.env');
        $envContent = file_get_contents($envFile);

        // Remove existing PAYSLIP_TEMPLATE line
        $envContent = preg_replace('/^PAYSLIP_TEMPLATE=.*$/m', '', $envContent);

        // Add new PAYSLIP_TEMPLATE line
        $envContent .= "\nPAYSLIP_TEMPLATE={$template}\n";

        file_put_contents($envFile, $envContent);

        $this->info("Payslip template set to: {$template}");
        $this->line('Please run: php artisan config:clear');

        return 0;
    }
}
