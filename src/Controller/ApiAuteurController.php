<?php

namespace App\Controller;

use App\Entity\Auteur;
use App\Repository\AuteurRepository;
use App\Repository\NationaliteRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Serializer\SerializerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Validator\Validator\ValidatorInterface;

class ApiAuteurController extends AbstractController
{
    /**
     * @Route("/api/auteurs", name="api_auteurs", methods={"GET"})
     */
    public function list(AuteurRepository $repo, SerializerInterface $serializer)
    {
        $auteurs=$repo->findAll();
        $resultat=$serializer->serialize(
            $auteurs,
            'json',
            [
                'groups'=>['listAuteurFull']
            ]
            
        );
        return new JsonResponse($resultat,200,[],true);
    }

    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_show", methods={"GET"})
     */
    public function show(Auteur $auteur, SerializerInterface $serializer, AuteurRepository $repo, $id)
    {
        $auteurX=$repo->findById($id);

        //dump($auteurX);dump($auteur);die;
        $resultat=$serializer->serialize(
            $auteurX[0],
            'json',
            [
                'groups'=>['listAuteurSimple']  
            ]
            
        );
       
        return new JsonResponse($resultat,Response::HTTP_OK,[],true);
    }
    /**
     * @Route("/api/auteurs", name="api_auteurs_create", methods={"POST"})
     */
    public function create(Request $request, SerializerInterface $serializer, NationaliteRepository $repoNation, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $data = $request->getContent();
        $auteur=new Auteur();
        //$serializer->deserialize($data, Auteur::class, 'json', ['object_to_populate'=>$auteur]);
        $dataTab=$serializer->decode($data, 'json');
        $nationalite = $repoNation->find($dataTab['Relation']['id']);
        $serializer->deserialize($data, Auteur::class, 'json', ['object_to_populate'=>$auteur]);
        $auteur->setRelation($nationalite);

        
        //gestion
        //$validator=Validation::createValidator();
        $errors=$validator->validate($auteur);
        //dump(count($errors));die;
        if(count($errors)){
            $errorsJson=$serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST,[],true);
        }

        $manager->persist($auteur);
        $manager->flush();
        //dump($auteurX);dump($auteur);die;
       
        return new JsonResponse(
            "le auteur a bien été créé",
            Response::HTTP_CREATED,
            ["location"=>"api/auteurs/".$auteur->getId()],
            true
        );
        //["location"=>$this->generateUrl('api_auteurs_show', ["id"=>$auteur->getId(), UrlGeneratorInterface::ABSOLUTE_PATH])]
    }

    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_update", methods={"PUT"})
     */
    public function edit(Request $request, SerializerInterface $serializer, AuteurRepository $repo, NationaliteRepository $repoNation, $id, EntityManagerInterface $manager, ValidatorInterface $validator)
    {
        $auteurX=$repo->findById($id);
        $auteur = $auteurX[0];
        $data = $request->getContent();
        $dataTab=$serializer->decode($data, 'json');
        $nationalite = $repoNation->find($dataTab['Relation']['id']);
        //solution 1
        $serializer->deserialize($data, Auteur::class, 'json', ['object_to_populate'=>$auteur]);
        $auteur->setRelation($nationalite);
       // gestion des erreurs de validation
        $errors=$validator->validate($auteur);
       
        if(count($errors)){
            $errorsJson=$serializer->serialize($errors, 'json');
            return new JsonResponse($errorsJson, Response::HTTP_BAD_REQUEST,[],true);
        }

        $manager->persist($auteur);
        $manager->flush();
        return new JsonResponse("l'auteur a bien été modifié",Response::HTTP_OK,[],true);
    }

    /**
     * @Route("/api/auteurs/{id}", name="api_auteurs_delete", methods={"DELETE"})
     */
    public function delete(AuteurRepository $repo, $id, EntityManagerInterface $manager)
    {
        $auteurX=$repo->findById($id);
        $auteur = $auteurX[0];
        //dump($auteur);die;
        $manager->remove($auteur);
        $manager->flush();
        return new JsonResponse("l'auteur a bien été supprimé",Response::HTTP_OK,[]);
    }
}
