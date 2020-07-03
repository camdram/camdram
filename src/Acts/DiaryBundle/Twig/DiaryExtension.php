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
    public function getName(): string
    {
        return 'acts_diary';
    }

    public function getFunctions(): array
    {
        return [new \Twig\TwigFunction('render_diary', [$this, 'renderDiary'],
            ['is_safe' => ['html'], 'needs_environment' => true])];
    }

    public function renderDiary(\Twig\Environment $env, Diary $diary)
    {
        return $env->render('ActsDiaryBundle:Diary:index.html.twig', array('diary' => $diary->createView()));
    }
}
