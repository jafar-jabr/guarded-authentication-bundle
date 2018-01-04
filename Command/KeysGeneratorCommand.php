<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class KeysGeneratorCommand
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Command
 */
class KeysGeneratorCommand extends Command
{
    protected static $defaultName = 'jafar:generate-keys';

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setDescription('Generate private and public key for JWT encryption')
            ->setHelp('Generate password protected private and public key for JWT encryption')
            ->addArgument('passPhrase', InputArgument::REQUIRED, 'Pass phrase for Openssl keysPair.');
    }

    /**
     * {@inheritdoc}
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $passPhrase = $input->getArgument('passPhrase');
        $key_directory = dirname(dirname(__FILE__)) . '\Api\JWT\KeyLoader\Keys';
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($privateKey, $privkey, $passPhrase);
        $pubkey = openssl_pkey_get_details($privateKey);
        $pubkey = $pubkey["key"];
        file_put_contents($key_directory.'\private.pem', $privkey);
        file_put_contents($key_directory.'\public.pem', $pubkey);
        $output->writeln('<info>private and public keys generated successfully.</info>');
        return 0;
    }
}
