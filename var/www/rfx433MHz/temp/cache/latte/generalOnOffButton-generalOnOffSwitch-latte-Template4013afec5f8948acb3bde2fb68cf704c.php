<?php
// source: /var/www/rfx433MHz/app/components/generalOnOffButton/generalOnOffSwitch.latte

class Template4013afec5f8948acb3bde2fb68cf704c extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('8c6e157813', 'html')
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
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetSwitch!", array('on')), ENT_COMPAT) ?>
"> ON </a></td>
						<td></td>
						<td><a class="mybutton_<?php echo Latte\Runtime\Filters::escapeHtml($config->status=="ON" ? "OFF" : "ON", ENT_COMPAT) ?>
" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetSwitch!", array('off')), ENT_COMPAT) ?>
"> OFF </a></td>
						<td><?php echo Latte\Runtime\Filters::escapeHtml($config->name, ENT_NOQUOTES) ?></td>
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