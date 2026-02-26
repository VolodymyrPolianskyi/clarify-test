<?php

namespace App\Exception;

class UnsupportedCarrierException extends \InvalidArgumentException
{
    public function __construct(string $carrier)
    {
        parent::__construct(sprintf('Unsupported carrier: "%s"', $carrier));
    }
}
