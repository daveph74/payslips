<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Add detailed payroll fields from CSV
            $table->decimal('total_earnings', 10, 2)->default(0)->after('bonus');
            $table->decimal('social_security_system', 10, 2)->default(0)->after('deductions');
            $table->decimal('philhealth', 10, 2)->default(0)->after('social_security_system');
            $table->decimal('pag_ibig', 10, 2)->default(0)->after('philhealth');
            $table->decimal('withholding_tax', 10, 2)->default(0)->after('pag_ibig');
            $table->decimal('loans', 10, 2)->default(0)->after('withholding_tax');
            $table->decimal('unpaid_absences_tardiness', 10, 2)->default(0)->after('loans');
            $table->decimal('others_authorized_deductions', 10, 2)->default(0)->after('unpaid_absences_tardiness');
            $table->decimal('total_deductions', 10, 2)->default(0)->after('others_authorized_deductions');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('payrolls', function (Blueprint $table) {
            // Drop the added fields
            $table->dropColumn([
                'total_earnings',
                'social_security_system',
                'philhealth',
                'pag_ibig',
                'withholding_tax',
                'loans',
                'unpaid_absences_tardiness',
                'others_authorized_deductions',
                'total_deductions'
            ]);
        });
    }
};
