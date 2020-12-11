<?php

namespace Camdram\Tests\CamdramBundle\Twig;

use Acts\CamdramBundle\Service\TextService;
use Acts\CamdramBundle\Twig\CamdramExtension;
use PHPUnit\Framework\TestCase;
use Acts\CamdramBundle\Entity\Position;
use Symfony\Component\Routing\Generator\UrlGeneratorInterface;

class CamdramExtensionTest extends TestCase
{
    /**
     * @var CamdramExtension
     */
    private $extension;

    /**
     * @var UrlGeneratorInterface
     */
    private $router;

    public function setUp(): void
    {
        $textService = new TextService();
        $this->router = $this->createMock(UrlGeneratorInterface::class);
        $this->extension = new CamdramExtension($textService, $this->router);
    }

    public function testAnnotatePositions()
    {
        $position1 = new Position;
        $position1->setName('Technical Director')
            ->setSlug('technical-director')
            ->addTagName('Technical Director')
            ->addTagName('TD');
        $position2 = new Position;
        $position2->setName('Director')
            ->setSlug('director')
            ->addTagName('Director');
        $positions = [$position1, $position2];

        $this->router->method('generate')->will($this->returnCallback(function($route, $params) {
            return '/positions/'.$params['identifier'];
        }));

        $input = "We are seeking a Technical Director and a Director.";
        $output = $this->extension->annotatePositions($input, $positions);
        $expected = 'We are seeking a <span>Technical Director '
            .'<a class="position-link" href="/positions/technical-director"><i class="fa fa-question-circle"></i></a></span>'
            .' and a <span>Director '
            .'<a class="position-link" href="/positions/director"><i class="fa fa-question-circle"></i></a></span>.';
        $this->assertEquals($expected, $output);
    }
}
