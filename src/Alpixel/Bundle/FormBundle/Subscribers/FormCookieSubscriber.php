<?php

namespace Alpixel\Bundle\FormBundle\Subscribers;

use Doctrine\ORM\EntityManager;
use Symfony\Bridge\Doctrine\Form\Type\EntityType;
use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Symfony\Component\Form\Extension\Core\Type\CheckboxType;
use Symfony\Component\Form\Extension\Core\Type\ResetType;
use Symfony\Component\Form\Extension\Core\Type\SubmitType;
use Symfony\Component\Form\Form;
use Symfony\Component\Form\FormConfigInterface;
use Symfony\Component\Form\FormEvent;
use Symfony\Component\Form\FormEvents;
use Symfony\Component\Form\FormInterface;
use Symfony\Component\Form\SubmitButton;
use Symfony\Component\HttpFoundation\Session\Session;

class FormCookieSubscriber implements EventSubscriberInterface
{
    protected $entityManager;
    protected $session;

    public function __construct(Session $session, EntityManager $entityManager)
    {
        $this->entityManager = $entityManager;
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

    protected static function fieldHasType(FormConfigInterface $config, string $type)
    {
        $match = false;
        $innerType = $config->getType()->getInnerType();

        if (get_class($innerType) === $type) {
            $match = true;
        } else if ($innerType->getParent() === $type) {
            $match = true;
        }

        return $match;
    }

    public function onPreSetData(FormEvent $event)
    {
        $form = $event->getForm();
        $formName = $this->getSessionName($form);
        if ($this->session->has($formName)) {
            $data = $event->getData();
            $filters = $this->session->get($formName);
            if (is_array($filters)) {
                foreach ($filters as $field => $value) {
                    if ($form->has($field)) {
                        $fieldConfig = $form->get($field)->getConfig();

                        if (self::fieldHasType($fieldConfig, EntityType::class)) {
                            $entityManager = $fieldConfig->getOption('em');
                            if ($entityManager === null) {
                                $entityManager = $this->entityManager;
                            }
                            $className = $fieldConfig->getOption('class');
                            $value = $entityManager
                                ->getRepository($className)
                                ->find($value);
                        } else if (self::fieldHasType($fieldConfig, CheckboxType::class)) {
                            $value = (bool)$value;
                        }
                    }

                    $data[$field] = $value;
                }
            }

            $event->setData($data);
        }

        $form->add('reset', SubmitType::class, [
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

        if ($form->get('reset') instanceof SubmitButton) {
            $reset = $form->get('reset');
            $formName = $this->getSessionName($form);
            if ($reset->isClicked() === true && $this->session->has($formName) === true) {
                $this->session->remove($formName);
            }
        }
    }

    private function getSessionName(Form $form)
    {
        return 'filter_form_' . $form->getConfig()->getName();
    }
}
