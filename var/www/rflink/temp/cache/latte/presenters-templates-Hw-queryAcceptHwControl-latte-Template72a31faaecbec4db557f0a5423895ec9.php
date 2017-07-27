<?php
// source: /var/www/seahu/app/presenters/templates/Hw/queryAcceptHwControl.latte

class Template72a31faaecbec4db557f0a5423895ec9 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('de2db5b8fc', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lbaef5fb56ef_content')) { function _lbaef5fb56ef_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?>You go to control Hardware, bat another service may-be controled this hardware.<br>
When you intervence into Hardware, those services may run not consistently.<br>
You intervence only when you know what you do.
<table>
	<tr>
		<td><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Hw:"), ENT_COMPAT) ?>
">← Back</a></td>
		<td></td><td></td><td></td>
		<td><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Hw:accept"), ENT_COMPAT) ?>
">I acknowledge</a></td>
	</tr>
</table><?php
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