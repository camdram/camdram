<?php

namespace Acts\CamdramApiBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class ActsCamdramApiBundle extends Bundle
{
    public function getParent()
    {
        return 'FOSOAuthServerBundle';
    }
}
