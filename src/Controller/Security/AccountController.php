<?php

namespace App\Controller\Security;

use App\Classe\Search;
use App\Entity\Address;
use App\Form\App\SearchType;
use App\Service\CartService;
use App\Security\EmailVerifier;
use App\Form\Security\AccountType;
use Doctrine\ORM\EntityManagerInterface;
use App\Repository\ConversationRepository;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/compte', name: 'account_')]
class AccountController extends AbstractController
{
    private EmailVerifier $emailVerifier;

    public function __construct(EmailVerifier $emailVerifier)
    {
        $this->emailVerifier = $emailVerifier;
    }
    
    #[Route('/', name: 'show')]
    public function show(Request $request, CartService $sessionCart): Response
    {
        // Filter by name and category
        $search = new Search();
        $formSearch = $this->createForm(SearchType::class, $search);
        $formSearch->handleRequest($request);

        return $this->render('security/account/index.html.twig', [
            'user' => $this->getUser(),
            'controller_name' => 'Mon compte',
            'formSearch' => $formSearch,
            'sessionCart' => $sessionCart,
        ]);
    }

    #[Route('/modifier-infos', name: 'edit')]
    public function edit(Request $request, UserPasswordHasherInterface $encoder, EntityManagerInterface $entityManager, CartService $sessionCart): Response
    {
        // Filter by name and category
        $search = new Search();
        $formSearch = $this->createForm(SearchType::class, $search);
        $formSearch->handleRequest($request);

        $user = $this->getUser();
        $oldEmail = $this->getUser()->getEmail();
        $address = new Address();
        $form = $this->createForm(AccountType::class, $user);
        $form->handleRequest($request);
        

        if ($form->isSubmitted() && $form->isValid()) {

            if($form->get('newPassword')->getData()){
                $oldPassword = $form->get('oldPassword')->getData();
                if($encoder->isPasswordValid($user, $oldPassword)){
                    $user->setPassword(
                        $encoder->hashPassword(
                            $user,
                            $form->get('newPassword')->getData()
                        )
                    );
                }
            }

            foreach($user->getAddresses() as $address){
                $address->setUser($user);
                $entityManager->persist($address);
            }

            if($oldEmail != $form->get('email')->getData()){
                $user->setIsVerified(0);
            }
            $entityManager->persist($user);
            $entityManager->flush();

            if($oldEmail != $form->get('email')->getData()){
                // generate a signed url and email it to the user
                $this->emailVerifier->sendEmailConfirmation('verify_email', $user,
                    (new TemplatedEmail())
                        ->from(new Address('noreply@ancestral.com', 'Ancestral réponse automatique'))
                        ->to($user->getEmail())
                        ->subject('Please Confirm your Email')
                        ->htmlTemplate('security/confirmation_email.html.twig')
                );
                // do anything else you need here, like send an email
            }

            return $this->redirectToRoute('account_show');
            
        }
        return $this->render('security/account/edit.html.twig', [
            'form' => $form,
            'controller_name' => 'Modification de mes informations personnelles',
            'formSearch' => $formSearch,
            'sessionCart' => $sessionCart,
        ]);
    }

    #[Route('/conversation/{ticket?}', name: 'message')]
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
