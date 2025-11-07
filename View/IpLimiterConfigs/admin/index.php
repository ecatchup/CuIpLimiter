<?php
/* SVN FILE: $Id$ */
/**
 * [IpLimiter] 設定ページ
 *
 * PHP version 5
 *
 * baserCMS :  Based Website Development Project <http://basercms.net>
 * Copyright 2008 - 2012, baserCMS Users Community <http://sites.google.com/site/baserusers/>
 *
 * @copyright		Copyright 2011 - 2012, Catchup, Inc.
 * @link			http://www.e-catchup.jp Catchup, Inc.
 * @package			ip_limiter.views
 * @since			Baser v 2.0.0
 * @version			$Revision$
 * @modifiedby		$LastChangedBy$
 * @lastmodified	$Date$
 * @license			MIT lincense
 */
 ?>
<script type="text/javascript">
$(window).load(function() {
	$("#IpLimiterConfigAllowedIp").focus();
});
</script>

<?php echo $this->BcForm->create('IpLimiterConfig', ['url' => ['action' => 'index']]) ?>
<table class="form-table bca-form-table">
	<tr>
		<th class="bca-form-table__label"><?php echo $this->BcForm->label('IpLimiterConfig.allowed_ip', '許可するIPアドレス') ?></th>
		<td class=" bca-form-table__input">
			<small>* (アスタリスク)でグループ指定が行えます。カンマ区切りで複数指定できます。</small><br />
			<?php echo $this->BcForm->input('IpLimiterConfig.allowed_ip', array('type' => 'text', 'size' => 60)) ?>
			<?php echo $this->BcForm->error('IpLimiterConfig.allowed_ip') ?>
		</td>
	</tr>
	<tr>
		<th class="bca-form-table__label"><?php echo $this->BcForm->label('IpLimiterConfig.limit_folders', 'フォルダー指定') ?></th>
		<td class=" bca-form-table__input">
			<small>指定したフォルダのみに制限をかける事ができます。カンマ区切りで複数指定できます。</small><br />
			<?php echo $this->BcForm->input('IpLimiterConfig.limit_folders', array('type' => 'text', 'size' => 60)) ?>
			<?php echo $this->BcForm->error('IpLimiterConfig.limit_folders') ?>
		</td>
	</tr>
	<tr>
		<th class="bca-form-table__label"><?php echo $this->BcForm->label('IpLimiterConfig.redirect_url', 'リダイレクト先URL') ?></th>
		<td class=" bca-form-table__input">
			<small>指定しない場合は、Not Found ページが表示されます。</small><br />
			<?php echo $this->BcForm->input('IpLimiterConfig.redirect_url', array('type' => 'text', 'size' => 60)) ?>
			<?php echo $this->BcForm->error('IpLimiterConfig.redirect_url') ?>
		</td>
	</tr>
</table>

<div class="submit bca-actions">
	<div class="bca-actions__main">
		<?php echo $this->BcForm->button(__d('baser', '保存'), ['div' => false, 'class' => 'button bca-btn',
			'data-bca-btn-type' => 'save',
			'data-bca-btn-size' => 'lg',
			'data-bca-btn-width' => 'lg',
		]) ?>
	</div>
</div>
<?php echo $this->BcForm->end() ?>
