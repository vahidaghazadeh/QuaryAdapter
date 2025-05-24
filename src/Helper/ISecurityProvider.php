<?php

namespace Opsource\QueryAdapter\Helper;

interface ISecurityProvider
{
    /**
     * Sign serialized closure
     * @param  string  $closure
     * @return array
     */
    public function sign(string $closure): array;

    /**
     * Verify signature
     * @param  array  $data
     * @return bool
     */
    public function verify(array $data): bool;
}
