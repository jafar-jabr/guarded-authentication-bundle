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
 */
class KeysGeneratorCommand extends Command
{
    protected static $defaultName = 'jafar:generate-keys';

    private $keysDir;

    public function __construct(string $keys_dir = '')
    {
        $this->keysDir = $keys_dir;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jafar:generate-keys')
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
        $key_directory = $this->prepareTheRoute();
        $privateKey = openssl_pkey_new([
            'private_key_bits' => 4096,
            'private_key_type' => OPENSSL_KEYTYPE_RSA,
        ]);
        openssl_pkey_export($privateKey, $privkey, $passPhrase);
        $pubkey = openssl_pkey_get_details($privateKey);
        $pubkey = $pubkey['key'];
        file_put_contents($key_directory.'private.pem', $privkey);
        file_put_contents($key_directory.'public.pem', $pubkey);
        $output->writeln('<info>private and public keys generated successfully.</info>');

        return 0;
    }

    /**
     * @return string
     */
    private function prepareTheRoute()
    {
        if (!is_dir($this->keysDir) || !is_readable($this->keysDir)) {
            mkdir($this->keysDir, 0777);
        }

        return $this->keysDir;
    }
}
