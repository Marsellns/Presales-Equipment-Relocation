<?php

namespace Tests\Feature;

use App\Filament\Modules\ReportModuleRegistry;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\Concerns\CreatesUsersWithRoles;
use Tests\TestCase;

class SelectedFilamentModulesTest extends TestCase
{
    use CreatesUsersWithRoles;
    use RefreshDatabase;

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

    public function test_requested_module_pages_render_inside_the_report_panel(): void
    {
        $admin = $this->userWithRole('admin');

        foreach ([
            route('infrastruktur.index'),
            route('equipment-relocation.index'),
            route('po-hq.index'),
            route('po-varcost.index'),
            route('presales.index'),
        ] as $url) {
            $this->actingAs($admin)->get($url)
                ->assertOk()
                ->assertSee('SIMASTER Modules');
        }
    }
}