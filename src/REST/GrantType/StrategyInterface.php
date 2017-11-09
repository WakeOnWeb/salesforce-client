<?php

namespace WakeOnWeb\SalesforceClient\REST\GrantType;

use WakeOnWeb\SalesforceClient\REST\Gateway;

interface StrategyInterface
{
    public function buildAccessToken(Gateway $gateway): string;
}
