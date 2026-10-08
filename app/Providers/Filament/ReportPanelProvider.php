<?php

namespace App\Providers\Filament;

use App\Filament\Modules\ModuleNavigation;
use App\Filament\Modules\ReportModuleRegistry;
use Filament\Http\Middleware\Authenticate;
use Filament\Http\Middleware\AuthenticateSession;
use Filament\Http\Middleware\DisableBladeIconComponents;
use Filament\Http\Middleware\DispatchServingFilamentEvent;
use Filament\Navigation\NavigationBuilder;
use Filament\Panel;
use Filament\PanelProvider;
use Filament\Support\Colors\Color;
use Illuminate\Cookie\Middleware\AddQueuedCookiesToResponse;
use Illuminate\Cookie\Middleware\EncryptCookies;
use Illuminate\Foundation\Http\Middleware\PreventRequestForgery;
use Illuminate\Routing\Middleware\SubstituteBindings;
use Illuminate\Session\Middleware\StartSession;
use Illuminate\View\Middleware\ShareErrorsFromSession;

class ReportPanelProvider extends PanelProvider
{
    public function panel(Panel $panel): Panel
    {
        return $panel
            ->default()
            ->id('report')
            ->path('report')
            ->login()
            ->authGuard('web')
            ->brandName('SIMASTER Modules')
            ->colors(['primary' => Color::Red])
            ->maxContentWidth('full')
            ->sidebarCollapsibleOnDesktop()
            ->homeUrl(fn () => route('infrastruktur.index'))
            ->plugins(ReportModuleRegistry::plugins())
            ->navigation(fn (NavigationBuilder $builder) => $builder->groups(ModuleNavigation::groups()))
            ->renderHook('panels::head.end', fn () => view('filament.partials.module-assets'))
            ->middleware([
                EncryptCookies::class,
                AddQueuedCookiesToResponse::class,
                StartSession::class,
                AuthenticateSession::class,
                ShareErrorsFromSession::class,
                PreventRequestForgery::class,
                SubstituteBindings::class,
                DisableBladeIconComponents::class,
                DispatchServingFilamentEvent::class,
            ])
            ->authMiddleware([Authenticate::class], isPersistent: true);
    }
}
