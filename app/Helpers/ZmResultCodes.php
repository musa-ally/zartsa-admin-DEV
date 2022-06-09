<?php

namespace App\Helpers;

class ZmResultCodes
{

    const FAILED_CHECK_BILL_ITEMS = 0x1000;
    const FAILED_INVALID_AMOUNT = -0x1001;
    const FAILED_NETWORK_ERROR = -0x1002;
    const FAILED_SIGNATURE_ERROR = -0x1003;
    const FAILED_NO_RESPONSE = -0x1004;
}
