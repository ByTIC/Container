<?php

namespace Nip\Container\ServiceProviders;

use Nip\Config\Config;
use Nip\Container\Container;

/**
 * Class ServiceProviderAwareTrait
 * @package Nip\Container\ServiceProviders
 */
trait ServiceProviderAwareTrait
{
    /**
     * All of the registered service providers.
     *
     * @var ProviderRepository
     */
    protected $providerRepository = null;

    public function registerConfiguredProviders()
    {
        $providers = $this->getConfiguredProviders();
        foreach ($providers as $provider) {
            $this->getProviderRepository()->add($provider);
        }

        return $this->getProviderRepository()->register();
    }

    /**
     * @return ProviderRepository
     */
    public function getProviderRepository()
    {
        if ($this->providerRepository === null) {
            $this->providerRepository = new ProviderRepository();
            $this->providerRepository->setContainer($this->getContainer());
        }

        return $this->providerRepository;
    }

    /**
     * @return array
     */
    public function getConfiguredProviders()
    {
        return $this->getConfigProvidersValue($this->getGenericProviders());
    }

    /**
     * @param null $default
     * @return array|null
     * @throws \Exception
     */
    protected function getConfigProvidersValue($default = null)
    {
        if (function_exists('config') === false) {
            return $default;
        }
        if (function_exists('app') === false && !(Container::getInstance() instanceof Container)) {
            return $default;
        }
        $container = function_exists('app') ? app() : Container::getInstance();
        if ($container->has('config') == false) {
            return $default;
        }
        $value = config('app.providers');
        if ($value instanceof Config) {
            $value = $value->toArray();
        }
        if (empty($value)) {
            return $default;
        }
        return $value;
    }

    /**
     * @return array
     */
    public function getGenericProviders()
    {
        return [];
    }

    public function bootProviders()
    {
        $this->getProviderRepository()->boot();
    }
}
