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

        $input = 'Technical Director';
        $output = $this->extension->annotatePositions($input, $positions);
        $expected = '<a class="position-link" href="/positions/technical-director">Technical Director&nbsp;'
            .'<i class="fa fa-question-circle"></i></a>';
        $this->assertEquals($expected, $output);

        $input = 'Technical Directors';
        $output = $this->extension->annotatePositions($input, $positions);
        $expected = '<a class="position-link" href="/positions/technical-director">Technical Directors&nbsp;'
            .'<i class="fa fa-question-circle"></i></a>';
        $this->assertEquals($expected, $output);

        $input = "We are seeking a Technical Director and a Director.";
        $output = $this->extension->annotatePositions($input, $positions);
        $expected = 'We are seeking a <a class="position-link" href="/positions/technical-director">'
            .'Technical Director&nbsp;<i class="fa fa-question-circle"></i></a>'
            .' and a <a class="position-link" href="/positions/director">Director&nbsp;'
            .'<i class="fa fa-question-circle"></i></a>.';
        $this->assertEquals($expected, $output);
    }
}
