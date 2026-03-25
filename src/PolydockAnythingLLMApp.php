<?php

declare(strict_types=1);

namespace Amazeeio\PolydockAppAnythingLLM;

// use Filament\Forms;
// use Filament\Infolists;
use Filament\Forms\Components\Component;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppInstanceFields;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppStoreFields;
use FreedomtechHosting\PolydockApp\Attributes\PolydockAppTitle;
use FreedomtechHosting\PolydockApp\Contracts\HasAppInstanceFormFields;
use FreedomtechHosting\PolydockApp\Contracts\HasStoreAppFormFields;
use FreedomtechHosting\PolydockAppAmazeeioGeneric\PolydockApp as GenericPolydockApp;

#[PolydockAppTitle('AnythingLLM App')]
#[PolydockAppStoreFields]
#[PolydockAppInstanceFields]
class PolydockAnythingLLMApp extends GenericPolydockApp implements HasAppInstanceFormFields, HasStoreAppFormFields
{
    public static string $version = '0.1.0';

    /**
     * @return array<Component>
     */
    #[\Override]
    public static function getStoreAppFormSchema(): array
    {
        return [];
    }

    /**
     * @return array<\Filament\Infolists\Components\Component>
     */
    #[\Override]
    public static function getStoreAppInfolistSchema(): array
    {
        return [];
    }

    /**
     * @return array<Component>
     */
    #[\Override]
    public static function getAppInstanceFormSchema(): array
    {
        return [];
    }

    /**
     * @return array<\Filament\Infolists\Components\Component>
     */
    #[\Override]
    public static function getAppInstanceInfolistSchema(): array
    {
        return [];
    }
}
