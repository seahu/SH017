<?php
// source: /var/www/rfx433MHz/app/components/kanluxApoTm3RemoveControl/kanluxApoTm3RemoveControl.latte

class Template95d90a7efda577ff1610fc9f0d350821 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('2f40f4c7e0', 'html')
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
						<td><a  class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_D->status=="ON" ? "ON" : "OFF", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_D', 'on')), ENT_COMPAT) ?>
"> ON </a></td>
						<td></td>
						<td><a class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_D->status=="ON" ? "OFF" : "ON", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_D', 'off')), ENT_COMPAT) ?>
"> OFF </a></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->but_D->name, ENT_NOQUOTES) ?></td>
					<tr>
					<tr>
						<td><a  class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_E->status=="ON" ? "ON" : "OFF", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_E', 'on')), ENT_COMPAT) ?>
"> ON </a></td>
						<td></td>
						<td><a class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->but_E->status=="ON" ? "OFF" : "ON", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetButton!", array('but_E', 'off')), ENT_COMPAT) ?>
"> OFF </a></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->but_E->name, ENT_NOQUOTES) ?></td>
					<tr>

				</table>
			</td>
			<td>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetRFXCode!"), ENT_COMPAT) ?>
"> Set code </a><br>
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