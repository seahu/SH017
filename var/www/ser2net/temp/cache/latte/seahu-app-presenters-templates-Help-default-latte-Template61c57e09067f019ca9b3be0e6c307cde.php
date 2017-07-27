<?php
// source: /var/www/seahu/app/presenters/templates/Help/default.latte

class Template61c57e09067f019ca9b3be0e6c307cde extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('59f8ae05a1', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb4fa1cd1ea1_content')) { function _lb4fa1cd1ea1_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><h1>Help and documentation</H1>

<table>
	<tr>
		<td>
			<h2><a href="http://www.seahu.cz" class="help"> Seahu </a></h2>
				<a href="../../help/seahu/en.Seahu_SH017_quick_start_manual.pdf" class="mybutton">
					Quick start
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/en.png">
				</a><br>
				<a href="../../help/seahu/cz.SeahuSH017_rychly_navod.pdf" class="mybutton">
					Rychlý start
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/cz.png">
				</a><br>
				<br>
				<a href="../../help/seahu/en.Seahu_SH017_hardware_specification.pdf" class="mybutton">
					Hardware specification
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/en.png">
				</a><br>
				<a href="../../help/seahu/cz.Seahu_SH017_hardwerova_specifikace.pdf" class="mybutton">
					Hardwerová specifikace
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/cz.png">
				</a><br>
				<br>
					
			<h2><a href="http://www.domoticz.com" class="help">Domoticz</a></h2>
			
				<a href="../../help/domoticz/en.Seahu_SH017_domoticz_basic_setting.pdf" class="mybutton">
					Basic setting
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/en.png">
				</a><br>
				<a href="../../help/domoticz/cz.Seahu_SH017_domoticz_zakladni_ovladani.pdf" class="mybutton">
					Základní ovládaní
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/cz.png">
				</a><br>
				<br>
				<a href="../../help/domoticz/en.Seahu_SH017_domoticz_example_service_events.pdf" class="mybutton">
					Example service events
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/en.png">
				</a><br>
				<a href="../../help/domoticz/cz.Seahu_SH017_domoticz_ukazka_obsluhy_udalosti.pdf" class="mybutton">
					Ukázka obsluhy události
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/cz.png">
				</a><br>
				<br>
				<a href="../../help/domoticz/DomoticzManual.pdf" class="mybutton">
					Generaly manual
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/en.png">
				</a><br>
				<br>
				<a href="../../help/domoticz/domoticz.db" class="mybutton">
					Download start database
					<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/downloading-png-24.png">
				</a><br>
		</td>
		<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		<td valign="top">
			<h2><a href="http://www.rexcontrols.com" class="help">Rex controls</a></h2>
			<a href="../../help/rex_controls/en.Seahu_SH017_rex_controls_quick_start.pdf" class="mybutton">
				Quick start
				<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/en.png">
			</a><br>
			<a href="../../help/rex_controls/cz.Seahu_SH017_rex_controls_rychly_navod.pdf" class="mybutton">
				Rychlý start
				<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/cz.png">
			</a><br>
			<br>
			<a href="../../help/rex_controls/producer/REX-2.50.1.7567-x86.exe" class="mybutton">
				Programming enviroment
				<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/windows_logo.png">
			</a><br>
			<a href="../../help/rex_controls/seahu_support.zip" class="mybutton">
				Examples and prepared tasks
				<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/zip.png">
			</a><br>
			<br>
			<a href="../../help/rex_controls/producer" class="mybutton">
				Producer materials (Materály vydavatele)
				<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>
/images/en.png"><image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/cz.png">
			</a><br>

			<h2><a href="../../help" class="help">Utility</a></h2>
			<a href="../../help/utility/putty.exe" class="mybutton">
				Putty
				<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/windows_logo.png">
			</a><br>
			<a href="../../help/utility/winscp576.zip" class="mybutton">
				Winscp
				<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/zip.png">
			</a><br>
			
		</td>
	</tr>
</table>

<?php
}}

//
// end of blocks
//

// template extending

$_l->extends = empty($_g->extended) && isset($_control) && $_control instanceof Nette\Application\UI\Presenter ? $_control->findLayoutTemplateFile() : NULL; $_g->extended = TRUE;

if ($_l->extends) { ob_start(function () {});}

// prolog Nette\Bridges\ApplicationLatte\UIMacros

// snippets support
if (empty($_l->extends) && !empty($_control->snippetMode)) {
	return Nette\Bridges\ApplicationLatte\UIRuntime::renderSnippets($_control, $_b, get_defined_vars());
}

//
// main template
//
if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars()) ; 
}}