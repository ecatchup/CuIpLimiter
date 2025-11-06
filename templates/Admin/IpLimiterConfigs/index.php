<?php
/**
 * CuIpLimiter: Restrict by IP address Plugin for baserCMS <https://basercms.net>
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp/>
 *
 * @copyright    Copyright (c) Catchup, Inc.
 * @link      https://catchup.co.jp Catchup Project
 * @license      https://opensource.org/license/mit MIT License
 */
/**
 * @var \BaserCore\View\BcAdminAppView $this
 * @var \Cake\Datasource\EntityInterface $entity
 */
$this->BcAdmin->setTitle('IPリミッター設定');
$this->BcAdmin->setHelp('ip_limiter_configs_index');
?>


<script type="text/javascript">
  $(window).load(function () {
    $("#allowed-ip").focus();
  });
</script>

<?php echo $this->BcAdminForm->create($entity, ['url' => ['action' => 'index']]) ?>
<table class="form-table bca-form-table">
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('allowed_ip', '許可するIPアドレス') ?></th>
    <td class=" bca-form-table__input">
      <small>* (アスタリスク)でグループ指定が行えます。カンマ区切りで複数指定できます。</small><br/>
      <?php echo $this->BcAdminForm->control('allowed_ip', ['type' => 'text', 'size' => 60]) ?>
      <?php echo $this->BcAdminForm->error('allowed_ip') ?>
    </td>
  </tr>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('limit_folders', 'フォルダー指定') ?></th>
    <td class=" bca-form-table__input">
      <small>指定したフォルダのみに制限をかける事ができます。カンマ区切りで複数指定できます。</small><br/>
      <?php echo $this->BcAdminForm->control('limit_folders', ['type' => 'text', 'size' => 60]) ?>
      <?php echo $this->BcAdminForm->error('limit_folders') ?>
    </td>
  </tr>
  <tr>
    <th class="bca-form-table__label"><?php echo $this->BcAdminForm->label('redirect_url', 'リダイレクト先URL') ?></th>
    <td class=" bca-form-table__input">
      <small>指定しない場合は、Not Found ページが表示されます。</small><br/>
      <?php echo $this->BcAdminForm->control('redirect_url', ['type' => 'text', 'size' => 60]) ?>
      <?php echo $this->BcAdminForm->error('redirect_url') ?>
    </td>
  </tr>
</table>

<div class="submit bca-actions">
  <div class="bca-actions__main">
    <?php echo $this->BcAdminForm->button(__d('baser_core', '保存'), [
      'div' => false,
      'class' => 'button bca-btn bca-actions__item',
      'data-bca-btn-type' => 'save',
      'data-bca-btn-size' => 'lg',
      'data-bca-btn-width' => 'lg',
      'id' => 'BtnSave'
    ]) ?>
  </div>
</div>

<?php echo $this->BcAdminForm->end() ?>
