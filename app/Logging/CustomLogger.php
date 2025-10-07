<?php

namespace App\Logging;

use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Monolog\Formatter\LineFormatter;

class CustomLogger
{
    public function __invoke(array $config)
    {
        $logger = new Logger('subscription-platform');

        $formatter = new LineFormatter(
            "[%datetime%] %channel%.%level_name%: %message% %context%\n",
            'Y-m-d H:i:s'
        );

        $handler = new StreamHandler(
            storage_path('logs/custom.log'),
            Logger::DEBUG
        );

        $handler->setFormatter($formatter);
        $logger->pushHandler($handler);

        return $logger;
    }
}
