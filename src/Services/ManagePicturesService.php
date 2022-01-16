<?php

namespace App\Services;

use App\Entity\Annonces;
use App\Entity\Images;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBagInterface;

class ManagePicturesService
{
    private $params;

    public function __construct(ParameterBagInterface $parameterBagInterface)
    {
        $this->params = $parameterBagInterface;
    }

    public function addImage(array $images, Annonces $annonce)
    {
        foreach ($images as $image) {

            // Génère un nouveau nom (unique) de fichier
            $fichier = md5(uniqid()) . '.' . $image->guessExtension();

            // Copie ce fichier dans le dossier 'upload/images/offers'
            $image->move(
                $this->params->get('images_directory'),
                $fichier
            );

            // Stock le nom de l'image dans la BDD
            $img = new Images;
            $img->setName($fichier);
            $annonce->addImage($img);
        }
    }
}