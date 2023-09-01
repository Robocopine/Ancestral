<?php

namespace App\Service;

use App\Entity\Conversation;
use App\Repository\UserRepository;
use App\Repository\AvatarRepository;
use App\Repository\MessageRepository;
use App\Repository\ConversationRepository;
use Doctrine\Common\Persistence\ObjectManager;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

class MessageService {

  private $user;
  private $recipient;
  private $recipients;
  private $messages;
  private $message;
  private $limit = 10;

  public function __construct(TokenStorageInterface $tokenStorage, RequestStack $request, MessageRepository $messages,
    ObjectManager $manager, UserRepository $users, ConversationRepository $conversations)
  {
    $this->user = $tokenStorage->getToken()->getUser();
    $this->email = $request->getCurrentRequest()->attributes->get('email');
    $this->messageId = $request->getCurrentRequest()->attributes->get('messageId');
    $this->request = $request;
    $this->recipient = $users->findOneByEmail($this->email);
    $this->messages = $messages;
    $this->manager = $manager;
    $this->users = $users;
    $this->conversations = $conversations;
    $this->message = $messages->findOneById($this->messageId);

  }

  public function getRecipient(){
    return $this->recipient;
  }

  public function setRecipient($recipient){
    
    $this->recipient = $users->findOneByEmail($this->email);

    return $this;
  }

  public function getMessages(){
    return $this->messages;
  }

  public function setMessages($messages){
    $this->messages = $messages;

    return $this;
  }

  public function getMessage(){
    return $this->message;
  }

  public function setMessage($message){
    $this->message = $message;

    return $this;
  }

  public function getLimit()
  {
    return $this->limit;
  }

  public function setLimit($limit)
  {
    $this->limit = $limit;

    return $this;
  }

  public function getUserMessages(){

    return $this->manager->createQuery('SELECT m FROM App\Entity\Message m JOIN m.sender s JOIN m.recipient r
      WHERE (s.id = :user AND r.id = :recipient) OR (r.id = :user AND s.id = :recipient) ORDER BY m.createdAt DESC')
      ->setParameters(['user' => $this->user, 'recipient' => $this->recipient])->setMaxResults($this->limit)->getResult()
    ;
  }

  public function getActiveConversation(){

    if($this->user->getEmail() != 'juliebotch@gmail.com'){
      $conversation = $this->conversations->findOneBy(['client' => $this->user]);
    }else{
      $conversation = $this->conversations->findOneBy(['client' => $this->recipient]);
    }
    return $conversation;

  }

  public function getConversation(){
    return $this->manager->createQuery('SELECT c FROM App\Entity\Conversation c JOIN c.contactA a JOIN c.contactB b
    WHERE a.id = :user OR b.id= :user')->setParameters(['user' => $this->user])->getResult();
  }

  public function getRecipients() : array{
    $conversations = $this->getConversation();
    foreach ($conversations as $conversation) {
      if($conversation->getContactA() != $this->user){
        $recipient = $conversation->getContactA()->getEmail();
      }else{
        $recipient = $conversation->getContactB()->getEmail();
      }
      $recipients[] = $recipient;
    }
    return $recipients;
  }

  public function deleteMessage(){
    $this->manager->remove($this->getMessage());
    $this->manager->flush();
  }

  public function getCountMessages(){
    return $this->manager->createQuery('SELECT COUNT(m) FROM App\Entity\Message m
    WHERE m.conversation = :conversation')->setParameters(['conversation' => $this->getActiveConversation()])->getSingleScalarResult();
  }
}