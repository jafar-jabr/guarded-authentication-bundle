<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */
namespace Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSRefresher;

use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSEncoder\JWSEncoderInterface;
use Jafar\Bundle\GuardedAuthenticationBundle\Api\JWSExtractor\TokenExtractor;
use Symfony\Component\HttpFoundation\Request;
use Jafar\Bundle\GuardedAuthenticationBundle\Exception\ApiException;

/**
 * Class JWSRefresher
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class JWSRefresher implements JWSRefresherInterface
{

    /**
     * @var JWSEncoderInterface
     */
    private $encoder;

    /**
     * JWSRefresher constructor.
     *
     * @param JWSEncoderInterface $JWSEncoder
     */
    public function __construct(JWSEncoderInterface $JWSEncoder)
    {
        $this->encoder = $JWSEncoder;
    }

    /**
     * {@inheritdoc}
     *
     */
    public function decode(Request $request)
    {
        if ($request->headers->has('refresh-token')) {
            $extractor    = new TokenExtractor('', 'refresh-token');
            $token        = $extractor->extract($request);
            try {
                return  $this->encoder->decode($token);
            } catch (ApiException $e) {
                throw new ApiException(
                    'Invalid refresh token',
                    'An error occurred while trying 
                to decode the JWT token. Please verify your configuration (private key/passPhrase)',
                    $e
                );
            }
        } else {
            throw new ApiException(
                'refresh token not provided',
                'An error occurred while trying 
                to extract the refresh token. Please check your request header'
            );
        }
    }
}
