<?php
// source: /var/www/seahu/app/presenters/templates/Servicies/default.latte

class Template2b3b0b93851f44cfba6136be5a5cd470 extends Latte\Template {
function render() {
foreach ($this->params as $__k => $__v) $$__k = $__v; unset($__k, $__v);
// prolog Latte\Macros\CoreMacros
list($_b, $_g, $_l) = $template->initialize('a759d1a081', 'html')
;
// prolog Latte\Macros\BlockMacros
//
// block content
//
if (!function_exists($_b->blocks['content'][] = '_lbeba96c78ce_content')) { function _lbeba96c78ce_content($_b, $_args) { foreach ($_args as $__k => $__v) $$__k = $__v
?><div class="services">
<h1>Aviable Services</H1>
<?php $iterations = 0; foreach ($services as $service) { ?>
	<table>
		<tr>
			<td>
				<div class="service">
					<table>
						<tr>
							<td>
								<div class="service_title">
									<?php echo Latte\Runtime\Filters::escapeHtml($service['title'], ENT_NOQUOTES) ?>

								</div>
<?php if ($service['status']==true) { ?>
									<div class="service_run">
									 run
									</div> 
<?php } else { ?>
									<div class="service_no_run">
									 no run
									</div> 
<?php } ?>
								<div class="service_preview">
<?php if ($service['status']==true) { ?>
										<a href="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($service['link']), ENT_COMPAT) ?>
"><image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>
/images/<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($service['preview']), ENT_COMPAT) ?>"></a>
<?php } else { ?>
										<image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>
/images/<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($service['preview']), ENT_COMPAT) ?>">
<?php } ?>
								</div>
								<div class="service_logo">
									<a href="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($service['homePage']), ENT_COMPAT) ?>
"><image src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>
/images/<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($service['logo']), ENT_COMPAT) ?>"></a>
								</div>
							</td>
<?php if ($user->loggedIn) { ?>
								<td valign="top">
									<div class="service_start_stop">
<?php if ($service['status']==true) { ?>
											<img src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/start-grey.png">
											<a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Servicies:disableServicie", array($service['id'])), ENT_COMPAT) ?>
"><img src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/stop.png"></a>
<?php } else { ?>
											<a href="<?php echo Latte\Runtime\Filters::escapeHtml($_control->link("Servicies:enableServicie", array($service['id'])), ENT_COMPAT) ?>
"><img src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/start.png"></a>
											<img src="<?php echo Latte\Runtime\Filters::escapeHtml(Latte\Runtime\Filters::safeUrl($basePath), ENT_COMPAT) ?>/images/stop-grey.png">
<?php } ?>
									</div>
								</td>
<?php } ?>
						</tr>
					</table>
				</div>
			</td>
			<td>
				<div class="service_description">
					<?php echo Latte\Runtime\Filters::escapeHtml($service['description'], ENT_NOQUOTES) ?>

				</div>
			</td>
		</tr>
	</table>
<?php $iterations++; } ?>
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
?>

<?php if ($_l->extends) { ob_end_clean(); return $template->renderChildTemplate($_l->extends, get_defined_vars()); }
call_user_func(reset($_b->blocks['content']), $_b, get_defined_vars()) ; 
}}