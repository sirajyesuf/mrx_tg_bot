<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ClientStatus extends Enum
{
    const Pending = 1;
    const Approve = 2;
    const Deny = 3;
}
