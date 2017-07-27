<?php
// source: /var/www/seahu/app/presenters/templates/Homepage/default.latte

class Template7866ae9b2ecbfe67fdc5d9cebe77af28 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('993fabab76', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lb151a834e75_content')) { function _lb151a834e75_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
;if ($emptny==true) { ?>
	<h2>Not runing serices</h2>
	Go to menu service select your prefered service and get it start.
<?php } else { ?>
	<h1>Running Services</H1>
	<div class="services">
<?php $iterations = 0; foreach ($services as $service) { ?>
		<div class="service">
			<table>
<?php if ($service['status']==true) { ?>
					<tr>
						<td>
							<div class="service_title">
								<?php echo Latte\Runtime\Filters::escapeHtml($service['title'], ENT_NOQUOTES) ?>

							</div>
							<div class="service_preview">
									<a href="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($service['link']), ENT_COMPAT) ?>
"><image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>
/images/<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($service['preview']), ENT_COMPAT) ?>"></a>
							</div>
							<div class="service_logo">
								<a href="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($service['homePage']), ENT_COMPAT) ?>
"><image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>
/images/<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($service['logo']), ENT_COMPAT) ?>"></a>
							</div>
						</td>
					</tr>
<?php } ?>
			</table>
		</div>
<?php $iterations++; } ?>
	</div>
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
?>

<?php if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars()) ; 
}}