<?php

namespace Siwymilek\CrudBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

class CrudBundle extends Bundle
{
    public function getContainerExtension()
    {
        if (null === $this->extension) {
            $this->extension = new CoreCrudExtension();
        }
        return $this->extension;
    }
}
