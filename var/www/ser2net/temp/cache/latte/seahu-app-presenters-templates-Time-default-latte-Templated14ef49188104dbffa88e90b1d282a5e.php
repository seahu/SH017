<?php
// source: /var/www/seahu/app/presenters/templates/Time/default.latte

class Templated14ef49188104dbffa88e90b1d282a5e extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('95ab1504dc', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb5b4bbac8ba_content')) { function _lb5b4bbac8ba_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="net">
	<h1> Time </h1>
	date: <?php echo Latte\Runtime\Filters::escapeHtml($template->date($time, 'j. n. Y'), ENT_NOQUOTES) ?> <br>
	time: <?php echo Latte\Runtime\Filters::escapeHtml($template->date($time, 'G:i'), ENT_NOQUOTES) ?> <br>
	time zone: <?php echo Latte\Runtime\Filters::escapeHtml($timeZone, ENT_NOQUOTES) ?> <br>
	
<?php if ($user->loggedIn) { ?>
		<p><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Time:updateTimeForm"), ENT_COMPAT) ?>
"> Edit</a></p>
<?php } ?>
</div>
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