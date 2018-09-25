<?php
/**
 * Created by OOO 1C-SOFT.
 * User: Dremin_S
 * Date: 19.09.2018
 */

namespace Dar\Admin\Builder;

use CAdminPopup;
use Bitrix\Main\Text\HtmlFilter;

class UiContextMenu extends \CAdminUiContextMenu
{
	private $isShownFilterContext = false;

	public function setFilterContextParam($bool)
	{
		$this->isShownFilterContext = $bool;
	}

	private function showActionButton()
	{
		if (!empty($this->additional_items))
		{
			if ($this->isPublicMode)
			{
				$menuUrl = "BX.adminList.showPublicMenu(this, ".HtmlFilter::encode(
					CAdminPopup::PhpToJavaScript($this->additional_items)).");";
			}
			else
			{
				$menuUrl = "BX.adminList.ShowMenu(this, ".HtmlFilter::encode(
					CAdminPopup::PhpToJavaScript($this->additional_items)).");";
			}

			?>
			<button class="ui-btn ui-btn-light-border ui-btn-themes ui-btn-icon-setting" onclick="
				<?=$menuUrl?>"></button>
			<?
		}
	}

	public function Show()
	{
		foreach (GetModuleEvents("main", "OnAdminContextMenuShow", true) as $arEvent)
		{
			ExecuteModuleEventEx($arEvent, array(&$this->items));
		}

		if (empty($this->items) && empty($this->additional_items))
		{
			return;
		}

		\Bitrix\Main\UI\Extension::load("ui.buttons");
		\Bitrix\Main\UI\Extension::load("ui.buttons.icons");

		if ($this->isPublicMode): ob_start(); ?>
		<div class="pagetitle-container pagetitle-align-right-container" style="padding-right: 12px;">
		<? else: ?>
		<? if (!$this->isShownFilterContext): ?>
			<div class="adm-toolbar-panel-container">
				<div class="adm-toolbar-panel-flexible-space">
					<? $this->showBaseButton(); ?>
				</div>
		<? endif ?>
		<div class="adm-toolbar-panel-align-right">
		<? endif;

		$this->showActionButton();

		if ($this->isShownFilterContext || $this->isPublicMode)
		{
			$this->showBaseButton();
		}

		?>
		</div>
		<? if (!$this->isShownFilterContext && !$this->isPublicMode): ?>
		</div>
		<? endif;

		if ($this->isPublicMode)
		{
			global $APPLICATION;
			$APPLICATION->AddViewContent("inside_pagetitle", ob_get_clean());
		}
	}

	private function showBaseButton()
	{
		if (!empty($this->items))
		{
			$items = $this->items;
			$firstItem = array_shift($items);
			if (!empty($firstItem["MENU"]))
			{
				$items = array_merge($items, $firstItem["MENU"]);
			}
			if ($this->isPublicMode)
			{
				$menuUrl = "BX.adminList.showPublicMenu(this, ".HtmlFilter::encode(
						CAdminPopup::PhpToJavaScript($items)).");";
			}
			else
			{
				$menuUrl = "BX.adminList.ShowMenu(this, ".HtmlFilter::encode(
						CAdminPopup::PhpToJavaScript($items)).");";
			}
			if (count($items) > 0):?>
				<? if (!empty($firstItem["ONCLICK"])): ?>
					<div class="ui-btn-double ui-btn-primary">
						<button onclick="<?=HtmlFilter::encode($firstItem["ONCLICK"])?>" class="ui-btn-main">
							<?=HtmlFilter::encode($firstItem["TEXT"])?>
						</button>
						<button onclick="<?=$menuUrl?>" class="ui-btn-extra"></button>
					</div>
				<? else: ?>
					<? if (isset($firstItem["DISABLE"])): ?>
						<div class="ui-btn-double ui-btn-primary">
							<button onclick="<?=$menuUrl?>" class="ui-btn-main">
								<?=HtmlFilter::encode($firstItem["TEXT"])?>
							</button>
							<button onclick="<?=$menuUrl?>" class="ui-btn-extra"></button>
						</div>
					<? else: ?>
						<div class="ui-btn-double ui-btn-primary">
							<a href="<?=HtmlFilter::encode($firstItem["LINK"])?>" class="ui-btn-main">
								<?=HtmlFilter::encode($firstItem["TEXT"])?>
							</a>
							<button onclick="<?=$menuUrl?>" class="ui-btn-extra"></button>
						</div>
					<? endif; ?>
				<? endif; ?>
			<? else:?>
				<? if (!empty($firstItem["ONCLICK"])):
					?>
					<button class="ui-btn ui-btn-primary" onclick="<?=HtmlFilter::encode($firstItem["ONCLICK"])?>">
						<?=HtmlFilter::encode($firstItem["TEXT"])?>
					</button>
				<? else: ?>
					<a class="ui-btn ui-btn-primary" href="<?=HtmlFilter::encode($firstItem["LINK"])?>">
						<?=HtmlFilter::encode($firstItem["TEXT"])?>
					</a>
				<? endif; ?>
			<?endif;
		}
	}
}