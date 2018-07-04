<?php
/**
 * Created by PhpStorm.
 * User: peter
 * Date: 06/04/15
 * Time: 01:36
 */

namespace Acts\CamdramApiBundle\Service;

use Nelmio\ApiDocBundle\Formatter\AbstractFormatter;

class ApiDocFormatter extends AbstractFormatter
{

    /**
     * @var \Twig_Environment
     */
    private $twig;

    public function __construct(\Twig_Environment $twig)
    {
        $this->twig = $twig;
    }

    /**
     * Format a single array of data
     *
     * @param  array $data
     * @return string|array
     */
    protected function renderOne(array $data)
    {
        // TODO: Implement renderOne() method.
    }

    /**
     * Format a set of resource sections.
     *
     * @param  array $collection
     * @return string|array
     */
    protected function render(array $collection)
    {
        return $this->twig->render('ActsCamdramApiBundle:Doc:resources.html.twig', array(
            'resources' => $collection
        ));
    }
}
