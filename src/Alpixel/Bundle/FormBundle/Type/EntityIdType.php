<?php

namespace Alpixel\Bundle\FormBundle\Type;

use Symfony\Component\Form\AbstractType;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\FormBuilderInterface;
use Symfony\Component\OptionsResolver\OptionsResolver;
use Symfony\Component\OptionsResolver\OptionsResolverInterface;
use Symfony\Component\Form\FormView;
use Symfony\Component\Form\FormInterface;

use Alpixel\Bundle\DataTransformer\EntityToIdTransformer;

/**
 * Entity identitifer
 *
 */
class EntityIdType extends AbstractType
{
    protected $registry;

    public function __construct(RegistryInterface $registry)
    {
        $this->registry = $registry;
    }

    public function buildForm(FormBuilderInterface $builder, array $options)
    {
        $builder->addModelTransformer(new EntityToIdTransformer(
            $this->registry->getManager($options['em']),
            $options['class'],
            $options['property'],
            $options['query_builder'],
            $options['multiple']
        ));
    }

    public function configureOptions(OptionsResolver $resolver)
    {
        $resolver->setRequired(array(
            'class',
        ));

        $resolver->setDefaults(array(
            'em'            => null,
            'property'      => null,
            'query_builder' => null,
            'hidden'        => true,
            'multiple'      => false,
        ));
    }

    public function buildView(FormView $view, FormInterface $form, array $options)
    {
        if (true === $options['hidden']) {
            $view->vars['type'] = 'hidden';
        }
    }

    public function getParent()
    {
        return 'text';
    }

    public function getName()
    {
        return 'alpixel_entity_id';
    }
}
