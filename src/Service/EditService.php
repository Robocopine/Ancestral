<?php

namespace App\Service;

use App\Repository\SectionRepository;
use App\Repository\ClientEditRepository;

class EditService
{
    public function __construct(ClientEditRepository $clientEditRepository, SectionRepository $sectionRepository)
    {
        $this->clientEditRepository = $clientEditRepository;
        $this->sectionRepository = $sectionRepository;
    }

    public function getIcon(){
        $section = $this->sectionRepository->findOneByName("logo_icon");
        $elementEdit = $this->clientEditRepository->findOneBySection($section);
        $icon = $elementEdit->getContent();
        return $icon;
    }

    public function getLogoHeader(){
        $section = $this->sectionRepository->findOneByName("logo_header");
        $elementEdit = $this->clientEditRepository->findOneBySection($section);
        $icon = $elementEdit->getContent();
        return $icon;
    }

    public function getItems(){
        $items = ['icon' => $this->getIcon(), 'logo_header' => $this->getLogoHeader()];
        return $items;
    }

}