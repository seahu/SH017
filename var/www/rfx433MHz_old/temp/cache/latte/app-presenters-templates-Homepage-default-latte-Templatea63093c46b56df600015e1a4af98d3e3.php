<?php
// source: /var/www/rfx433MHz/app/presenters/templates/Homepage/default.latte

class Templatea63093c46b56df600015e1a4af98d3e3 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('6cb6c445e3', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lbe8ac003428_content')) { function _lbe8ac003428_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="ser2net">
	</p>
		<table>
<?php if ($user->loggedIn) { ?>
				<tr>
					<td colspan='4'><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:SelectMakerForm"), ENT_COMPAT) ?>
"> Add new device</a></td>
					<td colspan='4' align='right'><a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:"), ENT_COMPAT) ?>
"> <image src="images/refresh.svg" height="40px"></a></td>
				</tr>
<?php } $iterations = 0; foreach ($iterator = $_l->its[] = new Latte\Runtime\CachingIterator($devices) as $device) { if ($iterator->isOdd()) { ?>
				<tr bgcolor='#EFEFEF'>
<?php } else { ?>
				<tr>
<?php } ?>
					<td><image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($device['maker_img']), ENT_COMPAT) ?>" width="70px"><td>
					<td><image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($device['product_img']), ENT_COMPAT) ?>" height="50px"></td>
					<td> <?php echo Latte\Runtime\Filters::escapeHtml($device['product'], ENT_NOQUOTES) ?><td>
<?php if ($user->loggedIn) { ?>
						<td align='right'><?php if (is_object($device['componentName'])) $_l->tmp = $device['componentName']; else $_l->tmp = $_control->getComponent($device['componentName']); if ($_l->tmp instanceof Nette\Application\UI\IRenderable) $_l->tmp->redrawControl(NULL, FALSE); $_l->tmp->render() ?></td>
						<td><a href='<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($device['product_bay']), ENT_QUOTES) ?>'><image src="images/kosik.svg" height="40px"></a></td>
						<td><a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:DeleteProductQuery", array($device['key'])), ENT_COMPAT) ?>
"> delete</a></td>
<?php } ?>
				</tr>
<?php $iterations++; } array_pop($_l->its); $iterator = end($_l->its) ?>
		</table>
	</p>

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