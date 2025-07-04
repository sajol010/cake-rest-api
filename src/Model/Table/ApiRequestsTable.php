<?php
namespace RestApi\Model\Table;
use Cake\ORM\Behavior\TimestampBehavior;
use Cake\ORM\Table;
/**
 * ApiRequests Model
 *
 * @mixin TimestampBehavior
 */
class ApiRequestsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->setTable('api_requests');
        $this->setPrimaryKey('id');
        $this->addBehavior('Timestamp');
    }
}