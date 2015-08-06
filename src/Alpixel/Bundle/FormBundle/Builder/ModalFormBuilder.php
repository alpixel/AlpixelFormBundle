<?php

namespace Alpixel\Bundle\FormBundle\Builder;

use SubscriptionBundle\Entity\Subscription;
use Symfony\Bridge\Doctrine\RegistryInterface;
use Symfony\Component\Form\Form;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Session\Session;
use Symfony\Component\Templating\EngineInterface;

class ModalFormBuilder
{
    protected $registry;
    protected $session;
    protected $templating;

    public function __construct(RegistryInterface $registry, Session $session, EngineInterface $templating)
    {
        $this->registry     = $registry;
        $this->session      = $session;
        $this->templating   = $templating;
    }

    public function createView(Form $form, $template, $parameters) {
        $html = $this->templating->render($template, $parameters);

        $response = new JsonResponse;
        $response->setData(array(
            "submitted" => $form->isSubmitted(),
            "errors"    => count($form->getErrors(true)),
            "html"      => $html,
        ));
        return $response;
    }

    public function handleForm(Form $form, &$object) {
        $entityManager = $this->registry->getManager();

        if ($form->isValid()) {
            $entityManager->persist($object);
            $entityManager->flush();

            $this->session->getFlashBag()->add(
                'success',
                'L\'élément a bien été sauvegardé'
            );
        }

        return $object;
    }

}
