<?php
// source: /var/www/rfx433MHz/app/components/interTechnoSocket/interTechnoSocket_RenameSocket.latte

class Template8f5a3525077b7deb12840f697a00de78 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('6510e859f9', 'html')
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
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("InterTechnoSocketSetCode!"), ENT_COMPAT) ?>
"> Set code </a>
				<a class="mybutton_1" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("InterTechnoSocketScanCode!"), ENT_COMPAT) ?>
"> Scan code </a><br>
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