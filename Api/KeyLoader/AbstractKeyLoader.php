<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class AbstractKeyLoader
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Api\KeyLoader
 */
abstract class AbstractKeyLoader implements KeyLoaderInterface
{
    const TYPE_PUBLIC = 'public';

    const TYPE_PRIVATE = 'private';

    /**
     * @var string
     */
    private $privateKey;

    /**
     * @var string
     */
    private $publicKey;

    /**
     * @var string
     */
    private $passPhrase;

    /**
     * AbstractKeyLoader constructor.
     *
     * @param string $passPhrase
     * @param string $keys_dir
     */
    public function __construct(string $passPhrase, string $keys_dir)
    {
        $this->privateKey = $keys_dir.'private.pem';
        $this->publicKey  = $keys_dir.'public.pem';
        $this->passPhrase = $passPhrase;
    }

    /**
     * {@inheritdoc}
     */
    public function getPassPhrase()
    {
        return $this->passPhrase;
    }

    /**
     * @param string $type One of "public" or "private"
     *
     * @return null|string The path of the key
     *
     * @throws \InvalidArgumentException If the given type is not valid
     */
    protected function getKeyPath($type)
    {
        if (!in_array($type, [self::TYPE_PUBLIC, self::TYPE_PRIVATE])) {
            throw new \InvalidArgumentException(
                sprintf('The key type must be "public" or "private", "%s" given.', $type)
            );
        }
        $path = null;
        if (self::TYPE_PUBLIC === $type) {
            $path = $this->publicKey;
        }
        if (self::TYPE_PRIVATE === $type) {
            $path = $this->privateKey;
        }
        if (!is_file($path) || !is_readable($path)) {
            throw new \RuntimeException(
                sprintf(
                    '%s key "%s" does not exist or is not readable. 
                    Please run jafar:generate-keys again to regenerate the kys!',
                    ucfirst($type),
                    $path,
                    $type
                )
            );
        }

        return $path;
    }
}
