<?php

namespace Svd\CoreBundle\MimeType;

use Symfony\Component\HttpFoundation\File\MimeType\MimeTypeExtensionGuesser;

/**
 * MimeType
 */
class MimeTypeMatcher extends MimeTypeExtensionGuesser
{
    /**
     * Get matches
     *
     * @return array
     */
    public function getMatches()
    {
        return $this->defaultExtensions;
    }
}
