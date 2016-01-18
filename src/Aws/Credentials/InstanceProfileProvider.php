<?php
namespace Aws\Credentials;

use GuzzleHttp\Promise;

/**
 * Overrides default instance profile provider to bypass the authentication over fake s3 server
 */
class InstanceProfileProvider
{
     public function __invoke()
    {
        return Promise\coroutine(function () {
            yield new Credentials(
                'mocked',
                'mocked',
                'mocked',
                'mocked'
            );

        });

    }
}
