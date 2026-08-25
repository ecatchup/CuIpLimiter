<?php
/**
 * CuIpLimiter: Restrict by IP address Plugin for baserCMS <https://basercms.net>
 * Copyright (c) Catchup, Inc. <https://catchup.co.jp/>
 *
 * @copyright        Copyright (c) Catchup, Inc.
 * @link            https://catchup.co.jp Catchup Project
 * @license            https://opensource.org/license/mit MIT License
 */

use BaserCore\Utility\BcUtil;
use CuIpLimiter\Lib\CuIpLimiterUtil;

// コンソール（bin/cake・CRON 等）では IP 制限を行わない
if (BcUtil::isConsole()) return;

if (CuIpLimiterUtil::isAllowed()) return;
if (!CuIpLimiterUtil::hasLimitFolders()) {
	header("HTTP/1.0 404 Not Found");
	exit;
}
if (!CuIpLimiterUtil::isLimitFolder()) return;
$redirectUrl = CuIpLimiterUtil::getRedirectUrl();
if ($redirectUrl) {
	header("Location: " . $redirectUrl);
	exit;
} else {
	header("HTTP/1.0 404 Not Found");
	exit;
}
