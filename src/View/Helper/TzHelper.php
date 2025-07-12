<?php
declare(strict_types=1);

namespace CakeTezos\View\Helper;

use Cake\View\Helper;

class TzHelper extends Helper
{
    /**
     * Returns shortened tz address for display
     *
     * @param string $address
     * @return string
     */
    public function shortenAddress(string $address): string
    {
        return substr($address, 0, 6) . '...' . substr($address, strlen($address) - 6);
    }
}
