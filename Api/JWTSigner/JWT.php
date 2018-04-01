<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Base64\Base64UrlSafeEncoder;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Base64\EncoderInterface;

/**
 * Class JWT.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JWT
{
    /**
     * @var array
     */
    protected $payload;

    /**
     * @var array
     */
    protected $header;

    /**
     * @var EncoderInterface
     */
    protected $encoder;

    /**
     * Constructor.
     *
     * @param array $payload
     * @param array $header
     */
    public function __construct(array $payload, array $header)
    {
        $this->setPayload($payload);
        $this->setHeader($header);
        $this->setEncoder(new Base64UrlSafeEncoder());
    }

    /**
     * @param EncoderInterface $encoder
     *
     * @return $this
     */
    public function setEncoder(EncoderInterface $encoder)
    {
        $this->encoder = $encoder;

        return $this;
    }

    /**
     * Generates the signininput for the current JWT.
     *
     * @return string
     */
    public function generateSigninInput()
    {
        $base64payload = $this->encoder->encode(json_encode($this->getPayload(), JSON_UNESCAPED_SLASHES));
        $base64header  = $this->encoder->encode(json_encode($this->getHeader(), JSON_UNESCAPED_SLASHES));

        return sprintf('%s.%s', $base64header, $base64payload);
    }

    /**
     * Returns the payload of the JWT.
     *
     * @return array
     */
    public function getPayload()
    {
        return $this->payload;
    }

    /**
     * Sets the payload of the current JWT.
     *
     * @param array $payload
     *
     * @return $this
     */
    public function setPayload(array $payload)
    {
        $this->payload = $payload;

        return $this;
    }

    /**
     * Returns the header of the JWT.
     *
     * @return array
     */
    public function getHeader()
    {
        return $this->header;
    }

    /**
     * Sets the header of this JWT.
     *
     * @param array $header
     *
     * @return $this
     */
    public function setHeader(array $header)
    {
        $this->header = $header;

        return $this;
    }
}
