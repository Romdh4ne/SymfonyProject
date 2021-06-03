<?php

namespace App\Controller;

use DOMXPath;
use Dompdf\Dompdf;
use Dompdf\Options;
use App\Entity\Version;
use Convertio\Convertio;
use GrabzIt\GrabzItClient;
use Mnvx\Lowrapper\Format;
use App\Form\EditeurTextType;
use Mnvx\Lowrapper\Converter;
use PhpOffice\PhpWord\PhpWord;
use GrabzIt\GrabzItDOCXOptions;
use App\Entity\DocumentOriginal;
use PhpOffice\PhpWord\Shared\Html;
use App\Service\EditeurTextService;
use Symfony\Component\Finder\Finder;
use Mnvx\Lowrapper\LowrapperParameters;
use Doctrine\ORM\EntityManagerInterface;
use thiagoalessio\TesseractOCR\TesseractOCR;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\HttpFoundation\Session\Session;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class EditeurTextController extends AbstractController
{
  



    /**
     * @Route("/editeur/text/", name="editeur_text")
     * @IsGranted("ROLE_USER")

     */
    public function editorDisplay(Request $request,EntityManagerInterface $manager, EditeurTextService $service): Response
    {
     $Id = $request->query->get("documentId");
              $session=new Session();  
            $data= $session->get('data');
              $nomDocument= $data['nomDocument'];

     $documentId=$data['documentOriginal'];
            $contenu=$data['contenu'];

            $formulaire = $this->createForm(EditeurTextType::class,null,['text'=>$nomDocument]);
              $formulaire->handleRequest($request);
                      $utilisateur = $this->getUser();

       $repo = $this->getDoctrine()->getRepository(DocumentOriginal::class);
       $documents = $repo->findAll([
            'id_utilisateur' => $utilisateur->getId(),
        ]);

        $documentOriginal=end($documents);
         $finder = new Finder();
         $version = new Version();

       if($Id==$documentId){
       
        if ($formulaire->get('PDF')->isClicked()){
        $nomDocument=$formulaire->getData('nom_document')["nom_document"];
       $content =  $formulaire->getData('content')["content"];

        $conversion=$service->EnregisterPDF( $formulaire,$content,$nomDocument
        ,$documentOriginal,$version

        );
                       return $this->redirectToRoute('mes_documents');

      } 
          if ($formulaire->get('JPG')->isClicked()){
         $nomDocument=$formulaire->getData('nom_document')["nom_document"];
       $content=  $formulaire->getData('content')["content"];

     $conversion=$service->EnregistrerImage( $formulaire,$content,$nomDocument
        ,$documentOriginal,$version

        ); 
                       return $this->redirectToRoute('mes_documents');

      } 
          if ($formulaire->get('DOCX')->isClicked()) {
        $nomDocument=$formulaire->getData('nom_document')["nom_document"];

                   $content=  $formulaire->getData('content')["content"];

            $conversion=$service->EnregistrerDOCX( $formulaire,$content,$nomDocument
        ,$documentOriginal,$version

        );
         return $this->redirectToRoute('mes_documents');
       
        }
            
    }
        return $this->render('editeur_text/editeurText.html.twig', [
          'formulaire'=> $formulaire->createView(),
          'text' =>$contenu
        ]);
    
 
}



  /**
  * @Route("/editeur/text/modifier/{id}", name="editeur_text_modifier")
  * @IsGranted("ROLE_USER") 
  */
  public function modifierContenu($id,Request $request,EntityManagerInterface $manager, EditeurTextService $service): Response{
        $contents="";
        $session=new Session();  
         $version = new Version();
          $repo = $this->getDoctrine()->getRepository(DocumentOriginal::class);
          $document = $repo->findOneBy(array(
            'id' => $id
        ));
         
      
        if (!($document ==null)){
         $utilisateur = $this->getUser();
        $formulaire = $this->createForm(EditeurTextType::class,null,['text'=>$document->getNomDocument()]);
        $formulaire->handleRequest($request);
          $contenu=$document->getContenu();
          $nomDocument=$document->getNomDocument();
        }else{
          
        $repo = $this->getDoctrine()->getRepository(Version::class);
        $versions = $repo->findOneBy(array(
            'id' => $id,
        ));
          $nomDocument=$versions->getNomDocument();

        $utilisateur = $this->getUser();
        $formulaire = $this->createForm(EditeurTextType::class,null,['text'=>$versions->getNomDocument()]);
        $formulaire->handleRequest($request);
                $contenu=$versions->getContenu();
           $document = $versions->getDocumentOriginal();
      }
        if ($formulaire->get('PDF')->isClicked()){
        $content=  $formulaire->getData('content')["content"];
        $conversion=$service->EnregisterPDF( $formulaire,$content,$nomDocument
        ,$document,$version
        );
       return $this->redirectToRoute('mes_documents');

      } 
          if ($formulaire->get('JPG')->isClicked()){
          $content=  $formulaire->getData('content')["content"];
     $conversion=$service->EnregistrerImage( $formulaire,$content,$nomDocument
        ,$document,$version
            
        ); 
               return $this->redirectToRoute('mes_documents');

      } 
          if ($formulaire->get('DOCX')->isClicked()) {
            $content=  $formulaire->getData('content')["content"];
            $conversion=$service->EnregistrerDOCX( $formulaire,$content,$nomDocument
        ,$document,$version

        );
       return $this->redirectToRoute('mes_documents');

      }
    
   

    
   
      return $this->render('editeur_text/editeurText.html.twig', [
          'formulaire'=> $formulaire->createView(),
          'text' =>$contenu
      ]);
  }
}

