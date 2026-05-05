<?php

declare(strict_types=1);

namespace app;

use Tempest\Http\Response;
use Tempest\Http\Responses\Ok;
use Tempest\Router\Get;

final readonly class HelloController
{
    #[Get(uri: '/')]
    public function index(): Response
    {
        static $count = 0;

        $count++;

        return new Ok(
            body: sprintf(
                "pid %d, request %d, sapi %s\n",
                getmypid(),
                $count,
                PHP_SAPI,
            ),
        );
    }
}
