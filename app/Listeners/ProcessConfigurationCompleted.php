<?php
namespace App\Listeners;

use App\Events\ConfigurationCompleted;

class ProcessConfigurationCompleted
{
    public function handle(ConfigurationCompleted $event)
    {
        // Access the configuration data from the event
        $configuration = $event->configuration;

        // Implement your logic here
        \Log::info('Configuration completed', ['configuration' => $configuration]);
    }
}




