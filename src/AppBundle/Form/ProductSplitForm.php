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
 * Copiatek – info@copiatek.nl – Postbus 547 2501 CM Den Haag
 */

namespace AppBundle\Form;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ChoiceType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Extension\Core\Type\IntegerType;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Doctrine\ORM\EntityRepository;

class ProductSplitForm extends AbstractType
{
    /**
     * {@inheritdoc}
     */
    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        if ($options['stock'] > 2) {
            $choices = [
                'Split part of stock to new bundle' => 'split_stockpart',
                'Individualize part of stock' => 'individualize_stockpart',
                'Individualize whole stock' => 'individualize_stock',
                'Individualize whole bundle' => 'individualize_bundle'
            ];
        }
        elseif ($options['stock'] == 2) {
            $choices = [
                'Individualize stock' => 'individualize_stock',
                'Individualize whole bundle' => 'individualize_bundle'
            ];
        }
        else {
            $choices = ['Individualize whole bundle' => 'individualize_bundle'];           
        }
        
        $builder->add('how', ChoiceType::class, array(
                'choices' => $choices));

        if ($options['stock'] > 2) {
            $builder->add('quantity', IntegerType::class, [
                'attr' => ['min' => 1, 'max' => $options['stock'] - 1]
            ]);
        }

        $builder
            ->add('status',  EntityType::class, [
                'class' => 'AppBundle:ProductStatus',
                'choice_label' => 'name',
                'required' => true,
                'query_builder' => function (EntityRepository $er) { return $er->createQueryBuilder('x')->orderBy("x.name", "ASC"); }
            ])
            ->add('newSku', CheckboxType::class, [
                'required' => false,
                'label' => 'Create new SKU(s)'
            ])
            ->add('split', SubmitType::class, [
                'attr' => ['class' => 'btn-success btn-120']
            ]);
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setDefaults(array(
            'csrf_protection' => true,
            'csrf_field_name' => '_token',
            'csrf_token_id'   => 'splitproduct',
        ));

        $resolver->setRequired(array('stock'));
    }
}
