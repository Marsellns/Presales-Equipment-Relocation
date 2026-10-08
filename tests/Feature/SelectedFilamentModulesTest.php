<?php

namespace Tests\Feature;

use App\Filament\Modules\ReportModuleRegistry;
use Tests\TestCase;

class SelectedFilamentModulesTest extends TestCase
{
    public function test_report_panel_contains_only_the_requested_module_groups(): void
    {
        $this->assertSame([
            'simaster-infrastructure',
            'simaster-equipment-relocation',
            'simaster-po-monitoring',
        ], array_map(
            static fn ($plugin): string => $plugin->getId(),
            ReportModuleRegistry::plugins(),
        ));

        $this->assertCount(15, ReportModuleRegistry::pages());
    }
}
