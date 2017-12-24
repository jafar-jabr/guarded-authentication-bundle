<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/21/2017
 * Time: 9:33 PM
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWT\KeyLoader;

interface KeyLoaderInterface
{
    /**
     * Loads a key from a given type (public or private).
     *
     * @param resource|string
     *
     * @return resource|string
     */
    public function loadKey($type);

    /**
     * @return string
     */
    public function getPassPhrase();
}