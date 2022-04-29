<?php

namespace App\Enums;

use BenSampo\Enum\Enum;

/**
 * @method static static OptionOne()
 * @method static static OptionTwo()
 * @method static static OptionThree()
 */
final class ClaimStatus extends Enum
{
    const Pending = 1;
    const Deny = 2;
    const Apply = 3;
}
