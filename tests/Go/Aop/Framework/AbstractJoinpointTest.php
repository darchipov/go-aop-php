<?php

namespace Go\Aop\Framework;

class AbstractJoinpointTest extends \PHPUnit_Framework_TestCase
{
    /**
     * @var AbstractJoinpoint
     */
    protected $joinpoint;

    /**
     * @dataProvider sortingTestSource
     */
    public function testSortingLogic($advices, array $order = array())
    {
        $ref = new \ReflectionProperty('Go\Aop\Framework\AbstractJoinpoint', 'advices');
        $ref->setAccessible(true);

        $joinpoint = $this->getMockForAbstractClass(
            'Go\Aop\Framework\AbstractJoinpoint',
            array(__CLASS__, $advices)
        );
        $advices = $ref->getValue($joinpoint);
        foreach ($advices as $index => $advice) {
            if (isset($order[$index])) {
                $this->assertInstanceOf($order[$index], $advice);
            }
        }
    }

    public function sortingTestSource()
    {
        return array(
            // #0
            array(
                array(
                    $this->getMock('Go\Aop\AdviceAfter'),
                    $this->getMock('Go\Aop\AdviceBefore')
                ),
                array(
                    'Go\Aop\AdviceBefore',
                    'Go\Aop\AdviceAfter'
                )
            ),
            // #1
            array(
                array(
                    $this->getMock('Go\Aop\AdviceAfter'),
                    $this->getMock('Go\Aop\AdviceAround')
                ),
                array(
                    'Go\Aop\AdviceAfter',
                    'Go\Aop\AdviceAround'
                )
            ),
            // #2
            array(
                array(
                    $this->getMock('Go\Aop\AdviceBefore'),
                    $this->getMock('Go\Aop\AdviceAfter')
                ),
                array(
                    'Go\Aop\AdviceBefore',
                    'Go\Aop\AdviceAfter'
                )
            ),
            // #3
            array(
                array(
                    $this->getMock('Go\Aop\AdviceBefore'),
                    $this->getMock('Go\Aop\AdviceAround')
                ),
                array(
                    'Go\Aop\AdviceBefore',
                    'Go\Aop\AdviceAround'
                )
            ),
            // #4
            array(
                array(
                    $this->getMock('Go\Aop\AdviceAround'),
                    $this->getMock('Go\Aop\AdviceAfter')
                ),
                array(
                    'Go\Aop\AdviceAfter',
                    'Go\Aop\AdviceAround'
                )
            ),
            // #5
            array(
                array(
                    $this->getMock('Go\Aop\AdviceAround'),
                    $this->getMock('Go\Aop\AdviceBefore')
                ),
                array(
                    'Go\Aop\AdviceBefore',
                    'Go\Aop\AdviceAround'
                )
            ),
            // #6
            array(
                array(
                    $this->getMock('Go\Aop\AdviceBefore'),
                    $this->getMock('Go\Aop\AdviceAround'),
                    $this->getMock('Go\Aop\AdviceBefore'),
                    $this->getMock('Go\Aop\AdviceAfter'),
                ),
                array(
                    'Go\Aop\AdviceBefore',
                    'Go\Aop\AdviceBefore',
                    'Go\Aop\AdviceAfter',
                    'Go\Aop\AdviceAround',
                )
            ),
            // #7
            array(
                array(
                    $forth = $this->getOrderedAdvice(4, 'ForthAdvice'),
                    $first = $this->getOrderedAdvice(1, 'FirstAdvice')
                ),
                array(
                    get_class($first),
                    get_class($forth),
                )
            ),
        );
    }

    /**
     * Returns the ordered advice
     *
     * @param int $order Order
     * @param string $name Mock class name
     * @return \PHPUnit_Framework_MockObject_MockObject|OrderedAdvice
     */
    private function getOrderedAdvice($order, $name)
    {
        $mock = $this->getMock('Go\Aop\Framework\OrderedAdvice', array(), array(), $name);
        $mock
            ->expects($this->any())
            ->method('getAdviceOrder')
            ->will(
                $this->returnValue($order)
            );

        return $mock;
    }
}
