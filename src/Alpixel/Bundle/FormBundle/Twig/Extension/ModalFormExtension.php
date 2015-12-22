<?php

namespace Alpixel\Bundle\FormBundle\Twig\Extension;

use Symfony\Component\Templating\EngineInterface;


class ModalFormExtension extends \Twig_Extension
{

    public function getName() {
        return 'modal_form';
    }

    public function getFunctions() {
        return array(
            new \Twig_SimpleFunction('modal_form_button', array($this, 'createButton'), array(
                'is_safe' => array('html'),
                'needs_environment' => true
            )),
        );
    }

    public function createButton(\Twig_Environment $twig, $path, $parameters, $label, $css, $btn = null) {
        return $twig->render('AlpixelFormBundle:front:blocks/modal_button.html.twig', array(
            'path'       => $path,
            'parameters' => $parameters,
            'label'      => $label,
            'btn'        => (isset($btn) ? $btn : 'plus-circle'),
            'css'        => $css,
            'modalId'    => uniqid(),
        ));
    }

}
