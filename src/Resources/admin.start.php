<?php
/**
 * Created by OOO 1C-SOFT.
 * User: GrandMaster
 * Date: 03.09.18
 */

require_once($_SERVER['DOCUMENT_ROOT'].'/bitrix/modules/main/include/prolog_admin_before.php');

//dump(
//	\CFile::FormatSize(123731968),
//	\CFile::FormatSize(20480)
//);


try{
	$AdminProvider = \Dar\Admin\AdminProvider::register();
	$AdminProvider->initPage();
} catch (\Dar\Admin\Exceptions\NotFoundResource $err){
	include $_SERVER['DOCUMENT_ROOT'] . BX_ROOT . '/admin/404.php';
}

$isPopup = ($AdminProvider->getRequest()->query->get('mode') == 'list');
if ($isPopup){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_popup_admin.php");
} else {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/prolog_admin_after.php");
}

//dump($page);
if(BX_ADMIN_SECTION_404 !== 'Y'){
	$AdminProvider->createPage()->render();
}

if ($isPopup){
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_popup_admin.php");
} else {
	require($_SERVER["DOCUMENT_ROOT"]."/bitrix/modules/main/include/epilog_admin.php");
}
