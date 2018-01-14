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
 * Class LoadedJWS
 */
class LoadedJWS
{
    const VERIFIED = 'verified';

    const EXPIRED = 'expired';

    const INVALID = 'invalid';

    /**
     * @var array
     */
    private $payload;

    /**
     * @var string
     */
    private $state;

    /**
     * @var bool
     */
    private $hasLifetime;

    /**
     * LoadedJWS constructor.
     *
     * @param array $payload
     * @param bool  $isVerified
     * @param bool  $hasLifetime
     */
    public function __construct(array $payload, bool $isVerified, bool $hasLifetime = true)
    {
        $this->payload     = $payload;
        $this->hasLifetime = $hasLifetime;

        if (true === $isVerified) {
            $this->state = self::VERIFIED;
        }
        $this->checkIssuedAt();
        $this->checkExpiration();
    }

    /**
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * @return bool
     */
    public function isVerified()
    {
        return self::VERIFIED === $this->state;
    }

    /**
     * @return bool
     */
    public function isExpired()
    {
        $this->checkExpiration();

        return self::EXPIRED === $this->state;
    }

    /**
     * @return bool
     */
    public function isInvalid()
    {
        return self::INVALID === $this->state;
    }

    /**
     * Ensures that the signature is not expired.
     */
    private function checkExpiration()
    {
        if (!$this->hasLifetime) {
            return null;
        }

        if (!isset($this->payload['exp']) || !is_numeric($this->payload['exp'])) {
            return $this->state = self::INVALID;
        }

        if (0 <= (new \DateTime())->format('U') - $this->payload['exp']) {
            return $this->state = self::EXPIRED;
        }

        return null;
    }

    /**
     * Ensures that the iat claim is not in the future.
     */
    private function checkIssuedAt()
    {
        if (isset($this->payload['iat']) && (int) $this->payload['iat'] > time()) {
            return $this->state = self::INVALID;
        }

        return null;
    }
}
