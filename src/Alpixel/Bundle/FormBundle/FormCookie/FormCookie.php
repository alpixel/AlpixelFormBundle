<?php

namespace Alpixel\Bundle\FormBundle\FormCookie;

use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormView;
=======
use Symfony\Component\HttpFoundation\Session\Session;

/**
 * @author Alexis BUSSIERES <alexis@alpixel.fr>
 */
class FormCookie
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function getSessionFormName($form)
    {
        $name = '';
        switch (get_class($form)) {
            case Form::class :
                $name = $form->getConfig()->getName();
                break;
            case FormView::class :
                $name = $form->vars['name'];
                break;
            default:
                throw new \InvalidArgumentException(sprintf('The argument is not an instance of "%s" or "%s"',
                    Form::class,
                    FormView::class
                ));
                break;
        }

        return 'filter_form_'.$name;
    }

    public function hasSessionForm($name)
    {
        return $this->session->has($name);
    }

    public function getSessionForm($name)
    {
        if ($this->hasSessionForm($name)) {
            return $this->session->get($name);
        }
    }

    public function removeSessionForm($name)
    {
        if ($this->hasSessionForm($name)) {
            return $this->session->remove($name);
        }
    }

    public function reset(Form $form)
    {
        $formName = $this->getSessionFormName($form);

        return $this->removeSessionForm($formName);
    }
}