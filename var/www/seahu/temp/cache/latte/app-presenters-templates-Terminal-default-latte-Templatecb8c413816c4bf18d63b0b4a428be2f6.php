<?php
// source: /var/www/seahu/app/presenters/templates/Terminal/default.latte

class Templatecb8c413816c4bf18d63b0b4a428be2f6 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('93961d8b14', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb63dbb6ed5a_content')) { function _lb63dbb6ed5a_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><h2>Terminal:</h2>
<iframe src="https://<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($ip), ENT_COMPAT) ?>:4200" width="100%" height="100%">
</iframe>
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