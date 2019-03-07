<?php

declare(strict_types=1);

namespace Alsciende\Behat;

use Behat\Behat\Context\Environment\InitializedContextEnvironment;
use Behat\Behat\Hook\Scope\BeforeScenarioScope;

trait DataStoreContextGatheringTrait
{
    /**
     * @var DataStoreContext
     */
    public $dataStoreContext;

    /**
     * @BeforeScenario
     */
    public function gatherContexts(BeforeScenarioScope $scope)
    {
        $environment = $scope->getEnvironment();
        if ($environment instanceof InitializedContextEnvironment) {
            if ($environment->hasContextClass(DataStoreContext::class)) {
                $context = $environment->getContext(DataStoreContext::class);
                if ($context instanceof DataStoreContext) {
                    $this->dataStoreContext = $context;
                } else {
                    throw new \RuntimeException();
                }
            } else {
                throw new \LogicException(
                    sprintf('Context %s is required in suite "%s"',
                        DataStoreContext::class,
                        $scope->getSuite()->getName()
                    )
                );
            }
        }
    }
}
