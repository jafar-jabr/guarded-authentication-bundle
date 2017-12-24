<?php
/**
 * Created by PhpStorm.
 * User: Jafar Jabr
 * Date: 12/14/2017
 * Time: 10:07 AM
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Input\InputArgument;

class KeysGeneratorCommand extends Command
{
    protected function configure()
    {
        $this
            ->setName('jafar:generate-keys')
            ->setDescription('Generate private and public key for JWT encryption')
            ->setHelp('Generate password protected private and public key for JWT encryption')
            ->addArgument('passPhrase', InputArgument::REQUIRED, 'Pass phrase for Openssl keysPair.');
    }

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
        echo $pubkey;
    }
}
