<?php

namespace Acts\CamdramLegacyBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Acts\CamdramLegacyBundle\Entity\AuthToken;

class ExternalLoginController extends Controller
{
    public function defaultAction(Request $request)
    {
        $site = null;
        
        if ($request->query->has('redirect')) {
            $redirectUri = $request->query->get('redirect');
            $redirect_parts = parse_url($redirectUri);
            
            $repo = $this->getDoctrine()->getRepository('ActsCamdramLegacyBundle:ExternalSite');
            $site = $repo->findOneByUrl('http://' . $redirect_parts['host']);
            
            if ($this->isGranted('IS_AUTHENTICATED_FULLY')) {
                //Skip explanatory page if already fully logged in
                return $this->forward('ActsCamdramLegacyBundle:ExternalLogin:auth', [], ['redirect' => $redirectUri]);
            }
        }
        
        return $this->render(
        
            "ActsCamdramLegacyBundle:ExternalLogin:default.html.twig",
            ['site' => $site, 'redirect' => $request->query->get('redirect')]
        
        );
    }
    
    public function authAction(Request $request)
    {
        $this->get('camdram.security.utils')->ensureRole('IS_AUTHENTICATED_FULLY');
        
        $redirectUri = $request->query->get('redirect');
        $redirect_parts = parse_url($redirectUri);
        
        $repo = $this->getDoctrine()->getRepository('ActsCamdramLegacyBundle:ExternalSite');
        $site = $repo->findOneByUrl('http://' . $redirect_parts['host']);
        
        $tokenId = md5(rand());
        
        $token = new AuthToken();
        $token->setUser($this->getUser())
        ->setSite($site)
        ->setToken($tokenId)
        ->setIssued(new \DateTime);
        $em = $this->getDoctrine()->getEntityManager();
        $em->persist($token);
        $em->flush();
        
        parse_str(isset($redirect_parts['query']) ? $redirect_parts['query'] : '', $query_parts);
        $query_parts['camdramauthtoken'] = $tokenId;
        $query_parts['finaldestination'] = $redirectUri;
        $query_str = http_build_query($query_parts);
        $url = $redirect_parts['scheme']."://".$redirect_parts['host'].$redirect_parts['path']
        .'?'.$query_str;
        return $this->redirect($url);
    }
    
    public function spendTokenAction(Request $request)
    {
        //Delete tokens > 60s old
        $repo = $this->getDoctrine()->getRepository('ActsCamdramLegacyBundle:AuthToken');
        $qb = $repo->createQueryBuilder('t');
        $qb->delete()->where('t.issued < :threshold')
            ->setParameter('threshold', new \DateTime('-60sec'))
            ->getQuery()->execute();
        
        if ($request->query->has('tokenid')) {
            $tokenId = $request->get('tokenid');
            
            if ($token = $repo->findOneByToken($tokenId)) {
                $em = $this->getDoctrine()->getEntityManager();
                $em->remove($token);
                $em->flush();
                
                if ($user = $token->getUser()) {
                    return new Response("OK\n"
                        .$user->getId()."\n"
                        .$user->getName()."\n"
                        .$user->getEmail()."\n");
                }
            }
        }
        
        return new Response("ERROR");
    }
}
