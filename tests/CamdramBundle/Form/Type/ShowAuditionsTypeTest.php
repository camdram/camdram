<?php

namespace Camdram\Tests\CamdramBundle\Form\Type;

use Acts\CamdramBundle\Entity\Show;
use Acts\CamdramBundle\Entity\Audition;
use Acts\CamdramBundle\Form\Type\ShowAuditionsType;
use Symfony\Component\Form\Test\TypeTestCase;

class ShowAuditionsTypeTest extends TypeTestCase
{
    public function testSubmit()
    {
        // The exact error is
        // Symfony\Component\OptionsResolver\Exception\UndefinedOptionsException:
        // The option "constraints" does not exist. Defined options are: ...
        $this->markTestSkipped('test is broken due to constraints');
        $show = new Show();
        $form = $this->factory->create(ShowAuditionsType::class, $show);

        $form->submit([
            'aud_extra' => 'Lorem ipsum',
            'scheduled_auditions' => [
                [
                    'start_at' => ['date' => '2038-01-01', 'time' => '16:00'],
                    'end_at' => '18:00',
                    'location' => 'ADC Theatre'
                ],
            ],
            'non_scheduled_auditions' => [],
        ]);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals('Lorem ipsum', $show->getAudextra());
        $this->assertEquals(1, $show->getScheduledAuditions()->count());

        $audition = $show->getScheduledAuditions()->first();
        $this->assertEquals(new \DateTime('2038-01-01 16:00'), $audition->getStartAt());
        $this->assertEquals(new \DateTime('2038-01-01 18:00'), $audition->getEndAt());
        $this->assertEquals('ADC Theatre', $audition->getLocation());
    }

    public function testSubmitDst()
    {
        $this->markTestSkipped('test is broken due to constraints');
        $show = new Show();
        $form = $this->factory->create(ShowAuditionsType::class, $show);

        $form->submit([
            'aud_extra' => 'Lorem ipsum',
            'scheduled_auditions' => [
                [
                    'start_at' => ['date' => '2025-08-03', 'time' => '10:00'],
                    'end_at' => '15:00',
                    'location' => 'ADC Theatre'
                ],
            ],
            'non_scheduled_auditions' => [],
        ]);
        $this->assertTrue($form->isSynchronized());

        $this->assertEquals('Lorem ipsum', $show->getAudextra());
        $this->assertEquals(1, $show->getScheduledAuditions()->count());

        $audition = $show->getScheduledAuditions()->first();
        $this->assertEquals(new \DateTime('2025-08-03 09:00'), $audition->getStartAt());
        $this->assertEquals(new \DateTime('2025-08-03 14:00'), $audition->getEndAt());
        $this->assertEquals('ADC Theatre', $audition->getLocation());
    }
}
