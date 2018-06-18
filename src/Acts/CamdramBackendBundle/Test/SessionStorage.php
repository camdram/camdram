<?php

namespace Acts\CamdramBackendBundle\Test;

use Symfony\Component\HttpFoundation\Session\Storage\MockFileSessionStorage;

class SessionStorage extends MockFileSessionStorage
{
    /**
     * Override setId. For some reason it gets called in the wrong order during tests, causing an exception
     *
     * @param string $id
     */
    public function setId($id)
    {
        $this->id = $id;
    }
}
