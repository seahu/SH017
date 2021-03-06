<?php
// source: /var/www/rfx433MHz/app/components/interTechnoSocket/interTechnoSocket_scanCode.latte

class Templateef5412bfd21e1d0155dcce53177cd845 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('503b04df33', 'html')
;
// prolog Nette\Bridges\ApplicationLatte\UIMacros

// snippets support
if (empty($_l->extends) && !empty($_control->snippetMode)) {
	return Nette\Bridges\ApplicationLatte\UIRuntime::renderSnippets($_control, $_b, get_defined_vars());
}

//
// main template
//
?>
<div class="box">
	<table>
		<tr>
			<td>
<?php if ($config->scanning=="") { ?>
					<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("StartScanRFXCode!"), ENT_COMPAT) ?>
"> Start scan</a><br>
						actual family code:<br>
						<?php echo Latte\Runtime\Filters::escapeHtml($config->familyCode, ENT_NOQUOTES) ?><br>
						actual button code:<br>
						<?php echo Latte\Runtime\Filters::escapeHtml($config->buttonCode, ENT_NOQUOTES) ?>

						
<?php } else { ?>
					<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("StopScanRFXCode!"), ENT_COMPAT) ?>
"> Stop scan </a><br>
<?php } ?>
			</td>
			<td>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Control!"), ENT_COMPAT) ?>
"> Control </a><br>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Rename!"), ENT_COMPAT) ?>
"> Set Name </a><br>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("AddDomoticz!"), ENT_COMPAT) ?>
"> Add Domoticz </a><br>
			</td>
		</tr>
	</table>
</div>
<?php
}}