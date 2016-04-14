<?php

namespace Alpixel\Bundle\FormBundle\Twig\Extension;

use Alpixel\Bundle\FormBundle\FormCookie\FormCookie;
use Symfony\Component\Form\Form;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class FormCookieExtension extends \Twig_Extension
{

    protected $formCookie;

    public function __construct(FormCookie $formCookie)
    {
        $this->formCookie = $formCookie;
    }

    public function getFunctions()
    {
        return [
            new \Twig_SimpleFunction('alpixel_has_form_session', [$this, 'hasFormSession'])
        ];
    }

    public function hasFormSession($form)
    {
        $name = $this->formCookie->getSessionFormName($form);

        return $this->formCookie->hasSessionForm($name);
    }

    public function getName()
    {
        return 'alpixel_formbundle_form_cookie_extension';
    }
}