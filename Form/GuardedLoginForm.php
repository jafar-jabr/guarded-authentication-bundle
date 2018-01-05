<?php
/*
 * This file is part of the Guarded Authentication package.
 *
 * (c) Jafar Jabr <jafaronly@yahoo.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Jafar\Bundle\GuardedAuthenticationBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type as Types;

/**
 * @author Jafar Jabr <jafaronly@yahoo.com>
 * Class GuardedLoginForm
 * @package Jafar\Bundle\GuardedAuthenticationBundle\Form
 */
class GuardedLoginForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->add('_username')
            ->add('_password', Types\PasswordType::class);
    }
}
