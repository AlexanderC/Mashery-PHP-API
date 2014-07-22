<?php
/**
 * Created by PhpStorm.
 * User: AlexanderC <self@alexanderc.me>
 * Date: 7/22/14
 * Time: 15:01
 */

namespace AlexanderC\Api\Mashery;


interface InternalObjectCastInterface
{
    /**
     * Property names should be internal ones
     *
     * @return array
     */
    public function masheryCastMapIncomingValue();

    /**
     * Property names should be internal ones
     *
     * @return array
     */
    public function masheryCastMapOutgoingValue();
} 