<?php
// source: /var/www/rfx433MHz/app/components/deviceCompomemnt_RenameDevice.latte

class Template3732033522857287df12b52392b802c5 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('c87be63f7a', 'html')
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
<?php $_l->tmp = $_control->getComponent("formRenameDevice"); if ($_l->tmp instanceof Nette\Application\UI\IRenderable) $_l->tmp->redrawControl(NULL, FALSE); $_l->tmp->render() ?>
			</td>
			<td>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("SetRFXCode!"), ENT_COMPAT) ?>
"> Set code </a><br>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Control!"), ENT_COMPAT) ?>
"> Control </a><br>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("AddDomoticz!"), ENT_COMPAT) ?>
"> Add Domoticz </a><br>
			</td>
		</tr>
	</table>
</div>
<?php
}}