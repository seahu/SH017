<?php
// source: /var/www/seahu/app/presenters/templates/Modem/wait.latte

class Template3bb6d2f21e4cb00c9b4a8ad767de1d29 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('0a348f5d2e', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lbcefcba7e1e_content')) { function _lbcefcba7e1e_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><p>
	Please wait aproximate 15 sec to start modem, then click 
	<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Modem:"), ENT_COMPAT) ?>
"> OK </a> .
</p>
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