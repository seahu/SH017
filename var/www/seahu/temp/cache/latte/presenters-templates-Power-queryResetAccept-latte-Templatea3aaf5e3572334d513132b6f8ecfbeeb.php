<?php
// source: /var/www/seahu/app/presenters/templates/Power/queryResetAccept.latte

class Templatea3aaf5e3572334d513132b6f8ecfbeeb extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('ff1899f4f6', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb1962735037_content')) { function _lb1962735037_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?>Are you sure do <b>restrat</b>.
<table>
	<tr>
		<td><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Power:"), ENT_COMPAT) ?>
">← Back</a></td>
		<td></td><td></td><td></td>
		<td><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Power:reset"), ENT_COMPAT) ?>
">Yes</a></td>
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