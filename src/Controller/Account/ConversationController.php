<?php

namespace App\Controller\Account;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

#[Route('/compte/conversation', name: 'account_')]
class ConversationController extends AbstractController
{

    #[Route('/', name: 'message')]
    public function messageShow(MessageService $messages, UserRepository $users, UserService $user, ObjectManager $manager, Request $request, $ticket)
    {
        $conversation = findOneBy([
            'user' => $user,
            'ticket' => $ticket,
        ]);
        $message = new Message();
        $form = $this->createForm(MessageType::class, $message);
        $form->handleRequest($request);
        if($form->isSubmitted() && $form->isValid()){
            // Get connected user and set it as visitor
            $user = $this->getUser();
            $message->setSender($user);
            $message->setConversation($conversation);
            $manager->persist($message);
            $manager->flush();

            return $this->redirectToRoute('account_message', array(
                'ticket' => $ticket
            ));
        }
        
        
        // Translations of controller
        return $this->render('security/account/message.html.twig', [
            'controller_name' => '',
            'conversation' => $conversation,
            'form' => $form
        ]);
    }

    #[Route('/conversation/add/', name: 'message')]
    public function createConversation(MessageService $messages, ConversationRepository $conversationRepository){

        $user = $this->getUser();
        $conversation = findOneBy([
            'user' => $user,
            'isActive' => true,
        ]);
        if($conversation){
            return $this->redirectToRoute('account_message', array(
                'ticket' => $conversation->getTicket(),
            ));
        }else{
            $conversation = new Conversation();
            $conversation->setClient($user);
            $conversation->setResolved(false);
            $conversation->setActive(true);
            $conversationRepository->save($conversation, true);

            return $this->redirectToRoute('account_message', array(
                'ticket' => $conversation->getTicket(),
            ));
        }
        
        
    }
}
