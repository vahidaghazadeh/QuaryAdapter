<?php

namespace packages\Opsource\QueryAdapter\src\Contracts;

use Illuminate\Config\Repository;
use Illuminate\Contracts\Container\Container;
use Illuminate\Support\Collection;

interface SearchEngineBrokerManager
{
    public function getConnections(): Collection;
    public function getConfig(): Repository;
    public function getApp(): Container;
    public function getChannels(): Collection;
    public function resolveDefaultConfigName(): string;
    public function resolveConnection(): string;
    public function publisher(): string;
    public function consumer(): string;
    public function resolveChannelId(): string;
    public function resolveChannel(): string;
    public function makeConnection(): string;
    public function getConfigKey(): string;
    public function setConfigKey(string $configKey): void;
}
