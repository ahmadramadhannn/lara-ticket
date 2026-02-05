<?php

return [
    App\Providers\AppServiceProvider::class,
    App\Providers\Filament\CompanyAdminPanelProvider::class,
    App\Providers\Filament\OperatorPanelProvider::class, // Legacy: for backward compatibility redirects
    App\Providers\Filament\SuperAdminPanelProvider::class,
    App\Providers\Filament\TerminalAdminPanelProvider::class,
];
