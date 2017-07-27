<?php
// source: /var/www/rfx433MHz/app/components/interTechnoSocket/interTechnoSocket.latte

class Templateb7c2b7e77778e3b23a41b92126171ae8 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('1f505bdc75', 'html')
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
				<table class="small">
					<tr>
						<td><a  class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->status=="ON" ? "ON" : "OFF", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetSocket!", array('on')), ENT_COMPAT) ?>
"> ON </a></td>
						<td></td>
						<td><a class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->status=="ON" ? "OFF" : "ON", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetSocket!", array('off')), ENT_COMPAT) ?>
"> OFF </a></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->name, ENT_NOQUOTES) ?></td>
					<tr>
					<tr>
						<td></td>
						<td><a class="mybutton_OFF" title="Also can be use for reset socket." href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("AllOff!"), ENT_COMPAT) ?>
"> ALL OFF </a></td>
						<td></td>
						<td></td>
					</tr>
				</table>
			</td>
			<td>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("InterTechnoSocketSetCode!"), ENT_COMPAT) ?>
"> Set code </a>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("InterTechnoSocketScanCode!"), ENT_COMPAT) ?>
"> Scan code </a><br>
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