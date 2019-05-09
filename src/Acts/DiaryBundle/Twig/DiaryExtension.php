<?php

namespace Acts\DiaryBundle\Twig;

use Acts\DiaryBundle\Diary\Diary;
use Twig\Extension\AbstractExtension;

/**
 * Class DiaryExtension
 *
 * A Twig extension which provides custom functionality that can be used in Twig templates. The extension is registered
 * in services.yml
 */
class DiaryExtension extends AbstractExtension
{
    public function getName()
    {
        return 'acts_diary';
    }

    public function getFunctions()
    {
        return array(
            new \Twig_SimpleFunction('render_diary', array($this, 'renderDiary'), array('is_safe' => array('html'), 'needs_environment' => true))
        );
    }

    public function renderDiary(\Twig_Environment $env, Diary $diary)
    {
        return $env->render('ActsDiaryBundle:Diary:index.html.twig', array('diary' => $diary->createView()));
    }
}
