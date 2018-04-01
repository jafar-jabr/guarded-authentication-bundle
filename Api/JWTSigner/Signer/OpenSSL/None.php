<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Signer\OpenSSL;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Signer\SignerInterface;

/**
 * Class None.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class None implements SignerInterface
{
    /**
     * {@inheritdoc}
     */
    public function sign($input, $key)
    {
        return '';
    }

    /**
     * {@inheritdoc}
     */
    public function verify($key, $signature, $input)
    {
        return $signature === '';
    }
}
