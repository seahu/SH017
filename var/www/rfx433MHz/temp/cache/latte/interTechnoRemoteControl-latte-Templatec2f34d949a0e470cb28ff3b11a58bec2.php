<?php
// source: /var/www/rfx433MHz/app/components/interTechnoRemoteControl/interTechnoRemoteControl.latte

class Templatec2f34d949a0e470cb28ff3b11a58bec2 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('02d826b6cd', 'html')
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
						<td><a  class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_A->status=="ON" ? "ON" : "OFF", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_A', 'on')), ENT_COMPAT) ?>
"> ON </a></td>
						<td></td>
						<td><a class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_A->status=="ON" ? "OFF" : "ON", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_A', 'off')), ENT_COMPAT) ?>
"> OFF </a></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->but_A->name, ENT_NOQUOTES) ?></td>
					<tr>
					<tr>
						<td><a  class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_B->status=="ON" ? "ON" : "OFF", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_B', 'on')), ENT_COMPAT) ?>
"> ON </a></td>
						<td></td>
						<td><a class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_B->status=="ON" ? "OFF" : "ON", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_B', 'off')), ENT_COMPAT) ?>
"> OFF </a></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->but_B->name, ENT_NOQUOTES) ?></td>
					<tr>
					<tr>
						<td><a  class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_C->status=="ON" ? "ON" : "OFF", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_C', 'on')), ENT_COMPAT) ?>
"> ON </a></td>
						<td></td>
						<td><a class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_C->status=="ON" ? "OFF" : "ON", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_C', 'off')), ENT_COMPAT) ?>
"> OFF </a></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->but_C->name, ENT_NOQUOTES) ?></td>
					<tr>
					<tr>
						<td></td>
						<td></td>
						<td><a  class="mybutton_OFF"}" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_ALL_OFF', 'on')), ENT_COMPAT) ?>
"> ALL OFF </a></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->but_ALL_OFF->name, ENT_NOQUOTES) ?></td>
					<tr>
				</table>
			</td>
			<td>
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