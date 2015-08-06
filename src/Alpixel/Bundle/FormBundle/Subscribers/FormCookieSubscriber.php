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
        $formName = $form->getConfig()->getName();

        if($this->session->has('filter_form_'.$formName)) {
            $filters = $this->session->get('filter_form_'.$formName);

            foreach($filters as $field=>&$value) {
                $field = $form->get($field);
                if($field->getConfig()->getType()->getName() == 'entity') {
                    $entityManager = $field->getConfig()->getOption('em');
                    $className     = $field->getConfig()->getOption('class');
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
