<?php

namespace Opsource\QueryAdapter\Commands;

interface QueryAdapterCommandBuilderIfc
{
    public function handle(): int;
    public function getDestinationFilePath(): string;
    public function getFileName(): string;
    public function getTemplateContents();
    public function getIndicator(): string;
//    public function setJob(): string;
}
