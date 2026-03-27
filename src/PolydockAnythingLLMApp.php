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
use FreedomtechHosting\PolydockApp\Enums\PolydockAppInstanceStatus;
use FreedomtechHosting\PolydockApp\PolydockAppInstanceInterface;
use FreedomtechHosting\PolydockApp\PolydockAppVariableDefinitionBase;
use FreedomtechHosting\PolydockAppAmazeeioGeneric\PolydockAiApp as GenericPolydockAiApp;

#[PolydockAppTitle('AnythingLLM App')]
#[PolydockAppStoreFields]
#[PolydockAppInstanceFields]
class PolydockAnythingLLMApp extends GenericPolydockAiApp implements HasAppInstanceFormFields, HasStoreAppFormFields
{
    public static string $version = '0.1.1';

    /**
     * @return array<PolydockAppVariableDefinitionBase>
     */
    public static function getAppDefaultVariableDefinitions(): array
    {
        return array_merge(parent::getAppDefaultVariableDefinitions(), [
            new PolydockAppVariableDefinitionBase('amazee-ai-backend-region-id'),
        ]);
    }

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

    /**
     * @throws \FreedomtechHosting\PolydockApp\PolydockAppInstanceStatusFlowException
     */
    #[\Override]
    public function claimAppInstance(PolydockAppInstanceInterface $appInstance): PolydockAppInstanceInterface
    {
        $functionName = __FUNCTION__;
        $logContext = $this->getLogContext($functionName);

        $this->info($functionName.': starting AnythingLLM claim', $logContext);

        $this->validateAppInstanceStatusIsExpected($appInstance, PolydockAppInstanceStatus::PENDING_POLYDOCK_CLAIM);

        $this->setLagoonClientFromAppInstance($appInstance);
        $this->setAmazeeAiBackendClientFromAppInstance($appInstance);

        // Generate JWT_SECRET if not already set
        $jwtSecret = $appInstance->getKeyValue('anythingllm-jwt-secret');
        if (empty($jwtSecret)) {
            $jwtSecret = bin2hex(random_bytes(32));
            $appInstance->storeKeyValue('anythingllm-jwt-secret', $jwtSecret);
            $this->info('Generated new JWT_SECRET for AnythingLLM', $logContext);
        }

        // Set JWT_SECRET as Lagoon variable
        $this->addOrUpdateLagoonProjectVariable($appInstance, 'JWT_SECRET', $jwtSecret, 'GLOBAL');

        // Get AI credentials from backend
        $aiCredentials = $this->getPrivateAICredentialsFromBackend($appInstance);

        // Inject AI credentials as Lagoon variables using the new naming scheme
        $this->addOrUpdateLagoonProjectVariable($appInstance, 'LLM_URL', $aiCredentials['litellm_api_url'], 'GLOBAL');
        $this->addOrUpdateLagoonProjectVariable($appInstance, 'LLM_AI_KEY', $aiCredentials['litellm_token'], 'GLOBAL');
        $this->addOrUpdateLagoonProjectVariable($appInstance, 'EMBEDDING_PROVIDER', 'native', 'GLOBAL');

        // Inject DB credentials
        $this->addOrUpdateLagoonProjectVariable($appInstance, 'DB_HOST', $aiCredentials['database_host'], 'GLOBAL');
        $this->addOrUpdateLagoonProjectVariable($appInstance, 'DB_USER', $aiCredentials['database_username'], 'GLOBAL');
        $this->addOrUpdateLagoonProjectVariable($appInstance, 'DB_PASS', $aiCredentials['database_password'], 'GLOBAL');
        $this->addOrUpdateLagoonProjectVariable($appInstance, 'DB_NAME', $aiCredentials['database_name'], 'GLOBAL');
        $this->addOrUpdateLagoonProjectVariable($appInstance, 'DB_PORT', '5432', 'GLOBAL');

        $this->info('Injected AI credentials (new naming scheme) and JWT_SECRET for AnythingLLM', $logContext);

        return parent::claimAppInstance($appInstance);
    }
}
