<?php
/**
 * CuIpLimiter: Restrict by IP address Plugin for baserCMS <https://basercms.net>
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp/>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link            https://catchup.co.jp Catchup Project
 * @license            https://opensource.org/license/mit MIT License
 */

namespace CuIpLimiter\Model\Table;

use Cake\Validation\Validator;

/**
 * IpLimiterConfigsTable
 */
class IpLimiterConfigsTable extends \BaserCore\Model\Table\AppTable
{
    /**
     * Initialize
     *
     * @param array $config テーブル設定
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);
        $this->addBehavior('BaserCore.BcKeyValue');
    }

    /**
     * Validation Key Value
     *
     * @param Validator $validator
     * @return Validator
     */
    public function validationKeyValue(Validator $validator): Validator
    {
        return $validator;
    }

}
