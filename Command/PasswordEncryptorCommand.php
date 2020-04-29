<?php

namespace Jafar\Bundle\GuardedAuthenticationBundle\Command;

use Exception;
use RuntimeException;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use Symfony\Component\Console\Question\Question;
use Symfony\Component\Console\Style\SymfonyStyle;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

/**
 * Class PasswordEncryptorCommand.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class PasswordEncryptorCommand extends Command
{
    protected static $defaultName = 'jafar:encrypt-password';

    /** @var UserPasswordEncoderInterface */
    private $encoder;

    public function __construct(UserPasswordEncoderInterface $encoder)
    {
        $this->encoder = $encoder;
        parent::__construct();
    }

    /**
     * {@inheritdoc}
     */
    protected function configure()
    {
        $this
            ->setName('jafar:encrypt-password')
            ->setDescription('encrypt a password for first use before to have the regisration')
            ->setHelp('encrypt a password for first use before to have the regisration');
    }

    /**
     * {@inheritdoc}
     *
     * @throws Exception
     */
    protected function execute(InputInterface $input, OutputInterface $output)
    {
        $io = new SymfonyStyle($input, $output);
        $io->title('Thank you for using Jafar:GuardedAuthenticationBundle');
        $question = new Question('Please enter password to be encrypted ? ');
        $question->setHiddenFallback(false);
        $question->setValidator(function ($answer) {
            if (strlen($answer) < 6) {
                throw new RuntimeException('The password can not be less than 6 characters');
            } elseif (strlen($answer) > 50) {
                throw new RuntimeException('The password can not be more than 50 characters');
            }

            return $answer;
        });
        $helper        = $this->getHelper('question');
        $plainPassword = $helper->ask($input, $output, $question);
        $user          = null;
        if (class_exists('\App\Entity\User')) {
            $user = new \App\Entity\User();
        } elseif (class_exists('\App\Entity\Users')) {
            $user = new \App\Entity\Users();
        } else {
            throw new Exception('No User Entity found, searched in \'App\Entity\User\' and \'App\Entity\Users\'');
        }
        $enc = $this->encoder->encodePassword(
            $user,
            $plainPassword
        );
        $output->writeln('<info>'.$enc.'</info>');

        return 1;
    }
}
