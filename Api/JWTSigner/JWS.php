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

use InvalidArgumentException;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Base64\Base64Encoder;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Base64\Base64UrlSafeEncoder;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Base64\EncoderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWTSigner\Signer\SignerInterface;

/**
 * Class JWS.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JWS extends JWT
{
    protected $signature;

    protected $isSigned = false;

    protected $originalToken;

    protected $encodedSignature;

    protected $encryptionEngine;

    protected $supportedEncryptionEngine = 'OpenSSL';

    /**
     * Constructor.
     *
     * @param array  $header           An associative array of headers. The value can be any type accepted by json_encode or a JSON serializable object
     * @param string $encryptionEngine
     */
    public function __construct($header = [], $encryptionEngine = 'OpenSSL')
    {
        if ($encryptionEngine !== $this->supportedEncryptionEngine) {
            throw new InvalidArgumentException(sprintf('Encryption engine %s is not supported', $encryptionEngine));
        }

        $this->encryptionEngine = $encryptionEngine;

        parent::__construct([], $header);
    }

    /**
     * Signs the JWS signininput.
     *
     * @param resource|string $key
     * @param null | string   $password
     *
     * @return string
     */
    public function sign($key, $password = null)
    {
        $this->signature = $this->getSigner()->sign($this->generateSigninInput(), $key, $password);
        $this->isSigned  = true;

        return $this->signature;
    }

    /**
     * Returns the signature representation of the JWS.
     *
     * @return string
     */
    public function getSignature()
    {
        if ($this->isSigned()) {
            return $this->signature;
        }

        return;
    }

    /**
     * Checks whether the JSW has already been signed.
     *
     * @return bool
     */
    public function isSigned()
    {
        return (bool) $this->isSigned;
    }

    /**
     * Returns the string representing the JWT.
     *
     * @return string
     */
    public function getTokenString()
    {
        $signinInput = $this->generateSigninInput();

        return sprintf('%s.%s', $signinInput, $this->encoder->encode($this->getSignature()));
    }

    /**
     * Creates an instance of a JWS from a JWT.
     *
     * @param string           $jwsTokenString
     * @param bool             $allowUnsecure
     * @param EncoderInterface $encoder
     * @param string           $encryptionEngine
     *
     * @return JWS
     *
     * @throws \InvalidArgumentException
     */
    public static function load(
        $jwsTokenString,
        $allowUnsecure = false,
        EncoderInterface $encoder = null,
        $encryptionEngine = 'OpenSSL'
    ) {
        if (null === $encoder) {
            $encoder = strpbrk($jwsTokenString, '+/=') ? new Base64Encoder() : new Base64UrlSafeEncoder();
        }

        $parts = explode('.', $jwsTokenString);

        if (3 === count($parts)) {
            $header  = json_decode($encoder->decode($parts[0]), true);
            $payload = json_decode($encoder->decode($parts[1]), true);

            if (is_array($header) && is_array($payload)) {
                if ('none' === strtolower($header['alg']) && !$allowUnsecure) {
                    throw new InvalidArgumentException(sprintf('The token "%s" cannot be validated in a secure context, as it uses the unallowed "none" algorithm', $jwsTokenString));
                }

                $jws = new static($header, $encryptionEngine);

                $jws->setEncoder($encoder)
                    ->setHeader($header)
                    ->setPayload($payload)
                    ->setOriginalToken($jwsTokenString)
                    ->setEncodedSignature($parts[2]);

                return $jws;
            }
        }

        throw new InvalidArgumentException(sprintf('The token "%s" is an invalid JWS', $jwsTokenString));
    }

    /**
     * Verifies that the internal signin input corresponds to the encoded
     * signature previously stored (@see JWS::load).
     *
     * @param resource|string $key
     * @param string          $algo The algorithms this JWS should be signed with. Use it if you want to restrict which algorithms you want to allow to be validated.
     *
     * @return bool
     */
    public function verify($key, $algo = null)
    {
        if (empty($key) || ($algo && $this->header['alg'] !== $algo)) {
            return false;
        }

        $decodedSignature = $this->encoder->decode($this->getEncodedSignature());
        $signinInput      = $this->getSigninInput();

        return $this->getSigner()->verify($key, $decodedSignature, $signinInput);
    }

    /**
     * Get the original token signin input if it exists, otherwise generate the
     * signin input for the current JWS.
     *
     * @return string
     */
    private function getSigninInput()
    {
        $parts = explode('.', $this->originalToken);

        if (count($parts) >= 2) {
            return sprintf('%s.%s', $parts[0], $parts[1]);
        }

        return $this->generateSigninInput();
    }

    /**
     * Sets the original base64 encoded token.
     *
     * @param string $originalToken
     *
     * @return JWS
     */
    private function setOriginalToken($originalToken)
    {
        $this->originalToken = $originalToken;

        return $this;
    }

    /**
     * Returns the base64 encoded signature.
     *
     * @return string
     */
    public function getEncodedSignature()
    {
        return $this->encodedSignature;
    }

    /**
     * Sets the base64 encoded signature.
     *
     * @param string $encodedSignature
     *
     * @return JWS
     */
    public function setEncodedSignature($encodedSignature)
    {
        $this->encodedSignature = $encodedSignature;

        return $this;
    }

    /**
     * Returns the signer responsible to encrypting / decrypting this JWS.
     *
     * @return SignerInterface
     *
     * @throws \InvalidArgumentException
     */
    protected function getSigner()
    {
        $signerClass = sprintf('Jafar\\Bundle\\GuardedAuthenticationBundle\\Api\\JWTSigner\\Signer\\%s\\%s', $this->encryptionEngine, $this->header['alg']);

        if (class_exists($signerClass)) {
            return new $signerClass();
        }

        throw new InvalidArgumentException(
            sprintf("The algorithm '%s' is not supported for %s", $this->header['alg'], $this->encryptionEngine)
        );
    }
}
