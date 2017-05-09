<?php
/**
 * Options Page
 *
 * @package    Cherry Projects
 * @subpackage View
 * @author     Cherry Team <cherryframework@gmail.com>
 * @copyright  Copyright (c) 2012 - 2016, Cherry Team
 * @link       http://www.cherryframework.com/
 * @license    http://www.gnu.org/licenses/old-licenses/gpl-2.0.html
 */
?>

<form id="cherry-projects-options-form" method="post">
	<div class="cherry-projects-options-page-wrapper">
		<div class="cherry-projects-options-list-wrapper">
			<?php echo $__data['settings']['ui-settings']; ?>
		</div>
		<div class="cherry-projects-options-control-wrapper">
			<div id="cherry-projects-save-options" class="custom-button save-button">
				<span> <?php echo $__data['settings']['labels']['save-button-text']; ?></span>
			</div>
			<div id="cherry-projects-define-as-default" class="custom-button define-as-default-button">
				<span><?php echo $__data['settings']['labels']['define-as-button-text']; ?></span>
			</div>
			<div id="cherry-projects-restore-options" class="custom-button restore-button">
				<span><?php echo $__data['settings']['labels']['restore-button-text']; ?></span>
			</div>
			<div class="cherry-spinner-wordpress">
				<div class="double-bounce-1"></div>
				<div class="double-bounce-2"></div>
			</div>
		</div>
	</div>
</form>
