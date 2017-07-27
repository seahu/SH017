<?php
// source: /var/www/rfx433MHz/app/presenters/templates/Homepage/deleteProductQuery.latte

class Template599b84c2e4099fc059009f0cb902868d extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('5d065dbcde', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb7a1fa43ffe_content')) { function _lb7a1fa43ffe_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div id="ser2net">
	<h2> You sure to delete next item: </h2>
	</p>
		<table>
			<tr>
				<td><image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>
/<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($device['maker_img']), ENT_COMPAT) ?>" width="70px"><td>
				<td><image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>
/<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($device['product_img']), ENT_COMPAT) ?>" height="50px"></td>
				<td> <?php echo Latte\Runtime\Filters::escapeHtml($device['product'], ENT_NOQUOTES) ?><td>
				<td><?php echo Latte\Runtime\Filters::escapeHtml($device['name'], ENT_NOQUOTES) ?></td>
			</tr>
		</table>
	</p>
	<p>
		<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:"), ENT_COMPAT) ?>
"> No</a>
		<a class="mybutton" href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Homepage:DeleteProduct", array($id)), ENT_COMPAT) ?>
"> Yes</a>
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