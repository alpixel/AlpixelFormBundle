<?php

namespace Alpixel\Bundle\FormBundle\Twig\Extension;

class ModalFormExtension extends \Twig_Extension
{
    public function getName()
    {
        return 'modal_form';
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('modal_form_button', [$this, 'createButton'], [
                'is_safe'           => ['html'],
                'needs_environment' => true,
            ]),
        ];
    }

    public function createButton(\Twig_Environment $twig, $path, $parameters, $label, $css, $btn = null)
    {
        return $twig->render('AlpixelFormBundle:front:blocks/modal_button.html.twig', [
            'path'       => $path,
            'parameters' => $parameters,
            'label'      => $label,
            'btn'        => (isset($btn) ? $btn : 'plus-circle'),
            'css'        => $css,
            'modalId'    => uniqid(),
        ]);
    }
}
