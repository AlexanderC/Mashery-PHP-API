<?php
/**
 * Created by PhpStorm.
 * User: AlexanderC <self@alexanderc.me>
 * Date: 7/17/14
 * Time: 17:51
 */

namespace AlexanderC\Api\Mashery;


interface InternalObjectInterface
{
    /**
     * Properties that should be synced
     *
     * @return array
     */
    public function getMasherySyncProperties();

    /**
     * Object type that is represented by current object
     *
     * @return string
     */
    public function getMasheryObjectType();

    /**
     * Use getters and setters instead of direct property access
     *
     * @return bool
     */
    public function masheryUseSettersAndGetters();

    /**
     * Do not sync this properties back into the entity object
     *
     * @return array
     */
    public function getMasheryNoReverseSyncProperties();

    /**
     * @return mixed
     */
    public function getMasheryFieldsToSkipOnUpdate();
} 