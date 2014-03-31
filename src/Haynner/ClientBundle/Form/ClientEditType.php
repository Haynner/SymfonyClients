<?php

namespace Haynner\ClientBundle\Form;

use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;

class ClientEditType extends ClientType
{

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        parent::buildForm($builder,$options);
    }
    
    public function getName()
    {
        return 'haynner_clientbundle_clientedit';
    }
}
