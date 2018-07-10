<?php
namespace Acts\CamdramApiBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Response;

class DocController extends Controller
{
    public function indexAction()
    {
        return $this->render('api/doc/index.html.twig');
    }

    public function docAction()
    {
        $data = $this->get('nelmio_api_doc.extractor.api_doc_extractor')->all();
        $formatter = $this->get('acts.camdram_api.service.api_doc_formatter');
        return new Response($formatter->format($data));
    }
}
