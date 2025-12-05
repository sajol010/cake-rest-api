<?php

namespace RestApi\Model\Entity;

use Cake\I18n\DateTime;
use Cake\ORM\Entity;

/**
 * ApiRequest Entity
 *
 * @property string $id
 * @property string $http_method
 * @property string $endpoint
 * @property string $token
 * @property string $ip_address
 * @property string $request_data
 * @property int $response_code
 * @property string $response_type
 * @property string $response_data
 * @property string $exception
 * @property DateTime $created
 * @property DateTime $modified
 */
class ApiRequest extends Entity
{
    /**
     * Fields that can be mass assigned using newEntity() or patchEntity().
     *
     * Note that when '*' is set to true, this allows all unspecified fields to
     * be mass assigned. For security purposes, it is advised to set '*' to false
     * (or remove it), and explicitly make individual fields accessible as needed.
     *
     * @var array
     */
    protected $_accessible = [
        '*' => true,
        'id' => false
    ];
}
