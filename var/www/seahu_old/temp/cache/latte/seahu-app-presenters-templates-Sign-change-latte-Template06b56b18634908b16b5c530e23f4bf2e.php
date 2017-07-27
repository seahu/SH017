<?php
// source: /var/www/seahu/app/presenters/templates/Sign/change.latte

class Template06b56b18634908b16b5c530e23f4bf2e extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('6dd0a80f90', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb1b1b12c7d4_content')) { function _lb1b1b12c7d4_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;call_user_func(reset($_b->blocks['title']), $_b, get_defined_vars())  ?>

<?php $_l->tmp = $_control->getComponent("changeForm"); if ($_l->tmp instanceof Nette\Application\UI\IRenderable) $_l->tmp->redrawControl(NULL, FALSE); $_l->tmp->render() ?>

<p><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:"), ENT_COMPAT) ?>
">← Back</a></p>

<?php
}}

//
// block title
//
if (!function_exists($_b->blocks['title'][] = '_lb18b2a5325c_title')) { function _lb18b2a5325c_title($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><h1>Change password</h1>
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