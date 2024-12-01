<?php

namespace Opsource\QueryAdapter\Helper;

class SecurityProvider implements ISecurityProvider
{
    /** @var  string */
    protected string $secret;

    /**
     * SecurityProvider constructor.
     * @param  string  $secret
     */
    public function __construct(string $secret)
    {
        $this->secret = $secret;
    }

    /**
     * @inheritdoc
     */
    public function sign($closure): array
    {
        return array(
            'closure' => $closure,
            'hash' => base64_encode(hash_hmac('sha256', $closure, $this->secret, true)),
        );
    }

    /**
     * @inheritdoc
     */
    public function verify(array $data): bool
    {
        return base64_encode(hash_hmac('sha256', $data['closure'], $this->secret, true)) === $data['hash'];
    }
}
