<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Tests\Command;

use Jafar\Bundle\GuardedAuthenticationBundle\Command\KeysGeneratorCommand;
use Symfony\Bundle\FrameworkBundle\Console\Application;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;
use Symfony\Component\Console\Tester\CommandTester;

/**
 * Class KeysGeneratorCommandTest.
 *
 * @author Jafar Jabr <jafaronly@yahoo.com>
 */
class KeysGeneratorCommandTest extends KernelTestCase
{
    /**
     * Test command.
     */
    public function testCommand()
    {
        $kernel      = self::bootKernel();
        $application = new Application($kernel);

        $application->add(new KeysGeneratorCommand());

        $command = $application->find('jafar:generate-keys');

        $commandTester = new CommandTester($command);

        // Equals to a user inputting "Test" and hitting ENTER
        $commandTester->setInputs(['Test']);

//        // Equals to a user inputting "This", "That" and hitting ENTER
//        // This can be used for answering two separated questions for instance
//        $commandTester->setInputs(array('This', 'That'));
//
//        // For simulating a positive answer to a confirmation question, adding an
//        // additional input saying "yes" will work
//        $commandTester->setInputs(array('yes'));

        $this->assertEquals(0, $commandTester->execute([]));
        $this->assertContains('The configuration seems correct.', $commandTester->getDisplay());
    }
}
