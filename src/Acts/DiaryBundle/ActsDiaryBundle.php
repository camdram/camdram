<?php

namespace Acts\DiaryBundle;

use Symfony\Component\HttpKernel\Bundle\Bundle;

/**
 * Class ActsDiaryBundle
 *
 * The main purpose of the ActsDiaryBundle is to provide methods to render a Diary, including logic for
 * deciding how to arrange events into rows/columns. It is used by creating a Acts\DiaryBundle\Diary\Diary object using
 * the DiaryFactory, then adding event objects to it. The diary can then be passed to
 * Acts\DiaryBundle\Diary\Renderer\HtmlRenderer to render it as HTML.
 *
 * All Events must implement EventInterface
 *
 * This bundle is deliberately de-coupled from CamdramBundle, such that it could be made into a useful external bundle.
 * There's a helper class inside CamdramBundle which deals with turning shows and performances into events.
 */
class ActsDiaryBundle extends Bundle
{
}
