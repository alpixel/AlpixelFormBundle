<?php

namespace Alpixel\Bundle\FormBundle\Subscribers;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Form;
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
        return [
            FormEvents::PRE_SET_DATA => 'onPreSetData',
            FormEvents::PRE_SUBMIT   => 'onPreSubmit',
            FormEvents::POST_SUBMIT  => 'onPostSubmit',
        ];
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $formName = $this->getSessionName($form);

        if ($this->session->has($formName)) {
            $filters = $this->session->get($formName);

            foreach ($filters as $field => &$value) {
                if ($form->has($field)) {
                    $fieldConfig = $form->get($field)->getConfig();
                    $fieldType = $fieldConfig->getType()->getName();
                    switch ($fieldType) {
                        case 'entity':
                            $entityManager = $fieldConfig->getOption('em');
                            $className = $fieldConfig->getOption('class');
                            $value = $entityManager
                                        ->getRepository($className)
                                        ->find($value);
                        break;
                        case 'checkbox':
                            $value = (bool) $value;
                        break;
                    }
                }
            }

            $event->setData($filters);
        }

        $form->add('reset', 'reset', [
            'label' => 'Réinitialiser le formulaire',
            'attr'  => [
                'class' => 'btn btn-warning reset-cookie-form-action',
            ],
        ]);
    }

    public function onPreSubmit(FormEvent $event)
    {
        $filters = $event->getData();
        $form = $event->getForm();

        $this->session->set($this->getSessionName($form), $filters);
    }

    public function onPostSubmit(FormEvent $event)
    {
        $form = $event->getForm();
        if ($form->get('reset') instanceof Form) {
            $reset = $form->get('reset');
            $formName = $this->getSessionName($form);

            if ($reset->isSubmitted() === true && $this->session->has($formName) === true) {
                $this->session->remove($formName);
            }
        }
    }

    private function getSessionName(Form $form)
    {
        return 'filter_form_'.$form->getConfig()->getName();
    }
}
