<?php

namespace taskSymfony\taskBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class taskSymfonytaskBundle extends Bundle
{
    static public function escapeItem($item)
    {
        return htmlspecialchars($item);
    }
}
