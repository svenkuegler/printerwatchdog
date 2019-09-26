<?php

namespace App\Service;

use Symfony\Component\DependencyInjection\EnvVarProcessorInterface;

class NullableEnvVarProcessor implements EnvVarProcessorInterface {

    public function getEnv($prefix, $name, \Closure $getEnv)
    {
        try {
            $env = $getEnv($name);
        } catch (\Exception $e) {
            $env = null;
        }
        return $env;
    }

    public static function getProvidedTypes()
    {
        return [
            'nullable' => 'string',
        ];
    }

}