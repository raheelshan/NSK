<?php

/*
 * Nexxus Stock Keeping (online voorraad beheer software)
 * Copyright (C) 2018 Copiatek Scan & Computer Solution BV
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program. If not, see licenses.
 *
 * Copiatek - info@copiatek.nl - Postbus 547 2501 CM Den Haag
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\OptionsResolver\OptionsResolver;
use AppBundle\Entity\Customer;
use Symfony\Component\Form\Extension\Core\Type\EmailType;
use Symfony\Component\Form\Extension\Core\Type\TextType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Doctrine\ORM\EntityRepository;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;

class CustomerForm extends AbstractType
{
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder
            ->add('name', TextType::class);

        /** @var \AppBundle\Entity\User */
        $user = $options['user'];

        /** @var Customer */
        $customer = $builder->getData() ?? $options['customer'];
        
        $builder
            ->add('kvkNr', TextType::class, ['required' => false])
            ->add('representative', TextType::class, ['required' => false])
            ->add('email', EmailType::class)
            ->add('phone', TextType::class, ['required' => true])
            ->add('phone2', TextType::class, ['required' => false])
            ->add('street', TextType::class)
            ->add('streetExtra', TextType::class, ['required' => false, 'label' => 'Street2'])
            ->add('city', TextType::class)
            ->add('zip', TextType::class, ['required' => false])
            ->add('state', TextType::class, ['required' => false])
            ->add('country', TextType::class, ['required' => false])
            ->add('street2', TextType::class, ['required' => false, 'label' => 'Street'])
            ->add('streetExtra2', TextType::class, ['required' => false, 'label' => 'Street2'])
            ->add('city2', TextType::class, ['required' => false, 'label' => 'City'])
            ->add('zip2', TextType::class, ['required' => false, 'label' => 'Zip'])
            ->add('state2', TextType::class, ['required' => false, 'label' => 'State'])
            ->add('country2', TextType::class, ['required' => false, 'label' => 'Country'])
            ->add('isPartner', ChoiceType::class, array(
                'choices' => [
                    'No' => 0,
                    'No, has partner' => Customer::HAS_PARTNER,                    
                    'Yes, is partner' => Customer::PARTNER,
                    'Yes, is owning partner' => Customer::OWNING_PARTNER
            ]));    
            
            if ($customer && $customer->getIsPartner() == Customer::HAS_PARTNER) {

                // Is not partner, but has partner
                $builder->add('partner',  EntityType::class, [
                    'class' => 'AppBundle:Customer',
                    'choice_label' => 'name',
                    'required' => true,
                    'placeholder' => "",
                    'query_builder' => function (EntityRepository $er) { 
                        $qb = $er->createQueryBuilder('x')->orderBy("x.name", "ASC")
                            ->where('x.isPartner > 0'); 
                        return $qb;
                    }
                ]);
            }
            
            $builder->add('save', SubmitType::class, ['attr' => ['class' => 'btn-success btn-120']]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'data_class' => Customer::class,
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'customer',
            'user' => null,
            'customer' => null
        ));
    }
}