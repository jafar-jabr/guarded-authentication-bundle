<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Signer;

/**
 * Interface SignerInterface.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
interface SignerInterface
{
    /**
     * Signs the $input with the $key, after hashing it.
     *
     * @param string          $input
     * @param resource|string $key
     *
     * @return string|null
     */
    public function sign($input, $key);

    /**
     * Verifies that the input correspond to the $signature decrypted with the
     * given public $key.
     *
     * @param resource|string $key
     * @param string          $signature
     * @param string          $input
     *
     * @return bool
     */
    public function verify($key, $signature, $input);
}
