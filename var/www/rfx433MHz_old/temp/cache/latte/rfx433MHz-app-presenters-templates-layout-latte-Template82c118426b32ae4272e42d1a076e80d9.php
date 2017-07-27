<?php
// source: /var/www/rfx433MHz/app/presenters/templates/@layout.latte

class Template82c118426b32ae4272e42d1a076e80d9 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('ef17c69ae4', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block head
//
if (!function_exists($_b->blocks['head'][] = '_lbe33451b077_head')) { function _lbe33451b077_head($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;
}}

//
// block scripts
//
if (!function_exists($_b->blocks['scripts'][] = '_lbec1170e68f_scripts')) { function _lbec1170e68f_scripts($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?>	<script src="https://code.jquery.com/jquery-1.12.0.min.js"></script>
	<script src="https://nette.github.io/resources/js/netteForms.min.js"></script>
	<script src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/js/main.js"></script>
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
?>
<!DOCTYPE html>
<html>
<head>
	<meta charset="utf-8">

	<title><?php if (isset($_b->blocks["title"])) { ob_start(function () {}); Latte\Macros\BlockMacrosRuntime::callBlock($_b, 'title', $template->getParameters()); echo $template->striptags(ob_get_clean()) ?>
 | <?php } ?>Nette Sandbox</title>

	<link rel="stylesheet" href="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/css/style.css">
	<link rel="stylesheet" href="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/css/seahuStyle.css">
	<meta name="viewport" content="width=device-width">
	<?php if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['head']), $_b, get_defined_vars())  ?>

</head>

<body>
<?php $iterations = 0; foreach ($flashes as $flash) { ?>	<div<?php if ($_l->tmp = array_filter(array('flash', $flash->type))) echo ' class="', Latte\Runtime\Filters::escapeHtml(implode(" ", array_unique($_l->tmp)), ENT_COMPAT), '"' ?>
><?php echo Latte\Runtime\Filters::escapeHtml($flash->message, ENT_NOQUOTES) ?></div>
<?php $iterations++; } ?>
	<div class="navig">
		 <img src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/point_1x1.png" id="img_line">
	    <a href="http://www.seahu.cz"><img src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/logo_v2.svg" id="img_logo"></a>
	    <img src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/rfx433MHz_logo2.svg" id="rfx433_logo">
		 <div class="buttonLeft">
	    <a href="/seahu">Home</a></li>
	    </div>
	    <div class="buttonRight">
<?php if ($user->loggedIn) { ?>
		        <a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Sign:out"), ENT_COMPAT) ?>
">Logout</a></li>
<?php } else { ?>
		        <a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Sign:in"), ENT_COMPAT) ?>
">Login</a></li>
<?php } ?>
	    </div>	    
	</div>
	<br>
<?php Latte\Macros\BlockMacrosRuntime::callBlock($_b, 'content', $template->getParameters()) ?>
	</div>
<?php call_user_func(reset($_b->blocks['scripts']), $_b, get_defined_vars())  ?>
</body>
</html>
<?php
}}