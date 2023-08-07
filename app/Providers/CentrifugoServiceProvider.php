<?php

namespace App\Providers;

use Illuminate\Contracts\Support\DeferrableProvider;
use Illuminate\Foundation\Application;
use Illuminate\Support\ServiceProvider;
use phpcent\Client;

class CentrifugoServiceProvider extends ServiceProvider implements DeferrableProvider
{
    /**
     * Register services.
     */
    public function register(): void
    {
        $this->app->singleton(Client::class, function (Application $app) {
            $config = $app->make('config')->get('centrifugo');

            $url = $config['url'];
            $secret_key = $config['token_hmac_secret_key'];
            $api_key = $config['api_key'];
            $connection_timeout = $config['connection_timeout'];
            $timeout = $config['timeout'];
            $use_assoc = $config['use_assoc'];
            $safety = $config['safety'];
            $cert = $config['cert'];
            $ca_path = $config['ca_path'];

            $centrifugo = new Client($url, $api_key, $secret_key);

            if ($connection_timeout) {
                $centrifugo->setConnectTimeoutOption($connection_timeout);
            }

            if ($timeout) {
                $centrifugo->setTimeoutOption($timeout);
            }

            if ($use_assoc) {
                $centrifugo->setUseAssoc($use_assoc);
            }

            if ($safety) {
                $centrifugo->setSafety($safety);
            }

            if ($cert) {
                $centrifugo->setCert($cert);
            }

            if ($ca_path) {
                $centrifugo->setCAPath($ca_path);
            }


            return $centrifugo;
        });
    }

    public function provides(): array
    {
        return [Client::class];
    }
}
