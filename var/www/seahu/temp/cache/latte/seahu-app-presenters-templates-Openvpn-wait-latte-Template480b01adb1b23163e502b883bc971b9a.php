<?php
// source: /var/www/seahu/app/presenters/templates/Openvpn/wait.latte

class Template480b01adb1b23163e502b883bc971b9a extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('a876395929', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb349fe67da1_content')) { function _lb349fe67da1_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><p>
	Please wait aproximate 15 sec to start modem, then click 
	<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Openvpn:"), ENT_COMPAT) ?>
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