<?php

namespace Alpixel\Bundle\FormBundle\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\HttpFoundation\Session\Session;

class FormCookieSubscriber implements EventSubscriberInterface
{
    protected $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public static function getSubscribedEvents()
    {
        return array(
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit',
        );
    }

    public function onPreSetData(FormEvent $event)
    {
        $form     = $event->getForm();
        $formName = 'filter_form_'.$form->getConfig()->getName();

        if($this->session->has($formName)) {
            $filters = $this->session->get($formName);

            foreach($filters as $field=>&$value) {
                $fieldConfig = $form->get($field)->getConfig();
                if($fieldConfig->getType()->getName() == 'entity') {
                    $entityManager = $fieldConfig->getOption('em');
                    $className     = $fieldConfig->getOption('class');
                    $value = $entityManager
                                ->getRepository($className)
                                ->find($value);
                }
            }

            $event->setData($filters);
        }
    }

    public function onPreSubmit(FormEvent $event)
    {
        $filters  = $event->getData();
        $formName = $event->getForm()->getConfig()->getName();

        $this->session->set('filter_form_'.$formName, $filters);
    }

}
