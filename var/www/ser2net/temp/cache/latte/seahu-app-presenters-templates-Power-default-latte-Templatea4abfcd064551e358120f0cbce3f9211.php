<?php
// source: /var/www/seahu/app/presenters/templates/Power/default.latte

class Templatea4abfcd064551e358120f0cbce3f9211 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('9dbba5413b', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb076ab51201_content')) { function _lb076ab51201_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;if ($user->loggedIn) { ?>
			<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Power:queryShutDownAccept"), ENT_COMPAT) ?>
">Shut down</a>
			<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Power:queryResetAccept"), ENT_COMPAT) ?>
">Reset</a>
<?php } 
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